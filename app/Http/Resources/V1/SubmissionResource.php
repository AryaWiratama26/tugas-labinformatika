<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_name' => $this->student_name,
            'student_nim' => $this->student_nim,
            'subject_id' => $this->subject_id,
            'module_id' => $this->module_id,
            'lab_class_id' => $this->lab_class_id,
            'file_name' => $this->file_name,
            'file_size' => $this->file_size,
            'drive_url' => $this->google_drive_id,
            'score' => $this->score !== null ? (float) $this->score : null,
            'feedback' => $this->feedback,
            'is_hidden' => (bool) $this->is_hidden,
            'graded_at' => $this->graded_at?->toIso8601String(),
            'graded_by' => $this->graded_by,
            'submitted_at' => $this->created_at?->toIso8601String(),
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'module' => new ModuleResource($this->whenLoaded('module')),
            'lab_class' => new LabClassResource($this->whenLoaded('labClass')),
        ];
    }
}
