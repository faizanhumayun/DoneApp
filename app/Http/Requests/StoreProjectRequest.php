<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
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
        $companyId = auth()->user()->companies->first()?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('projects', 'name')->where('company_id', $companyId),
            ],
            'workflow_id' => [
                'required',
                'integer',
                Rule::exists('workflows', 'id')->where('company_id', $companyId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'estimated_cost' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
            'billable_resource' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
            'non_billable_resource' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
            'total_estimated_hours' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'members' => [
                'nullable',
                'array',
            ],
            'members.*.user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'members.*.role' => [
                'required',
                'string',
                Rule::in(['owner', 'admin', 'member', 'guest']),
            ],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a project name.',
            'name.max' => 'Project name cannot exceed 100 characters.',
            'name.unique' => 'A project with this name already exists in your company.',
            'workflow_id.required' => 'Please select a workflow for this project.',
            'workflow_id.exists' => 'The selected workflow is not valid.',
            'estimated_cost.numeric' => 'Estimated cost must be a valid number.',
            'estimated_cost.min' => 'Estimated cost cannot be negative.',
            'billable_resource.numeric' => 'Billable resource must be a valid number.',
            'billable_resource.min' => 'Billable resource cannot be negative.',
            'non_billable_resource.numeric' => 'Non-billable resource must be a valid number.',
            'non_billable_resource.min' => 'Non-billable resource cannot be negative.',
            'total_estimated_hours.integer' => 'Total estimated hours must be a whole number.',
            'total_estimated_hours.min' => 'Total estimated hours cannot be negative.',
            'members.*.user_id.required' => 'Please select a member.',
            'members.*.user_id.exists' => 'The selected member is not valid.',
            'members.*.user_id.distinct' => 'Each member can only be assigned once to a project.',
            'members.*.role.required' => 'Please select a role for each member.',
            'members.*.role.in' => 'The selected role is not valid.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('members')) {
                $members = $this->input('members');
                $userIds = array_filter(array_column($members, 'user_id'));

                // Check for duplicate user IDs
                if (count($userIds) !== count(array_unique($userIds))) {
                    $validator->errors()->add(
                        'members',
                        'Each member can only be assigned one role per project. Please remove duplicate member selections.'
                    );
                }
            }
        });
    }
}
