<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject_id' => $this->subject_id,
            'name' => $this->name,
            'description' => $this->description,
            'deadline' => $this->deadline?->toIso8601String(),
            'is_expired' => (bool) $this->is_expired,
            'is_hidden' => (bool) $this->is_hidden,
            'image_url' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
