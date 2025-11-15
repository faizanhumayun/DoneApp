<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkflowRequest extends FormRequest
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
                'max:255',
                Rule::unique('workflows', 'name')->where('company_id', $companyId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'statuses' => [
                'required',
                'array',
                'min:1',
            ],
            'statuses.*.name' => [
                'required',
                'string',
                'max:40',
                'distinct',
            ],
            'statuses.*.color' => [
                'required',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'statuses.*.is_active' => [
                'boolean',
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
            'name.required' => 'Please enter a workflow name.',
            'name.unique' => 'A workflow with this name already exists in your company.',
            'statuses.required' => 'You must include at least one status.',
            'statuses.min' => 'You must include at least one status.',
            'statuses.*.name.required' => 'Please enter a status name.',
            'statuses.*.name.max' => 'Status name cannot exceed 40 characters.',
            'statuses.*.name.distinct' => 'Status names must be unique.',
            'statuses.*.color.required' => 'Please select a color for each status.',
            'statuses.*.color.regex' => 'Invalid color format.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Ensure at least one active status exists
            $statuses = $this->input('statuses', []);
            $hasActiveStatus = collect($statuses)->contains(function ($status) {
                // Handle both boolean and string values (1, "1", true)
                $isActive = $status['is_active'] ?? true;
                return filter_var($isActive, FILTER_VALIDATE_BOOLEAN);
            });

            if (!$hasActiveStatus) {
                $validator->errors()->add(
                    'statuses',
                    'You must include at least one active status.'
                );
            }
        });
    }
}
