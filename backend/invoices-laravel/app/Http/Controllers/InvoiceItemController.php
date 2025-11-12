<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Http\Requests\StoreInvoiceItemRequest;
use App\Http\Requests\UpdateInvoiceItemRequest;
use Illuminate\Http\JsonResponse;

class InvoiceItemController extends Controller
{
    // List all items from a specific invoice
    public function index($invoiceId): JsonResponse
    {
        $invoice = Invoice::findOrFail($invoiceId);
        return response()->json([
            'status' => 'success',
            'data' => $invoice->items,
            'count' => $invoice->items->count(),
        ]);
    }

    // Create a new item for a specific invoice
    public function store(StoreInvoiceItemRequest $request, $invoiceId): JsonResponse
    {
        $invoice = Invoice::findOrFail($invoiceId);

        $validated = $request->validated();
        $validated['line_total'] = $validated['quantity'] * $validated['unit_price'];

        $item = $invoice->items()->create($validated);

        // Recalculate invoice totals
        $invoice->calculateTotals()->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Item added to the invoice successfully',
            'data' => $item,
        ], 201);
    }

    // Show a specific item
    public function show($invoiceId, $itemId): JsonResponse
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = $invoice->items()->findOrFail($itemId);
        return response()->json([
            'status' => 'success',
            'data' => $item,
        ]);
    }

    // Update a specific item
    public function update(UpdateInvoiceItemRequest $request, $invoiceId, $itemId): JsonResponse
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = $invoice->items()->findOrFail($itemId);

        $validated = $request->validated();
        $validated['line_total'] = $validated['quantity'] * $validated['unit_price'];

        $item->update($validated);

        // Recalculate invoice totals
        $invoice->calculateTotals()->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Item updated successfully',
            'data' => $item,
        ]);
    }

    // Delete a specific item
    public function destroy($invoiceId, $itemId): JsonResponse
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = $invoice->items()->findOrFail($itemId);
        
        $item->delete();

        // Recalculate invoice totals after deleting item
        $invoice->calculateTotals()->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Item deleted from the invoice successfully',
        ]);
    }
}
