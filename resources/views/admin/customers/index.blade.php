@extends('admin.layout')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')
<div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm">
    <div class="flex flex-col gap-4 border-b border-rose-100/80 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">People</p>
            <h3 class="mt-1 text-xl font-semibold text-stone-900">Customers</h3>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-stone-50">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-stone-500">
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Phone</th>
                    <th class="px-6 py-4">Orders</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Joined</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-100/70">
                @forelse($customers as $customer)
                <tr class="transition hover:bg-rose-50/60">
                    <td class="px-6 py-4 text-sm text-stone-600">{{ $customer->id }}</td>
                    <td class="px-6 py-4 font-semibold text-stone-900">{{ $customer->name }}</td>
                    <td class="px-6 py-4 text-stone-600">{{ $customer->email }}</td>
                    <td class="px-6 py-4 text-stone-600">{{ $customer->phone ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-stone-600">{{ $customer->orders_count }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $customer->status == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ ucfirst($customer->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-stone-500">{{ $customer->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.customers.show', $customer) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-rose-600 transition hover:text-rose-700">
                            View
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-10 text-center text-sm text-stone-500">No customers found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $customers->links('pagination::tailwind') }}
</div>
@endsection
