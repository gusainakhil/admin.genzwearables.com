@extends('admin.layout')

@section('title', 'Sizes')
@section('page-title', 'Sizes')

@section('content')
<div class="w-full">
    <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm">
        <div class="border-b border-rose-100/80 px-6 py-5">
            <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">Attributes</p>
            <h3 class="mt-1 text-xl font-semibold text-stone-900">Sizes</h3>
        </div>
        <div class="p-6">
            <h3 class="text-base font-semibold text-stone-900 mb-4">Add New Size</h3>
            <form action="{{ route('admin.sizes.store') }}" method="POST" class="mb-6">
            @csrf
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[240px]">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Size Name</label>
                    <input type="text" name="name" placeholder="Size name (e.g., S, M, L, XL)" 
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                    <i class="fas fa-plus"></i>Add Size
                </button>
            </div>
            </form>

            <h3 class="text-base font-semibold text-stone-900 mb-4">All Sizes</h3>
            <table class="w-full">
                <thead class="bg-stone-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Name</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rose-100/70">
                    @forelse($sizes as $size)
                    <tr class="transition hover:bg-rose-50/60">
                        <td class="px-6 py-4 whitespace-nowrap text-stone-600">{{ $size->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-stone-900">{{ $size->name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-center text-stone-500">No sizes found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
