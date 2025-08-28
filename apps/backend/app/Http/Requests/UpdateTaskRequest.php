<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|in:todo,doing,done',
            'assignee' => 'sometimes|nullable|string',
            'due_date' => 'sometimes|nullable|date',
            'tags' => 'sometimes|array',
            'tags.*' => 'string',
        ];
    }
}
