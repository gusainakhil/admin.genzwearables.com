<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->withCount('orders')
            ->latest()
            ->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $customer->load('orders.items', 'addresses');

        return view('admin.customers.show', compact('customer'));
    }

    public function updateStatus(Request $request, User $customer)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $customer->update($validated);

        return back()->with('success', 'Customer status updated successfully');
    }
}
