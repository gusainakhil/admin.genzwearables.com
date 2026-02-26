@extends('admin.layout')

@section('title', 'Company Details')
@section('page-title', 'Company Details')

@section('content')
<div class="w-full space-y-6">
    <div class="rounded-[28px] border border-rose-200/60 bg-white p-8 shadow-sm">
        <div class="mb-6">
            <h3 class="text-2xl font-semibold text-stone-900">Company Profile</h3>
            <p class="text-sm text-stone-500">Add or update company details used in invoices, labels, and branding.</p>
        </div>

        <form action="{{ route('admin.company-details.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Brand Name *</label>
                    <input type="text" name="brand_name" value="{{ old('brand_name', $companyDetail?->brand_name) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Website Name</label>
                    <input type="text" name="website_name" value="{{ old('website_name', $companyDetail?->website_name) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Phone Number 1</label>
                    <input type="text" name="phone_number1" value="{{ old('phone_number1', $companyDetail?->phone_number1) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Phone Number 2</label>
                    <input type="text" name="phone_number2" value="{{ old('phone_number2', $companyDetail?->phone_number2) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">GST Number</label>
                    <input type="text" name="gst_number" value="{{ old('gst_number', $companyDetail?->gst_number) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Support Email</label>
                    <input type="email" name="support_email" value="{{ old('support_email', $companyDetail?->support_email) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Primary Email</label>
                    <input type="email" name="email_primary" value="{{ old('email_primary', $companyDetail?->email_primary) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Secondary Email</label>
                    <input type="email" name="email_secondary" value="{{ old('email_secondary', $companyDetail?->email_secondary) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Address</label>
                    <textarea name="address" rows="3" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">{{ old('address', $companyDetail?->address) }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">City</label>
                    <input type="text" name="city" value="{{ old('city', $companyDetail?->city) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">District</label>
                    <input type="text" name="district" value="{{ old('district', $companyDetail?->district) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Pincode</label>
                    <input type="text" name="pincode" value="{{ old('pincode', $companyDetail?->pincode) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">State</label>
                    <input type="text" name="state" value="{{ old('state', $companyDetail?->state) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Country</label>
                    <input type="text" name="country" value="{{ old('country', $companyDetail?->country) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Additional Info</label>
                    <textarea name="additional_info" rows="3" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">{{ old('additional_info', $companyDetail?->additional_info) }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-2xl border border-rose-100/70 bg-stone-50/40 p-5">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Logo</label>
                    <input type="file" name="logo" accept="image/*" class="w-full rounded-xl border border-rose-200/60 bg-white px-3 py-2 text-sm text-stone-700 focus:border-rose-400 focus:outline-none">
                    @if($companyDetail?->logo)
                        <img src="{{ asset('storage/' . $companyDetail->logo) }}" alt="Company Logo" class="mt-4 h-16 w-auto rounded-lg border border-rose-100/70 bg-white p-2">
                    @endif
                </div>

                <div class="rounded-2xl border border-rose-100/70 bg-stone-50/40 p-5">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Favicon</label>
                    <input type="file" name="favicon" accept="image/*" class="w-full rounded-xl border border-rose-200/60 bg-white px-3 py-2 text-sm text-stone-700 focus:border-rose-400 focus:outline-none">
                    @if($companyDetail?->favicon)
                        <img src="{{ asset('storage/' . $companyDetail->favicon) }}" alt="Company Favicon" class="mt-4 h-12 w-12 rounded-lg border border-rose-100/70 bg-white p-2">
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-7 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                    <i class="fas fa-save"></i>
                    Save Company Details
                </button>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-rose-200/70 bg-white px-7 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-stone-600 transition hover:border-rose-300 hover:text-stone-800">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
