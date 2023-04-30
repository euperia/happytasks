<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'status_id' => ['nullable', 'exists:statuses,id'],
            'name' => ['required'],
            'url' => ['nullable'],
            'description' => ['nullable'],
            'notes' => ['nullable'],
            'duration' => ['nullable', 'integer'],
            'due_at' => ['nullable', 'date'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
