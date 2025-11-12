<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    // List all invoices with customer and items
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => Invoice::with(['customer', 'items'])->get(),
            'count' => Invoice::count(),
        ]);
    }

    // Create an invoice with validated data and items
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = Invoice::create($request->validated());

        // Process items if provided
        if ($request->has('items') && is_array($request->items)) {
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

        // Calculate totals
        $invoice->calculateTotals()->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice created successfully',
            'data' => $invoice->load(['customer', 'items']),
        ], 201);
    }

    // Show a specific invoice by ID
    public function show($id): JsonResponse
    {
        $invoice = Invoice::with(['customer', 'items'])->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $invoice,
        ]);
    }

    // Update an invoice with validated data
    public function update(UpdateInvoiceRequest $request, $id): JsonResponse
    {
        $invoice = Invoice::with(['items'])->findOrFail($id);
        $invoice->update($request->validated());

        // Update items if provided
        if ($request->has('items') && is_array($request->items)) {
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

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice updated successfully',
            'data' => $invoice->load(['customer', 'items']),
        ]);
    }

    // Delete an invoice (soft delete)
    public function destroy($id): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Invoice deleted successfully',
        ]);
    }
}
