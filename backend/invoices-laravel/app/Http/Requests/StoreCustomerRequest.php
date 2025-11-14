<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // In this project, we allow all requests (no authentication yet)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120|min:3',
            'email' => ['required', 'email', Rule::unique('customers', 'email')->whereNull('deleted_at')],
            'phone' => 'nullable|string|max:30',
            'tax_id' => ['required', 'string', Rule::unique('customers', 'tax_id')->whereNull('deleted_at'), 'regex:/^[A-Z0-9\-]{5,20}$/'],
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'required|string|max:2',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name is required',
            'name.min' => 'The name must be at least 3 characters',
            'email.required' => 'The email is required',
            'email.email' => 'The email must be a valid email address',
            'email.unique' => 'This email is already registered',
            'tax_id.required' => 'The tax ID is required',
            'tax_id.unique' => 'This tax ID is already registered',
            'tax_id.regex' => 'The tax ID must have a valid format (e.g., 123456789-K or 900123456)',
            'country.required' => 'The country is required',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert strings to booleans if necessary
        if ($this->has('is_active') && is_string($this->is_active)) {
            $this->merge([
                'is_active' => in_array($this->is_active, ['true', '1', 'yes']),
            ]);
        }

        // Set default country if not provided
        if (!$this->has('country')) {
            $this->merge([
                'country' => 'US',
            ]);
        }
    }
}