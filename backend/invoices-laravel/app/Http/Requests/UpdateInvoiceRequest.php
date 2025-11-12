<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
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
        $invoiceId = $this->route('invoice');

        return [
            'invoice_number' => "required|string|unique:invoices,invoice_number,{$invoiceId}|regex:/^INV-\d{4}-\d{6}$/",
            'issue_date' => 'required|date|date_format:Y-m-d',
            'due_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:issue_date',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'currency' => 'required|string|size:3|uppercase',
            'status' => 'nullable|in:draft,sent,paid,cancelled',
            'notes' => 'nullable|string|max:1000',
            'items' => 'nullable|array|min:1',
            'items.*.description' => 'required|string|max:255|min:3',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0.01',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'invoice_number.required' => 'The invoice number is required',
            'invoice_number.unique' => 'This invoice number already exists in another invoice',
            'invoice_number.regex' => 'The number must have the format INV-YYYY-XXXXXX',
            'issue_date.required' => 'The issue date is required',
            'issue_date.date_format' => 'The date must be in the format YYYY-MM-DD',
            'due_date.after_or_equal' => 'The due date cannot be earlier than the issue date',
            'tax_rate.required' => 'The tax rate is required',
            'currency.required' => 'The currency is required',
            'status.in' => 'The status must be one of: draft, sent, paid, or cancelled',
            'items.min' => 'The invoice must have at least one item',
            'items.*.description.required' => 'The description of each item is required',
            'items.*.quantity.required' => 'The quantity of each item is required',
            'items.*.unit_price.required' => 'The unit price of each item is required',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('currency')) {
            $this->merge([
                'currency' => strtoupper($this->currency),
            ]);
        }
    }
}
