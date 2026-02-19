<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DeleteUserAddressRequest;
use App\Http\Requests\Api\StoreUserAddressRequest;
use App\Http\Requests\Api\UpdateUserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $addresses,
        ]);
    }

    public function store(StoreUserAddressRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        if (($validated['is_default'] ?? false) === true) {
            $request->user()
                ->addresses()
                ->update(['is_default' => false]);
        }

        $address = UserAddress::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Address created successfully',
            'data' => $address,
        ], 201);
    }

    public function update(UpdateUserAddressRequest $request, UserAddress $address): JsonResponse
    {
        $validated = $request->validated();

        if (($validated['is_default'] ?? false) === true) {
            $request->user()
                ->addresses()
                ->whereKeyNot($address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
            'data' => $address->fresh(),
        ]);
    }

    public function destroy(DeleteUserAddressRequest $request, UserAddress $address): JsonResponse
    {
        $address->delete();

        return response()->json([
            'status' => true,
            'message' => 'Address deleted successfully',
        ]);
    }
}
