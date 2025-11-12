<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    // List all customers
    public function index(): JsonResponse
    {
        return response()->json(Customer::all());
    }

    // Create a customer
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:30',
            'tax_id' => 'required|string|unique:customers,tax_id',
        ]);
        $customer = Customer::create($validated);
        return response()->json($customer, 201);
    }

    // Show a specific customer by ID
    public function show($id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    // Update a customer
    public function update(Request $request, $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => "required|email|unique:customers,email,{$id}",
            'phone' => 'nullable|string|max:30',
            'tax_id' => "required|string|unique:customers,tax_id,{$id}",
        ]);
        $customer->update($validated);
        return response()->json($customer);
    }

    // Delete a customer (soft delete)
    public function destroy($id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return response()->json(['message' => 'Customer deleted']);
    }
}
