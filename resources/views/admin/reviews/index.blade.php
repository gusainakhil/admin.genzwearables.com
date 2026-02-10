@extends('admin.layout')

@section('title', 'Reviews')
@section('page-title', 'Reviews')

@section('content')
<div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-stone-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Product</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Rating</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Comment</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Date</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-rose-100/70">
            @forelse($reviews as $review)
            <tr class="transition hover:bg-rose-50/60">
                <td class="px-6 py-4 whitespace-nowrap text-stone-600">{{ $review->id }}</td>
                <td class="px-6 py-4 font-semibold text-stone-900">{{ $review->product->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-stone-600">{{ $review->user->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-amber-400' : 'text-stone-200' }}"></i>
                        @endfor
                        <span class="ml-2 text-sm text-stone-600">({{ $review->rating }})</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="max-w-xs truncate text-stone-600">{{ $review->comment }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500">
                    {{ $review->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline" onsubmit="return confirm('Delete this review?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-600 transition hover:text-rose-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-stone-500">No reviews found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $reviews->links('pagination::tailwind') }}
</div>
@endsection
