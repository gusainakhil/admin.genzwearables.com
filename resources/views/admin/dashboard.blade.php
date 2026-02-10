@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="relative space-y-6 rounded-[32px] bg-gradient-to-br from-rose-50 via-stone-50 to-amber-50 p-5 pt-2 shadow-sm">
    <div class="pointer-events-none absolute -top-14 right-0 h-64 w-64 rounded-full bg-gradient-to-br from-rose-200/70 via-amber-200/40 to-transparent blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-14 left-0 h-72 w-72 rounded-full bg-gradient-to-br from-stone-200/80 via-rose-100/50 to-transparent blur-3xl"></div>

    <div class="-mt-2 overflow-hidden rounded-[28px] border border-rose-200/60 bg-gradient-to-r from-stone-950 via-stone-900 to-stone-800 text-white shadow">
        <div class="flex flex-col gap-6 px-6 py-9 sm:px-8 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-3">
                <p class="text-[0.65rem] uppercase tracking-[0.45em] text-rose-200/80">Atelier Console</p>
                <h2 class="text-3xl font-semibold tracking-tight sm:text-4xl">Genz Wearables Dashboard</h2>
                <p class="max-w-2xl text-sm text-rose-100/80">A warm, premium view of orders, revenue, and customer energy. Your daily pulse, refined.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em]">
                    Today
                </span>
                <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em]">
                    Live Metrics
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="relative overflow-hidden rounded-[26px] border border-rose-200/60 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow">
            <div class="absolute right-0 top-0 h-24 w-24 -translate-y-6 translate-x-6 rounded-full bg-gradient-to-br from-rose-200/80 to-amber-100/60 blur-2xl"></div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">Total Users</p>
                        <p class="mt-3 text-4xl font-semibold text-stone-900">{{ $stats['total_users'] }}</p>
                        <p class="mt-2 text-xs font-semibold text-rose-600">+12% this month</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-rose-500 to-rose-600 text-white shadow-sm">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="h-1.5 w-full bg-gradient-to-r from-rose-500 via-rose-400 to-amber-300"></div>
        </div>

        <div class="relative overflow-hidden rounded-[26px] border border-rose-200/60 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow">
            <div class="absolute right-0 top-0 h-24 w-24 -translate-y-6 translate-x-6 rounded-full bg-gradient-to-br from-amber-200/80 to-rose-100/60 blur-2xl"></div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">Total Products</p>
                        <p class="mt-3 text-4xl font-semibold text-stone-900">{{ $stats['total_products'] }}</p>
                        <p class="mt-2 text-xs font-semibold text-rose-600">New drops weekly</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-rose-500 text-white shadow-sm">
                        <i class="fas fa-box text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="h-1.5 w-full bg-gradient-to-r from-amber-500 via-rose-500 to-rose-300"></div>
        </div>

        <div class="relative overflow-hidden rounded-[26px] border border-rose-200/60 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow">
            <div class="absolute right-0 top-0 h-24 w-24 -translate-y-6 translate-x-6 rounded-full bg-gradient-to-br from-rose-200/80 to-amber-100/60 blur-2xl"></div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">Total Orders</p>
                        <p class="mt-3 text-4xl font-semibold text-stone-900">{{ $stats['total_orders'] }}</p>
                        <p class="mt-2 text-xs font-semibold text-rose-600">Peak demand today</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-rose-500 to-amber-500 text-white shadow-sm">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="h-1.5 w-full bg-gradient-to-r from-rose-500 via-amber-400 to-amber-300"></div>
        </div>

        <div class="relative overflow-hidden rounded-[26px] border border-rose-200/60 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow">
            <div class="absolute right-0 top-0 h-24 w-24 -translate-y-6 translate-x-6 rounded-full bg-gradient-to-br from-rose-200/80 to-amber-100/60 blur-2xl"></div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">Total Revenue</p>
                        <p class="mt-3 text-4xl font-semibold text-stone-900">₹{{ number_format($stats['total_revenue'], 2) }}</p>
                        <p class="mt-2 text-xs font-semibold text-rose-600">High intent conversions</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-rose-500 to-rose-600 text-white shadow-sm">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="h-1.5 w-full bg-gradient-to-r from-rose-500 via-rose-400 to-amber-300"></div>
        </div>
    </div>

    <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-rose-100/80 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">Overview</p>
                <h3 class="mt-1 text-xl font-semibold text-stone-900">Recent Orders</h3>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="rounded-full bg-stone-900 px-4 py-2 text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-white">
                    Live Feed
                </span>
                <span class="rounded-full bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600">
                    Last {{ count($stats['recent_orders']) }} orders
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-stone-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-stone-500">
                        <th class="px-6 py-4">Order #</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rose-100/70">
                    @forelse($stats['recent_orders'] as $order)
                    <tr class="transition hover:bg-rose-50/60">
                        <td class="px-6 py-4">
                            <span class="font-semibold text-stone-900">{{ $order->order_number }}</span>
                        </td>
                        <td class="px-6 py-4 text-stone-600">{{ $order->user->name }}</td>
                        <td class="px-6 py-4 font-semibold text-stone-900">₹{{ number_format($order->total, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                @if($order->order_status == 'delivered') bg-emerald-100 text-emerald-800
                                @elseif($order->order_status == 'cancelled') bg-rose-100 text-rose-800
                                @else bg-amber-100 text-amber-800
                                @endif">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-500">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-rose-600 transition hover:text-rose-700">
                                View
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-stone-500">No orders yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
