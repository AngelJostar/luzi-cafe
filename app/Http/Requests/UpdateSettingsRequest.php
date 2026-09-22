<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
            'business_name' => ['required', 'string', 'max:120'],
            'business_email' => ['nullable', 'email', 'max:255'],
            'business_phone' => ['nullable', 'string', 'max:30'],
            'business_address' => ['nullable', 'string', 'max:500'],
            'currency' => ['required', 'string', 'size:3'],
            'timezone' => ['required', 'timezone'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'logo' => ['nullable', 'string', 'max:2048'],
            'max_prep_minutes' => ['required', 'integer', 'min:1', 'max:240'],
            'healthy_margin_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'low_margin_percent' => ['required', 'numeric', 'min:0', 'max:100', 'lte:healthy_margin_percent'],
            'tips_enabled' => ['required', 'boolean'],
            'scheduled_orders_enabled' => ['required', 'boolean'],
            'maintenance_mode' => ['required', 'boolean'],
            'allow_negative_stock' => ['required', 'boolean'],
        ];
    }
}
