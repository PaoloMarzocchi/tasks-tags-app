<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tagId = $this->route('tag');

        return [
            'name' => 'sometimes|string',
            'slug' => [
                'sometimes',
                'string',
                Rule::unique('tags', 'slug')->ignore($tagId, 'id')
            ],
            'color' => 'sometimes|nullable|string',
            'parent_slug' => 'sometimes|nullable|string',
        ];
    }
}
