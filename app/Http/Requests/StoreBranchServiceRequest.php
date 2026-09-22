<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBranchServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('branches.manage') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:80'],
            'provider' => ['nullable', 'string', 'max:120'],
            'account' => ['nullable', 'string', 'max:120'],
            'service_number' => ['nullable', 'string', 'max:80'],
            'amount' => ['required', 'numeric', 'min:0'],
            'frequency' => ['required', 'string', 'max:30'],
            'billing_day' => ['nullable', 'integer', 'between:1,31'],
            'due_day' => ['nullable', 'integer', 'between:1,31'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'contact_name' => ['nullable', 'string', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
