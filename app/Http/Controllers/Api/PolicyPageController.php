<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PolicyPage;
use Illuminate\Http\JsonResponse;

class PolicyPageController extends Controller
{
    public function index(): JsonResponse
    {
        $policyPage = PolicyPage::query()->first();

        return response()->json([
            'status' => true,
            'data' => [
                'privacy_policy' => (string) ($policyPage?->privacy_policy ?? ''),
                'terms_and_conditions' => (string) ($policyPage?->terms_and_conditions ?? ''),
                'return_and_refund' => (string) ($policyPage?->return_and_refund ?? ''),
            ],
        ]);
    }

    public function show(string $type): JsonResponse
    {
        $allowedTypes = ['privacy_policy', 'terms_and_conditions', 'return_and_refund'];

        if (! in_array($type, $allowedTypes, true)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid policy type.',
            ], 422);
        }

        $policyPage = PolicyPage::query()->first();

        return response()->json([
            'status' => true,
            'data' => [
                'type' => $type,
                'content' => (string) ($policyPage?->{$type} ?? ''),
            ],
        ]);
    }
}
