@extends('admin.layout')

@section('title', 'Return Requests')
@section('page-title', 'Return & Refund Requests')

@section('content')
<div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-stone-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Request ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Order</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Product</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Type</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Variant</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Images</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Reason</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Requested On</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-rose-100/70">
            @forelse($returnRequests as $request)
                @php
                    $statusClasses = [
                        'requested' => 'bg-amber-100 text-amber-700',
                        'approved' => 'bg-blue-100 text-blue-700',
                        'rejected' => 'bg-red-100 text-red-700',
                        'refunded' => 'bg-emerald-100 text-emerald-700',
                    ];
                @endphp
                <tr class="transition hover:bg-rose-50/60">
                    <td class="px-6 py-4 whitespace-nowrap text-stone-600">#{{ $request->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-stone-900">{{ $request->orderItem?->order?->order_number ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-stone-600">{{ $request->orderItem?->order?->user?->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-stone-600">{{ $request->orderItem?->product?->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-stone-600">{{ ucfirst($request->request_type ?? 'return') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-stone-600">{{ $request->productVariant?->sku ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-stone-600">{{ count($request->product_images ?? []) }}</td>
                    <td class="px-6 py-4 text-stone-600">{{ $request->reason ?: 'No reason provided' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $statusClasses[$request->status] ?? 'bg-stone-100 text-stone-600' }}">
                            {{ $request->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500">{{ $request->created_at?->format('M d, Y h:i A') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-6 py-4 text-center text-stone-500">No return or refund requests found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $returnRequests->links('pagination::tailwind') }}
</div>
@endsection
