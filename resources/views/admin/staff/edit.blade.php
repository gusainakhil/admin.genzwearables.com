@extends('admin.layout')

@section('title', 'Edit Staff User')
@section('page-title', 'Edit Staff User')

@section('content')
<div class="w-full">
    <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm">
        <div class="border-b border-rose-100/80 px-6 py-5">
            <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">Admin Access</p>
            <h3 class="mt-1 text-xl font-semibold text-stone-900">Edit Staff User</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.staff.update', $staff) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Name</label>
                    <input type="text" name="name" value="{{ old('name', $staff->name) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Email</label>
                        <input type="email" name="email" value="{{ old('email', $staff->email) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Status</label>
                    <select name="status" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                        <option value="active" {{ old('status', $staff->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $staff->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">New Password (Optional)</label>
                        <input type="password" name="password" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                        Update Staff
                    </button>
                    <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center rounded-full border border-rose-200 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-600 transition hover:bg-rose-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
