<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceItemRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:255|min:3',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0.01',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'description.required' => 'The description of the item is required',
            'description.min' => 'The description must be at least 3 characters',
            'description.max' => 'The description may not be greater than 255 characters',
            'quantity.required' => 'The quantity is required',
            'quantity.integer' => 'The quantity must be an integer',
            'quantity.min' => 'The quantity must be greater than 0',
            'unit_price.required' => 'The unit price is required',
            'unit_price.numeric' => 'The unit price must be a valid number',
            'unit_price.min' => 'The unit price must be greater than 0',
        ];
    }
}
