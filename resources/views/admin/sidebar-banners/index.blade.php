@extends('admin.layout')

@section('title', 'Sidebar Banners')
@section('page-title', 'Sidebar Banners')

@section('content')
<div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm">
    <div class="flex flex-col gap-4 border-b border-rose-100/80 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-500">Website</p>
            <h3 class="mt-1 text-xl font-semibold text-stone-900">Sidebar Banner Ads</h3>
        </div>
        <a href="{{ route('admin.sidebar-banners.create') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
            <i class="fas fa-plus"></i>
            Add Banner
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-stone-50">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-stone-500">
                    <th class="px-6 py-4">Image</th>
                    <th class="px-6 py-4">Heading</th>
                    <th class="px-6 py-4">Sub Heading</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-100/70">
                @forelse($sidebarBanners as $banner)
                <tr class="transition hover:bg-rose-50/60">
                    <td class="px-6 py-4">
                        <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->heading }}" class="h-16 w-28 rounded-lg border border-rose-100/80 object-cover">
                    </td>
                    <td class="px-6 py-4 font-semibold text-stone-900">{{ $banner->heading }}</td>
                    <td class="px-6 py-4 text-sm text-stone-600">{{ $banner->sub_heading }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.sidebar-banners.edit', $banner) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 text-rose-600 transition hover:bg-rose-50">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.sidebar-banners.destroy', $banner) }}" method="POST" class="inline" onsubmit="return confirm('Delete this banner?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 text-rose-600 transition hover:bg-rose-50">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-sm text-stone-500">No sidebar banners added yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $sidebarBanners->links('pagination::tailwind') }}
</div>
@endsection
