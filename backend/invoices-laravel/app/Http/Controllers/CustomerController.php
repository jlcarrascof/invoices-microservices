<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    // List all customers
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => Customer::all(),
            'count' => Customer::count(),
        ]);
    }

    // Create a customer with validated data
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Customer created successfully',
            'data' => $customer,
        ], 201);
    }

    // Show a specific customer by ID
    public function show($id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $customer,
        ]);
    }

    // Update a customer with validated data
    public function update(UpdateCustomerRequest $request, $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $customer->update($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Customer updated successfully',
            'data' => $customer,
        ]);
    }

    // Delete a customer (soft delete)
    public function destroy($id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Customer deleted successfully',
        ]);
    }
}
