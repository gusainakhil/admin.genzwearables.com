@extends('admin.layout')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-500">Inventory</p>
        <h3 class="text-lg font-semibold text-stone-900">All Products</h3>
    </div>
    <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
        <i class="fas fa-plus"></i>Add Product
    </a>
</div>

<div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-stone-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Category</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Brand</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Price</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-rose-100/70">
            @forelse($products as $product)
            <tr class="transition hover:bg-rose-50/60">
                <td class="px-6 py-4 whitespace-nowrap text-stone-600">{{ $product->id }}</td>
                <td class="px-6 py-4 font-semibold text-stone-900">{{ $product->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-stone-600">{{ $product->category?->name ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-stone-600">{{ $product->brand ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap font-semibold text-stone-900">₹{{ number_format($product->base_price, 2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full {{ $product->status == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ ucfirst($product->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center text-rose-600 transition hover:text-rose-700">
                        <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-600 transition hover:text-rose-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-stone-500">No products found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $products->links('pagination::tailwind') }}
</div>
@endsection
