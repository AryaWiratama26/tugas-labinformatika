<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Subject;
use App\Models\LabClass;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    private const SORTS = [
        'latest' => 'Terbaru',
        'oldest' => 'Terlama',
        'name' => 'Nama A-Z',
        'nim' => 'NIM kecil-besar',
        'score_desc' => 'Nilai tertinggi',
        'score_asc' => 'Nilai terendah',
    ];

    private const PER_PAGES = [10, 20, 50];

    public function index(Request $request)
    {
        $query = Submission::with(['subject', 'module', 'labClass']);
        $this->applyFilters($query, $request);
        $this->applySort($query, $request->input('sort', 'latest'));

        $perPage = (int) $request->input('per_page', 20);
        if (! in_array($perPage, self::PER_PAGES, true)) {
            $perPage = 20;
        }

        $submissions = $query->paginate($perPage)->withQueryString();
        $subjects = Subject::orderBy('name')->get();
        $modules = Module::with('subject')->orderBy('name')->get();
        $classes = LabClass::orderBy('name')->get();
        $hiddenCount = Submission::hidden()->count();

        return view('admin.submissions.index', compact(
            'submissions', 'subjects', 'modules', 'classes', 'hiddenCount'
        ));
    }

    public function edit(Submission $submission)
    {
        $submission->load(['subject', 'module', 'labClass']);
        return view('admin.submissions.edit', compact('submission'));
    }

    public function update(Request $request, Submission $submission)
    {
        $request->validate([
            'score' => 'nullable|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:5000',
        ]);

        $submission->forceFill([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'is_hidden' => $request->boolean('is_hidden'),
            'graded_at' => $request->filled('score') || $request->filled('feedback') ? now() : null,
            'graded_by' => auth()->user()->name ?? 'admin',
        ])->save();

        return redirect()->route('admin.submissions.index')
            ->with('success', "Nilai untuk {$submission->student_name} berhasil disimpan.");
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:hide,unhide,delete,grade',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:submissions,id',
            'score' => 'required_if:action,grade|nullable|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:5000',
        ]);

        $ids = $request->input('ids');
        $count = count($ids);

        match ($request->input('action')) {
            'hide' => Submission::whereIn('id', $ids)->update(['is_hidden' => true]),
            'unhide' => Submission::whereIn('id', $ids)->update(['is_hidden' => false]),
            'grade' => Submission::whereIn('id', $ids)->update([
                'score' => $request->input('score'),
                'feedback' => $request->input('feedback'),
                'graded_at' => now(),
                'graded_by' => auth()->user()->name ?? 'admin',
            ]),
            'delete' => $this->bulkDelete($ids),
        };

        $messages = [
            'hide' => "{$count} tugas disembunyikan dari daftar.",
            'unhide' => "{$count} tugas ditampilkan kembali.",
            'grade' => "Nilai {$count} tugas berhasil disimpan.",
            'delete' => "{$count} tugas dihapus permanen.",
        ];

        return redirect()->route('admin.submissions.index')
            ->with('success', $messages[$request->input('action')]);
    }

    public function export(Request $request)
    {
        $query = Submission::with(['subject', 'module', 'labClass']);

        if ($request->filled('ids')) {
            $ids = is_array($request->input('ids')) ? $request->input('ids') : explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        } else {
            $this->applyFilters($query, $request);
        }
        $this->applySort($query, $request->input('sort', 'latest'));

        $submissions = $query->get();
        $filename = 'rekap_tugas_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($submissions) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['No', 'NIM', 'Nama Mahasiswa', 'Mata Kuliah', 'Modul', 'Kelas', 'Nilai', 'Feedback', 'Status', 'Nama File', 'Waktu Upload']);

            foreach ($submissions as $i => $sub) {
                fputcsv($file, [
                    $i + 1,
                    $sub->student_nim,
                    $sub->student_name,
                    $sub->subject->name ?? '-',
                    $sub->module->name ?? '-',
                    $sub->labClass->name ?? '-',
                    $sub->score ?? '-',
                    $sub->feedback ?? '-',
                    $sub->is_hidden ? 'Tersembunyi' : 'Ditampilkan',
                    $sub->file_name,
                    $sub->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(Submission $submission)
    {
        $this->deleteFiles($submission);
        $submission->delete();

        return redirect()->route('admin.submissions.index')
            ->with('success', 'Data pengumpulan berhasil dihapus.');
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('student_name', 'like', '%'.$request->search.'%')
                    ->orWhere('student_nim', 'like', '%'.$request->search.'%');
            });
        }

        foreach (['subject_id', 'module_id', 'lab_class_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->input('graded') === '1') {
            $query->whereNotNull('score');
        } elseif ($request->input('graded') === '0') {
            $query->whereNull('score');
        }

        match ($request->input('visibility', 'visible')) {
            'hidden' => $query->hidden(),
            'all' => null,
            default => $query->visible(),
        };
    }

    private function applySort($query, string $sort): void
    {
        match ($sort) {
            'oldest' => $query->oldest(),
            'name' => $query->orderBy('student_name')->orderBy('id'),
            'nim' => $query->orderBy('student_nim')->orderBy('id'),
            'score_desc' => $query->orderByRaw('score IS NULL, score DESC')->orderBy('id'),
            'score_asc' => $query->orderByRaw('score IS NULL, score ASC')->orderBy('id'),
            default => $query->latest(),
        };
    }

    private function bulkDelete(array $ids): void
    {
        Submission::whereIn('id', $ids)->get()->each(function (Submission $submission) {
            $this->deleteFiles($submission);
            $submission->delete();
        });
    }

    private function deleteFiles(Submission $submission): void
    {
        try {
            if ($submission->file_path) {
                Storage::disk('google')->delete($submission->file_path);
            }
        } catch (\Exception $e) {
            // best-effort, tetap hapus DB
        }
    }

    public static function sorts(): array
    {
        return self::SORTS;
    }

    public static function perPages(): array
    {
        return self::PER_PAGES;
    }
}
