<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';
        return [
            'name' => $required.'|string|max:255',
            'code' => 'nullable|string|max:50',
            'is_hidden' => 'sometimes|boolean',
        ];
    }
}
