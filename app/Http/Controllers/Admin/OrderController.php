<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetail;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user', 'items');

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'address', 'items.product', 'items.variant.size', 'items.variant.color', 'payment', 'shipment');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:placed,packed,shipped,delivered,cancelled,returned',
        ]);

        $order->update($validated);

        return back()->with('success', 'Order status updated successfully');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $order->update($validated);

        return back()->with('success', 'Payment status updated successfully');
    }

    public function addShipment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'courier_name' => 'required|string|max:100',
            'tracking_number' => 'required|string|max:100',
            'shipped_date' => 'required|date',
        ]);

        $validated['order_id'] = $order->id;

        Shipment::updateOrCreate(
            ['order_id' => $order->id],
            $validated
        );

        $order->update(['order_status' => 'shipped']);

        return back()->with('success', 'Shipment details added successfully');
    }

    public function printInvoice(Order $order)
    {
        $order->load('user', 'address', 'items.product', 'items.variant.size', 'items.variant.color', 'payment', 'shipment');
        $companyDetail = CompanyDetail::query()->first();

        return view('admin.orders.print-invoice', compact('order', 'companyDetail'));
    }

    public function printParcelSheet(Order $order)
    {
        $order->load('user', 'address', 'items.product', 'items.variant.size', 'items.variant.color', 'payment', 'shipment');
        $companyDetail = CompanyDetail::query()->first();

        $scanPayload = json_encode([
            'order_number' => $order->order_number,
            'tracking_number' => $order->shipment?->tracking_number,
            'customer' => $order->user->name,
            'total' => (float) $order->total,
        ]);

        return view('admin.orders.print-parcel-sheet', compact('order', 'scanPayload', 'companyDetail'));
    }
}
