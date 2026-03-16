<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use Illuminate\Contracts\View\View;

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
}
