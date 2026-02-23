@extends('admin.layout')

@section('title', 'Order Details')
@section('page-title', 'Order Details')

@section('content')
<div class="w-full space-y-6">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-rose-600 transition hover:text-rose-700">
            <i class="fas fa-arrow-left"></i>Back to Orders
        </a>
    </div>

    <!-- Order Info -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
            <h3 class="text-lg font-semibold text-stone-900 mb-4">Order Information</h3>
            <dl class="space-y-2">
                <div>
                    <dt class="text-sm text-stone-500">Order Number</dt>
                    <dd class="font-medium text-stone-900">{{ $order->order_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-stone-500">Order Date</dt>
                    <dd class="text-stone-700">{{ $order->created_at->format('M d, Y h:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-stone-500">Order Status</dt>
                    <dd>
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($order->order_status == 'delivered') bg-emerald-100 text-emerald-800
                            @elseif($order->order_status == 'cancelled') bg-rose-100 text-rose-800
                            @else bg-amber-100 text-amber-800
                            @endif">
                            {{ ucfirst($order->order_status) }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
            <h3 class="text-lg font-semibold text-stone-900 mb-4">Customer Information</h3>
            <dl class="space-y-2">
                <div>
                    <dt class="text-sm text-stone-500">Name</dt>
                    <dd class="font-medium text-stone-900">{{ $order->user->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-stone-500">Email</dt>
                    <dd class="text-stone-700">{{ $order->user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-stone-500">Phone</dt>
                    <dd class="text-stone-700">{{ $order->user->phone ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
            <h3 class="text-lg font-semibold text-stone-900 mb-4">Payment Information</h3>
            <dl class="space-y-2">
                <div>
                    <dt class="text-sm text-stone-500">Payment Status</dt>
                    <dd>
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($order->payment_status == 'paid') bg-emerald-100 text-emerald-800
                            @elseif($order->payment_status == 'failed') bg-rose-100 text-rose-800
                            @else bg-amber-100 text-amber-800
                            @endif">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </dd>
                </div>
                @if($order->payment)
                <div>
                    <dt class="text-sm text-stone-500">Payment Method</dt>
                    <dd class="text-stone-700">{{ $order->payment->payment_method }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-stone-500">Transaction ID</dt>
                    <dd class="text-stone-700">{{ $order->payment->transaction_id ?? 'N/A' }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Shipping Address -->
    @php
        $shippingAddress = $order->address_snapshot ?? null;

        if (! $shippingAddress && $order->address) {
            $shippingAddress = [
                'name' => $order->address->name,
                'phone' => $order->address->phone,
                'address' => $order->address->address,
                'city' => $order->address->city,
                'state' => $order->address->state,
                'pincode' => $order->address->pincode,
                'country' => $order->address->country,
            ];
        }
    @endphp
    @if($shippingAddress)
    <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
        <h3 class="text-lg font-semibold text-stone-900 mb-4">Shipping Address</h3>
        <p class="text-stone-700">
            {{ $shippingAddress['name'] ?? 'N/A' }}<br>
            {{ $shippingAddress['address'] ?? 'N/A' }}<br>
            {{ $shippingAddress['city'] ?? 'N/A' }}, {{ $shippingAddress['state'] ?? 'N/A' }} {{ $shippingAddress['pincode'] ?? '' }}<br>
            {{ $shippingAddress['country'] ?? 'N/A' }}<br>
            Phone: {{ $shippingAddress['phone'] ?? 'N/A' }}
        </p>
    </div>
    @endif

    <!-- Order Items -->
    <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
        <h3 class="text-lg font-semibold text-stone-900 mb-4">Order Items</h3>
        <table class="w-full">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Product</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Size</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Color</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Quantity</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-100/70">
                @foreach($order->items as $item)
                <tr>
                    <td class="px-4 py-3 font-semibold text-stone-900">{{ $item->product->name }}</td>
                    <td class="px-4 py-3 text-stone-600">{{ $item->variant->size->name }}</td>
                    <td class="px-4 py-3 text-stone-600">{{ $item->variant->color->name }}</td>
                    <td class="px-4 py-3 text-stone-700">₹{{ number_format($item->price, 2) }}</td>
                    <td class="px-4 py-3 text-stone-700">{{ $item->quantity }}</td>
                    <td class="px-4 py-3 font-semibold text-stone-900">₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
                <tr class="font-medium">
                    <td colspan="5" class="px-4 py-3 text-right text-stone-600">Subtotal:</td>
                    <td class="px-4 py-3 text-stone-900">₹{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->shipping)
                <tr>
                    <td colspan="5" class="px-4 py-3 text-right text-stone-600">Shipping:</td>
                    <td class="px-4 py-3 text-stone-900">₹{{ number_format($order->shipping, 2) }}</td>
                </tr>
                @endif
                @if($order->discount)
                <tr>
                    <td colspan="5" class="px-4 py-3 text-right text-stone-600">Discount:</td>
                    <td class="px-4 py-3 text-rose-600">-₹{{ number_format($order->discount, 2) }}</td>
                </tr>
                @endif
                <tr class="font-bold text-lg">
                    <td colspan="5" class="px-4 py-3 text-right text-stone-600">Total:</td>
                    <td class="px-4 py-3 text-stone-900">₹{{ number_format($order->total, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Order Actions -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
            <h3 class="text-lg font-semibold text-stone-900 mb-4">Update Order Status</h3>
            <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="flex flex-wrap gap-3">
                    <select name="order_status" class="flex-1 rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                        <option value="placed" {{ $order->order_status == 'placed' ? 'selected' : '' }}>Placed</option>
                        <option value="packed" {{ $order->order_status == 'packed' ? 'selected' : '' }}>Packed</option>
                        <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="returned" {{ $order->order_status == 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                    <button type="submit" class="inline-flex items-center rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                        Update
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
            <h3 class="text-lg font-semibold text-stone-900 mb-4">Update Payment Status</h3>
            <form action="{{ route('admin.orders.payment-status', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="flex flex-wrap gap-3">
                    <select name="payment_status" class="flex-1 rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                        <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                    <button type="submit" class="inline-flex items-center rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Shipment Details -->
    <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
        <h3 class="text-lg font-semibold text-stone-900 mb-4">Shipment Details</h3>
        
        @if($order->shipment)
            <dl class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <dt class="text-sm text-stone-500">Courier Name</dt>
                    <dd class="font-medium text-stone-900">{{ $order->shipment->courier_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-stone-500">Tracking Number</dt>
                    <dd class="font-medium text-stone-900">{{ $order->shipment->tracking_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-stone-500">Shipped Date</dt>
                    <dd class="text-stone-700">{{ $order->shipment->shipped_date ? $order->shipment->shipped_date->format('M d, Y') : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-stone-500">Delivery Date</dt>
                    <dd class="text-stone-700">{{ $order->shipment->delivery_date ? $order->shipment->delivery_date->format('M d, Y') : 'N/A' }}</dd>
                </div>
            </dl>
        @endif

        <form action="{{ route('admin.orders.shipment', $order) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Courier Name</label>
                    <input type="text" name="courier_name" value="{{ $order->shipment->courier_name ?? '' }}" 
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Tracking Number</label>
                    <input type="text" name="tracking_number" value="{{ $order->shipment->tracking_number ?? '' }}" 
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Shipped Date</label>
                    <input type="date" name="shipped_date" value="{{ $order->shipment?->shipped_date?->format('Y-m-d') ?? date('Y-m-d') }}" 
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                </div>
            </div>
            <button type="submit" class="mt-4 inline-flex items-center rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                {{ $order->shipment ? 'Update' : 'Add' }} Shipment
            </button>
        </form>
    </div>
</div>
@endsection
