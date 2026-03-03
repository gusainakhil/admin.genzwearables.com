@extends('admin.layout')

@section('title', 'Add Sidebar Banner')
@section('page-title', 'Add Sidebar Banner')

@section('content')
<div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm">
    <div class="border-b border-rose-100/80 px-6 py-5">
        <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">Website</p>
        <h3 class="mt-1 text-xl font-semibold text-stone-900">Create Sidebar Banner</h3>
    </div>

    <div class="p-6">
        <form action="{{ route('admin.sidebar-banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="heading" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Heading</label>
                <input type="text" id="heading" name="heading" value="{{ old('heading') }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
            </div>

            <div>
                <label for="sub_heading" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Sub Heading</label>
                <input type="text" id="sub_heading" name="sub_heading" value="{{ old('sub_heading') }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
            </div>

            <div>
                <label for="image" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Image</label>
                <input type="file" id="image" name="image" accept="image/*" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                    <i class="fas fa-save"></i>
                    Save Banner
                </button>
                <a href="{{ route('admin.sidebar-banners.index') }}" class="inline-flex items-center rounded-full border border-rose-200 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-600 transition hover:bg-rose-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
