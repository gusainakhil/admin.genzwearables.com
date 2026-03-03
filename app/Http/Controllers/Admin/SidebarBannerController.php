<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSidebarBannerRequest;
use App\Http\Requests\Admin\UpdateSidebarBannerRequest;
use App\Models\SidebarBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SidebarBannerController extends Controller
{
    public function index(): View
    {
        $sidebarBanners = SidebarBanner::query()->latest()->paginate(15);

        return view('admin.sidebar-banners.index', compact('sidebarBanners'));
    }

    public function create(): View
    {
        return view('admin.sidebar-banners.create');
    }

    public function store(StoreSidebarBannerRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['image'] = $request->file('image')->store('sidebar-banners', 'public');

        SidebarBanner::query()->create($validated);

        return redirect()->route('admin.sidebar-banners.index')
            ->with('success', 'Sidebar banner created successfully');
    }

    public function edit(SidebarBanner $sidebarBanner): View
    {
        return view('admin.sidebar-banners.edit', compact('sidebarBanner'));
    }

    public function update(UpdateSidebarBannerRequest $request, SidebarBanner $sidebarBanner): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($sidebarBanner->image) {
                Storage::disk('public')->delete($sidebarBanner->image);
            }

            $validated['image'] = $request->file('image')->store('sidebar-banners', 'public');
        }

        $sidebarBanner->update($validated);

        return redirect()->route('admin.sidebar-banners.index')
            ->with('success', 'Sidebar banner updated successfully');
    }

    public function destroy(SidebarBanner $sidebarBanner): RedirectResponse
    {
        if ($sidebarBanner->image) {
            Storage::disk('public')->delete($sidebarBanner->image);
        }

        $sidebarBanner->delete();

        return redirect()->route('admin.sidebar-banners.index')
            ->with('success', 'Sidebar banner deleted successfully');
    }
}
