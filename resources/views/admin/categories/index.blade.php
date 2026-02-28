@extends('admin.layout')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
<div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm">
    <div class="flex flex-col gap-4 border-b border-rose-100/80 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">Catalog</p>
            <h3 class="mt-1 text-xl font-semibold text-stone-900">All Categories</h3>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
            <i class="fas fa-plus"></i>
            Add Category
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-stone-50">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-stone-500">
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Slug</th>
                    <th class="px-6 py-4">Parent</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-100/70">
                @forelse($categories as $category)
                <tr class="transition hover:bg-rose-50/60">
                    <td class="px-6 py-4 text-sm text-stone-600">{{ $category->id }}</td>
                    <td class="px-6 py-4 font-semibold text-stone-900">{{ $category->name }}</td>
                    <td class="px-6 py-4 text-sm text-stone-500">{{ $category->slug }}</td>
                    <td class="px-6 py-4 text-sm text-stone-600">{{ $category->parent?->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $category->status == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ ucfirst($category->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 text-rose-600 transition hover:bg-rose-50">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 text-rose-600 transition hover:bg-rose-50">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form> -->
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-sm text-stone-500">No categories found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $categories->links('pagination::tailwind') }}
</div>
@endsection
