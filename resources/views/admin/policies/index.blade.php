@extends('admin.layout')

@section('title', 'Policy Pages')
@section('page-title', 'Policy Pages')

@section('content')
<div class="w-full space-y-6">
    <div class="rounded-[28px] border border-rose-200/60 bg-white p-8 shadow-sm">
        <div class="mb-6">
            <h3 class="mb-2 text-2xl font-semibold text-stone-900">Legal Content Management</h3>
            <p class="text-sm text-stone-500">Add or update Privacy Policy, Terms and Conditions, and Return & Refund page content.</p>
        </div>

        <form action="{{ route('admin.policies.update') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="privacy_policy" class="mb-3 block text-sm font-semibold uppercase tracking-wider text-stone-500">
                    <i class="fas fa-user-shield mr-2 text-rose-500"></i>Privacy Policy
                </label>
                <textarea
                    id="privacy_policy"
                    name="privacy_policy"
                    rows="10"
                    placeholder="Write privacy policy content here..."
                    class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                >{{ old('privacy_policy', $policyPage->privacy_policy) }}</textarea>
                @error('privacy_policy')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="terms_and_conditions" class="mb-3 block text-sm font-semibold uppercase tracking-wider text-stone-500">
                    <i class="fas fa-file-contract mr-2 text-rose-500"></i>Terms and Conditions
                </label>
                <textarea
                    id="terms_and_conditions"
                    name="terms_and_conditions"
                    rows="10"
                    placeholder="Write terms and conditions content here..."
                    class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                >{{ old('terms_and_conditions', $policyPage->terms_and_conditions) }}</textarea>
                @error('terms_and_conditions')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="return_and_refund" class="mb-3 block text-sm font-semibold uppercase tracking-wider text-stone-500">
                    <i class="fas fa-undo-alt mr-2 text-rose-500"></i>Return and Refund Policy
                </label>
                <textarea
                    id="return_and_refund"
                    name="return_and_refund"
                    rows="10"
                    placeholder="Write return and refund content here..."
                    class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                >{{ old('return_and_refund', $policyPage->return_and_refund) }}</textarea>
                @error('return_and_refund')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap gap-4 pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-8 py-3 text-sm font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-rose-200/50"
                >
                    <i class="fas fa-save"></i>
                    Save Policy Pages
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
$(document).ready(function() {
    $('#privacy_policy, #terms_and_conditions, #return_and_refund').summernote({
        height: 380
    });
});
</script>
@endpush
