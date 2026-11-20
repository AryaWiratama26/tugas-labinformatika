<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'score' => 'nullable|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:5000',
            'is_hidden' => 'sometimes|boolean',
        ];
    }
}
