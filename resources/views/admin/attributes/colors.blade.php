@extends('admin.layout')

@section('title', 'Colors')
@section('page-title', 'Colors')

@section('content')
<div class="w-full">
    <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm">
        <div class="border-b border-rose-100/80 px-6 py-5">
            <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">Attributes</p>
            <h3 class="mt-1 text-xl font-semibold text-stone-900">Colors</h3>
        </div>
        <div class="p-6">
            <h3 class="text-base font-semibold text-stone-900 mb-4">Add New Color</h3>
            <form action="{{ route('admin.colors.store') }}" method="POST" class="mb-6">
            @csrf
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[240px]">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Color Name</label>
                    <input type="text" name="name" placeholder="Color name (e.g., Red, Blue)" 
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                </div>
                <div class="w-40">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Hex Code</label>
                    <input type="color" name="hex_code" value="{{ old('hex_code', '#000000') }}"
                        class="h-11 w-full rounded-2xl border border-rose-200/60 bg-white px-2 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>
                <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                    <i class="fas fa-plus"></i>Add Color
                </button>
            </div>
            </form>

            <h3 class="text-base font-semibold text-stone-900 mb-4">All Colors</h3>
            <table class="w-full">
                <thead class="bg-stone-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Color</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Hex Code</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rose-100/70">
                    @forelse($colors as $color)
                    <tr class="transition hover:bg-rose-50/60">
                        <td class="px-6 py-4 whitespace-nowrap text-stone-600">{{ $color->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center">
                                <span class="w-6 h-6 rounded-full border border-rose-100 mr-3" style="background-color: {{ $color->hex_code ?: '#e7e5e4' }}"></span>
                                <span class="font-semibold text-stone-900">{{ $color->name }}</span>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-stone-500">{{ $color->hex_code ?? 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-stone-500">No colors found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
