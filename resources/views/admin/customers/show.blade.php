@extends('admin.layout')

@section('title', 'Customer Details')
@section('page-title', 'Customer Details')

@section('content')
<div class="w-full space-y-6">
    <div>
        <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-rose-600 transition hover:text-rose-700">
            <i class="fas fa-arrow-left"></i>Back to Customers
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
            <h3 class="text-lg font-semibold text-stone-900 mb-4">Customer Information</h3>
            <dl class="space-y-2">
                <div>
                    <dt class="text-sm text-stone-500">Name</dt>
                    <dd class="font-medium text-stone-900">{{ $customer->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-stone-500">Email</dt>
                    <dd class="text-stone-700">{{ $customer->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-stone-500">Phone</dt>
                    <dd class="text-stone-700">{{ $customer->phone ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-stone-500">Status</dt>
                    <dd>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $customer->status == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ ucfirst($customer->status) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-stone-500">Joined</dt>
                    <dd class="text-stone-700">{{ $customer->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>

            <form action="{{ route('admin.customers.status', $customer) }}" method="POST" class="mt-4">
                @csrf
                @method('PATCH')
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Update Status</label>
                <div class="flex gap-2">
                    <select name="status" class="flex-1 rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                        <option value="active" {{ $customer->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $customer->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <button type="submit" class="inline-flex items-center rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                        Update
                    </button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-2 rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
            <h3 class="text-lg font-semibold text-stone-900 mb-4">Statistics</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-rose-100 bg-rose-50/60 p-4">
                    <p class="text-xs uppercase tracking-wider text-stone-500">Total Orders</p>
                    <p class="mt-2 text-2xl font-semibold text-stone-900">{{ $customer->orders->count() }}</p>
                </div>
                <div class="rounded-2xl border border-rose-100 bg-amber-50/60 p-4">
                    <p class="text-xs uppercase tracking-wider text-stone-500">Total Spent</p>
                    <p class="mt-2 text-2xl font-semibold text-stone-900">₹{{ number_format($customer->orders->where('payment_status', 'paid')->sum('total'), 2) }}</p>
                </div>
                <div class="rounded-2xl border border-rose-100 bg-stone-50 p-4">
                    <p class="text-xs uppercase tracking-wider text-stone-500">Addresses</p>
                    <p class="mt-2 text-2xl font-semibold text-stone-900">{{ $customer->addresses->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
        <h3 class="text-lg font-semibold text-stone-900 mb-4">Addresses</h3>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @foreach($customer->addresses as $address)
                <div class="rounded-2xl border border-rose-100 p-4 {{ $address->is_default ? 'bg-rose-50/70' : 'bg-white' }}">
                    @if($address->is_default)
                        <span class="inline-block rounded-full bg-rose-600 text-white text-xs px-3 py-1 mb-2">Default</span>
                    @endif
                    <p class="font-semibold text-stone-900">{{ $address->name }}</p>
                    <p class="text-sm text-stone-600 mt-2">
                        {{ $address->address }}<br>
                        {{ $address->city }}, {{ $address->state }} {{ $address->pincode }}<br>
                        {{ $address->country }}<br>
                        Phone: {{ $address->phone }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
        <h3 class="text-lg font-semibold text-stone-900 mb-4">Order History</h3>
        <table class="w-full">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Order #</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Items</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-100/70">
                @forelse($customer->orders as $order)
                <tr class="transition hover:bg-rose-50/60">
                    <td class="px-4 py-3 font-semibold text-stone-900">{{ $order->order_number }}</td>
                    <td class="px-4 py-3 text-stone-600">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3 text-stone-600">{{ $order->items->count() }}</td>
                    <td class="px-4 py-3 font-semibold text-stone-900">₹{{ number_format($order->total, 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($order->order_status == 'delivered') bg-emerald-100 text-emerald-800
                            @elseif($order->order_status == 'cancelled') bg-rose-100 text-rose-800
                            @else bg-amber-100 text-amber-800
                            @endif">
                            {{ ucfirst($order->order_status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-rose-600 transition hover:text-rose-700">
                            View
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-stone-500">No orders yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
