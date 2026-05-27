<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'task_id' => ['nullable', 'exists:tasks,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'work_date' => ['required', 'date'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after:started_at'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'summary' => ['required', 'string'],
            'billable' => ['sometimes', 'boolean'],
        ];
    }
}
