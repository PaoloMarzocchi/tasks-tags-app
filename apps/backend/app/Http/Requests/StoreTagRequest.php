<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'slug' => 'required|string|unique:tags,slug',
            'color' => 'nullable|string',
            'parent_slug' => 'nullable|string',
        ];
    }
}
