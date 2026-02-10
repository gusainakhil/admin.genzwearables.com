@extends('admin.layout')

@section('title', 'Coupons')
@section('page-title', 'Coupons')

@section('content')
<div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm">
    <div class="flex flex-col gap-4 border-b border-rose-100/80 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">Promotions</p>
            <h3 class="mt-1 text-xl font-semibold text-stone-900">All Coupons</h3>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
            <i class="fas fa-plus"></i>
            Add Coupon
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-stone-50">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-stone-500">
                    <th class="px-6 py-4">Code</th>
                    <th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4">Value</th>
                    <th class="px-6 py-4">Min Order</th>
                    <th class="px-6 py-4">Expiry</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-100/70">
                @forelse($coupons as $coupon)
                <tr class="transition hover:bg-rose-50/60">
                    <td class="px-6 py-4 font-semibold text-stone-900">{{ $coupon->code }}</td>
                    <td class="px-6 py-4 text-stone-600">{{ ucfirst($coupon->discount_type) }}</td>
                    <td class="px-6 py-4 text-stone-700">
                        @if($coupon->discount_type == 'percent')
                            {{ $coupon->discount_value }}%
                        @else
                            ₹{{ number_format($coupon->discount_value, 2) }}
                        @endif
                    </td>
                    <td class="px-6 py-4 text-stone-600">₹{{ number_format($coupon->min_order_amount, 2) }}</td>
                    <td class="px-6 py-4 text-stone-600">{{ $coupon->expiry_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $coupon->status == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ ucfirst($coupon->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 text-rose-600 transition hover:bg-rose-50">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 text-rose-600 transition hover:bg-rose-50">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-sm text-stone-500">No coupons found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $coupons->links('pagination::tailwind') }}
</div>
@endsection
