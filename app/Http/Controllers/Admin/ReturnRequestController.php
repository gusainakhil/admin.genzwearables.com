<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
    public function index(): View
    {
        $returnRequests = ReturnRequest::query()
            ->with([
                'orderItem.order.user',
                'orderItem.product',
                'productVariant.size',
                'productVariant.color',
            ])
            ->latest()
            ->paginate(20);

        return view('admin.return-requests.index', compact('returnRequests'));
    }

    public function show(ReturnRequest $returnRequest): View
    {
        $returnRequest->load([
            'orderItem.order.user',
            'orderItem.order.address',
            'orderItem.product',
            'orderItem.variant.size',
            'orderItem.variant.color',
            'productVariant.size',
            'productVariant.color',
        ]);

        return view('admin.return-requests.show', compact('returnRequest'));
    }

    public function update(Request $request, ReturnRequest $returnRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:requested,approved,rejected,refunded',
            'requested_by' => 'nullable|in:self,user',
            'tracking_id' => 'nullable|string|max:100',
            'courier_name' => 'nullable|string|max:100',
            'link' => 'nullable|url|max:2048',
        ]);

        $returnRequest->update([
            'status' => $validated['status'],
            'requested_by' => $validated['requested_by'] ?? null,
            'tracking_id' => ($validated['requested_by'] ?? null) === 'self'
                ? ($validated['tracking_id'] ?? null)
                : null,
            'courier_name' => ($validated['requested_by'] ?? null) === 'self'
                ? ($validated['courier_name'] ?? null)
                : null,
            'link' => ($validated['requested_by'] ?? null) === 'self'
                ? ($validated['link'] ?? null)
                : null,
        ]);

        return redirect()
            ->route('admin.return-requests.show', $returnRequest)
            ->with('success', 'Return request updated successfully.');
    }
}
