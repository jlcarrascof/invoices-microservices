<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvoiceItemController extends Controller
{
    // List all items from a specific invoice
    public function index($invoiceId): JsonResponse
    {
        $invoice = Invoice::findOrFail($invoiceId);
        return response()->json($invoice->items);
    }

    // Create a new item for a specific invoice
    public function store(Request $request, $invoiceId): JsonResponse
    {
        $invoice = Invoice::findOrFail($invoiceId);

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $validated['line_total'] = $validated['quantity'] * $validated['unit_price'];

        $item = $invoice->items()->create($validated);

        // Recalculate invoice totals
        $invoice->calculateTotals()->save();

        return response()->json($item, 201);
    }

    // Show a specific item
    public function show($invoiceId, $itemId): JsonResponse
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = $invoice->items()->findOrFail($itemId);
        return response()->json($item);
    }

    // Update a specific item
    public function update(Request $request, $invoiceId, $itemId): JsonResponse
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = $invoice->items()->findOrFail($itemId);

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $validated['line_total'] = $validated['quantity'] * $validated['unit_price'];

        $item->update($validated);

        // Recalculate invoice totals
        $invoice->calculateTotals()->save();

        return response()->json($item);
    }

    // Delete a specific item
    public function destroy($invoiceId, $itemId): JsonResponse
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = $invoice->items()->findOrFail($itemId);
        
        $item->delete();

        // Recalculate invoice totals after deleting item
        $invoice->calculateTotals()->save();

        return response()->json(['message' => 'Item deleted successfully']);
    }
}
