<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'nullable|in:todo,doing,done',
            'assignee' => 'nullable|string',
            'due_date' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
        ];
    }
}
