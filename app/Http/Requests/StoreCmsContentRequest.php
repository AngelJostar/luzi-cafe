<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCmsContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('settings.manage') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('cms_contents', 'key')],
            'area' => ['required', 'string', 'max:80'],
            'type' => ['required', Rule::in(['text', 'image', 'color'])],
            'label' => ['required', 'string', 'max:120'],
            'value' => ['nullable', 'string', 'max:5000', 'required_unless:type,image'],
            'image' => ['nullable', 'image', 'max:5120', 'required_if:type,image'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'published', 'scheduled'])],
            'scheduled_at' => ['nullable', 'date', 'required_if:status,scheduled'],
        ];
    }
}
