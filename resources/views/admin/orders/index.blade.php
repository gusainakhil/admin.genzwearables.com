@extends('admin.layout')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
<div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div class="min-w-[220px] flex-1">
            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Order Status</label>
            <select name="status" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
            <option value="">All Order Status</option>
            <option value="placed" {{ request('status') == 'placed' ? 'selected' : '' }}>Placed</option>
            <option value="packed" {{ request('status') == 'packed' ? 'selected' : '' }}>Packed</option>
            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
            </select>
        </div>
        
        <div class="min-w-[220px] flex-1">
            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Payment Status</label>
            <select name="payment_status" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
            <option value="">All Payment Status</option>
            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        
        <button type="submit" class="inline-flex items-center rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
            Filter
        </button>
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center rounded-full border border-rose-200/70 bg-white px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-600 transition hover:border-rose-300 hover:text-stone-800">
            Reset
        </a>
    </form>
</div>

<div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-stone-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Order #</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Total</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Payment</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Order Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Date</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-rose-100/70">
            @forelse($orders as $order)
            <tr class="transition hover:bg-rose-50/60">
                <td class="px-6 py-4 whitespace-nowrap font-semibold text-stone-900">{{ $order->order_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-stone-700">{{ $order->user->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap font-semibold text-stone-900">₹{{ number_format($order->total, 2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full 
                        @if($order->payment_status == 'paid') bg-emerald-100 text-emerald-800
                        @elseif($order->payment_status == 'failed') bg-rose-100 text-rose-800
                        @else bg-amber-100 text-amber-800
                        @endif">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full 
                        @if($order->order_status == 'delivered') bg-emerald-100 text-emerald-800
                        @elseif($order->order_status == 'cancelled') bg-rose-100 text-rose-800
                        @else bg-amber-100 text-amber-800
                        @endif">
                        {{ ucfirst($order->order_status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500">
                    {{ $order->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex flex-col items-start gap-1.5">
                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-rose-600 transition hover:text-rose-700">
                            View Details
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                        <a href="{{ route('admin.orders.print-invoice', $order) }}" target="_blank" class="text-xs font-semibold text-stone-700 underline-offset-2 transition hover:text-stone-900 hover:underline">
                            Print Invoice
                        </a>
                        <a href="{{ route('admin.orders.print-parcel-sheet', $order) }}" target="_blank" class="text-xs font-semibold text-stone-700 underline-offset-2 transition hover:text-stone-900 hover:underline">
                            Print Parcel Sheet
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-stone-500">No orders found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $orders->links('pagination::tailwind') }}
</div>
@endsection
