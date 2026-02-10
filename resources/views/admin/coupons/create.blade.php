@extends('admin.layout')

@section('title', 'Create Coupon')
@section('page-title', 'Create Coupon')

@section('content')
<div class="w-full">
    <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm">
        <div class="border-b border-rose-100/80 px-6 py-5">
            <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">Promotions</p>
            <h3 class="mt-1 text-xl font-semibold text-stone-900">Create Coupon</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Coupon Code</label>
                    <input type="text" name="code" value="{{ old('code') }}" 
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm uppercase focus:border-rose-400 focus:outline-none" required>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Discount Type</label>
                        <select name="discount_type" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                            <option value="flat" {{ old('discount_type') == 'flat' ? 'selected' : '' }}>Flat Amount</option>
                            <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Percentage</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Discount Value</label>
                        <input type="number" step="0.01" name="discount_value" value="{{ old('discount_value') }}" 
                            class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Minimum Order Amount</label>
                    <input type="number" step="0.01" name="min_order_amount" value="{{ old('min_order_amount') }}" 
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Expiry Date</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" 
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Status</label>
                    <select name="status" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                        Create Coupon
                    </button>
                    <a href="{{ route('admin.coupons.index') }}" class="inline-flex items-center rounded-full border border-rose-200 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-600 transition hover:bg-rose-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
