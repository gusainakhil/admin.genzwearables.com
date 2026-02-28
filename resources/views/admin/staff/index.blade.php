@extends('admin.layout')

@section('title', 'Staff Users')
@section('page-title', 'Staff Users')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-500">Admin Access</p>
        <h3 class="text-lg font-semibold text-stone-900">Manage Staff Users</h3>
    </div>
    <a href="{{ route('admin.staff.create') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
        <i class="fas fa-plus"></i>Add Staff
    </a>
</div>

<div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-stone-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Email</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Joined</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-rose-100/70">
            @forelse($staffUsers as $staff)
                <tr class="transition hover:bg-rose-50/60">
                    <td class="px-6 py-4 text-sm text-stone-600">{{ $staff->id }}</td>
                    <td class="px-6 py-4 font-semibold text-stone-900">{{ $staff->name }}</td>
                    <td class="px-6 py-4 text-stone-600">{{ $staff->email }}</td>
                    <td class="px-6 py-4 text-stone-600">{{ $staff->phone ?: 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $staff->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ ucfirst($staff->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-stone-500">{{ $staff->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.staff.edit', $staff) }}" class="inline-flex items-center text-rose-600 transition hover:text-rose-700" title="Edit Staff">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.staff.destroy', $staff) }}" method="POST" class="inline" onsubmit="return confirm('Delete this staff user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 transition hover:text-rose-700" title="Delete Staff">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-sm text-stone-500">No staff users found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $staffUsers->links('pagination::tailwind') }}
</div>
@endsection
