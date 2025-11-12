<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    // List all invoices
    public function index(): JsonResponse
    {
        return response()->json(Invoice::with(['customer', 'items'])->get());
    }

    // Create an invoice
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'tax_rate' => 'numeric|min:0',
            'currency' => 'required|string|max:6',
            'status' => 'nullable|in:draft,sent,paid,cancelled',
            'notes' => 'nullable|string'
        ]);

        $invoice = Invoice::create($validated);

        // Process items if they come in the request
        if($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $item) {
                $invoiceItem = new InvoiceItem([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['quantity'] * $item['unit_price'],
                ]);
                $invoice->items()->save($invoiceItem);
            }
        }

        // Recalculate totals
        $invoice->calculateTotals()->save();

        return response()->json($invoice->load(['customer', 'items']), 201);
    }

    // Show an invoice by ID
    public function show($id): JsonResponse
    {
        $invoice = Invoice::with(['customer', 'items'])->findOrFail($id);
        return response()->json($invoice);
    }

    // Update an invoice
    public function update(Request $request, $id): JsonResponse
    {
        $invoice = Invoice::with(['items'])->findOrFail($id);
        $validated = $request->validate([
            'invoice_number' => "required|string|unique:invoices,invoice_number,{$id}",
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'tax_rate' => 'numeric|min:0',
            'currency' => 'required|string|max:6',
            'status' => 'nullable|in:draft,sent,paid,cancelled',
            'notes' => 'nullable|string'
        ]);
        $invoice->update($validated);

        // Update items if they come in the request
        if($request->has('items') && is_array($request->items)) {
            $invoice->items()->delete(); // Delete old items
            foreach ($request->items as $item) {
                $invoiceItem = new InvoiceItem([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['quantity'] * $item['unit_price'],
                ]);
                $invoice->items()->save($invoiceItem);
            }
        }

        // Recalculate totals
        $invoice->calculateTotals()->save();

        return response()->json($invoice->load(['customer', 'items']));
    }

    // Delete an invoice (soft delete)
    public function destroy($id): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted']);
    }
}
