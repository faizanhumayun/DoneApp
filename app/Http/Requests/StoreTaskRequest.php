<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
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
        return [
            'project_id' => [
                'required',
                'integer',
                Rule::exists('projects', 'id'),
            ],
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
                'exists:workflow_statuses,id',
            ],
            'assignee_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'due_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
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
            'workflow_status_id.exists' => 'The selected workflow status is invalid or inactive.',
            'assignee_id.exists' => 'The selected assignee is not a valid user.',
            'due_date.after_or_equal' => 'Due date cannot be in the past.',
            'tags.max' => 'You cannot add more than 10 tags to a task.',
            'tags.*.max' => 'Each tag cannot exceed 20 characters.',
        ];
    }
}
