<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories'],
            'status_id' => ['nullable', 'exists:statuses'],
            'name' => ['nullable'],
            'url' => ['nullable'],
            'description' => ['nullable'],
            'notes' => ['nullable'],
            'duration' => ['nullable', 'integer'],
            'due_at' => ['nullable', 'date'],
        ];
    }

    public function authorize(): bool
    {
        $task = Task::find($this->route('task'))->first();
        return auth()->user()->id === $task->user_id;
    }
}
