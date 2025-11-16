<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $task = $this->route('task');
        $project = $task->project;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:50000',
            ],
            'priority' => [
                'required',
                Rule::in(['low', 'medium', 'high', 'critical']),
            ],
            'workflow_status_id' => [
                'required',
                'integer',
                Rule::exists('workflow_statuses', 'id')
                    ->where('workflow_id', $project->workflow_id),
            ],
            'assignee_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'due_date' => [
                'nullable',
                'date',
            ],
            'tags' => [
                'nullable',
                'array',
                'max:10',
            ],
            'tags.*' => [
                'nullable',
                'string',
                'max:20',
            ],
            'watchers' => [
                'nullable',
                'array',
            ],
            'watchers.*' => [
                'integer',
                Rule::exists('users', 'id'),
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'title.max' => 'Task title cannot exceed 255 characters.',
            'workflow_status_id.required' => 'Please select a workflow status.',
            'workflow_status_id.exists' => 'The selected workflow status is invalid.',
            'assignee_id.exists' => 'The selected assignee is not a valid user.',
            'tags.max' => 'You cannot add more than 10 tags to a task.',
            'tags.*.max' => 'Each tag cannot exceed 20 characters.',
        ];
    }
}
