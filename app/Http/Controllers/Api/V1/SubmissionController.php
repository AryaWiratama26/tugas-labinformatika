<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateGradeRequest;
use App\Http\Resources\V1\SubmissionResource;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Submission::with(['subject', 'module', 'labClass'])->latest();

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
        if ($request->filled('graded')) {
            $request->boolean('graded')
                ? $query->whereNotNull('score')
                : $query->whereNull('score');
        }

        match ($request->input('visibility', 'visible')) {
            'hidden' => $query->hidden(),
            'all' => null,
            default => $query->visible(),
        };

        return SubmissionResource::collection($query->paginate(20)->withQueryString());
    }

    public function show(Submission $submission)
    {
        $submission->load(['subject', 'module', 'labClass']);
        return new SubmissionResource($submission);
    }

    public function update(UpdateGradeRequest $request, Submission $submission)
    {
        $data = $request->validated();
        $submission->forceFill([
            'score' => $data['score'] ?? null,
            'feedback' => $data['feedback'] ?? null,
            'is_hidden' => $data['is_hidden'] ?? $submission->is_hidden,
            'graded_at' => array_key_exists('score', $data) || array_key_exists('feedback', $data) ? now() : $submission->graded_at,
            'graded_by' => $request->attributes->get('api_key')?->name ?? $submission->graded_by,
        ])->save();

        $submission->load(['subject', 'module', 'labClass']);
        return new SubmissionResource($submission);
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

        match ($request->input('action')) {
            'hide' => Submission::whereIn('id', $ids)->update(['is_hidden' => true]),
            'unhide' => Submission::whereIn('id', $ids)->update(['is_hidden' => false]),
            'grade' => Submission::whereIn('id', $ids)->update([
                'score' => $request->input('score'),
                'feedback' => $request->input('feedback'),
                'graded_at' => now(),
                'graded_by' => $request->attributes->get('api_key')?->name,
            ]),
            'delete' => $this->bulkDelete($ids),
        };

        return response()->json([
            'message' => 'Aksi bulk selesai.',
            'action' => $request->input('action'),
            'affected' => count($ids),
        ]);
    }

    public function destroy(Submission $submission)
    {
        $this->deleteFiles($submission);
        $submission->delete();
        return response()->json(['message' => 'Submission dihapus.']);
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
}
