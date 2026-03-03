<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SidebarBanner;
use Illuminate\Http\JsonResponse;

class SidebarBannerController extends Controller
{
    public function index(): JsonResponse
    {
        $banners = SidebarBanner::query()->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $banners->map(fn (SidebarBanner $banner) => [
                'id' => $banner->id,
                'heading' => $banner->heading,
                'sub_heading' => $banner->sub_heading,
                'image' => $banner->image,
                'image_url' => asset('storage/'.$banner->image),
            ])->values(),
        ]);
    }
}
