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
    public function index(Request $request)
    {
        $query = Submission::with(['subject', 'module', 'labClass'])->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('student_name', 'like', '%' . $request->search . '%')
                  ->orWhere('student_nim', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('module_id')) {
            $query->where('module_id', $request->module_id);
        }

        if ($request->filled('lab_class_id')) {
            $query->where('lab_class_id', $request->lab_class_id);
        }

        $submissions = $query->paginate(20)->withQueryString();
        $subjects = Subject::all();
        $modules = Module::with('subject')->get();
        $classes = LabClass::all();

        return view('admin.submissions.index', compact('submissions', 'subjects', 'modules', 'classes'));
    }

    public function export(Request $request)
    {
        $query = Submission::with(['subject', 'module', 'labClass'])->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('student_name', 'like', '%' . $request->search . '%')
                  ->orWhere('student_nim', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('module_id')) {
            $query->where('module_id', $request->module_id);
        }
        if ($request->filled('lab_class_id')) {
            $query->where('lab_class_id', $request->lab_class_id);
        }

        $submissions = $query->get();

        $filename = 'rekap_tugas_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($submissions) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['No', 'NIM', 'Nama Mahasiswa', 'Mata Kuliah', 'Modul', 'Kelas', 'Nama File', 'Waktu Upload']);

            foreach ($submissions as $i => $sub) {
                fputcsv($file, [
                    $i + 1,
                    $sub->student_nim,
                    $sub->student_name,
                    $sub->subject->name ?? '-',
                    $sub->module->name ?? '-',
                    $sub->labClass->name ?? '-',
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
        try {
            // Hapus file dari Google Drive jika ada
            if ($submission->file_path) {
                Storage::disk('google')->delete($submission->file_path);
            }
        } catch (\Exception $e) {
            // Tetap hapus dari DB meskipun Drive gagal
        }

        $submission->delete();

        return redirect()->route('admin.submissions.index')
            ->with('success', 'Data pengumpulan berhasil dihapus.');
    }
}
