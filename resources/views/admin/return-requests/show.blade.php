@extends('admin.layout')

@section('title', 'Return Request Details')
@section('page-title', 'Return Request Details')

@section('content')
@php
    $order = $returnRequest->orderItem?->order;
    $customer = $order?->user;

    $shippingAddress = $order?->address_snapshot;

    if (! $shippingAddress && $order?->address) {
        $shippingAddress = [
            'name' => $order->address->name,
            'phone' => $order->address->phone,
            'address' => $order->address->address,
            'city' => $order->address->city,
            'state' => $order->address->state,
            'pincode' => $order->address->pincode,
            'country' => $order->address->country,
        ];
    }

    $productImages = $returnRequest->product_images ?? [];
    $variant = $returnRequest->productVariant ?: $returnRequest->orderItem?->variant;
@endphp

<div class="w-full space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.return-requests.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-rose-600 transition hover:text-rose-700">
            <i class="fas fa-arrow-left"></i>Back to Return Requests
        </a>
        <button type="button" id="openReturnUpdateModal" class="inline-flex items-center rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-5 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
            Update Request
        </button>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-[28px] border border-rose-200/60 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-stone-900">Request Information</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Request ID</dt>
                    <dd class="font-medium text-stone-900">#{{ $returnRequest->id }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Type</dt>
                    <dd class="text-stone-700">{{ ucfirst($returnRequest->request_type ?? 'return') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Requested By</dt>
                    <dd class="text-stone-700">{{ $returnRequest->requested_by ? ucfirst($returnRequest->requested_by) : '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Tracking ID</dt>
                    <dd class="text-stone-700">{{ $returnRequest->tracking_id ?? 'Not assigned' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Courier Name</dt>
                    <dd class="text-stone-700">{{ $returnRequest->courier_name ?? 'Not assigned' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Link</dt>
                    <dd class="text-stone-700 break-all">
                        @if($returnRequest->link)
                            <a href="{{ $returnRequest->link }}" target="_blank" class="text-rose-600 transition hover:text-rose-700 hover:underline">{{ $returnRequest->link }}</a>
                        @else
                            Not assigned
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Status</dt>
                    <dd>
                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700">
                            {{ $returnRequest->status }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Submitted On</dt>
                    <dd class="text-stone-700">{{ $returnRequest->created_at?->format('M d, Y h:i A') ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-[28px] border border-rose-200/60 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-stone-900">Order Information</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Order Number</dt>
                    <dd class="font-medium text-stone-900">{{ $order?->order_number ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Order Status</dt>
                    <dd class="text-stone-700">{{ ucfirst($order?->order_status ?? '-') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Payment Status</dt>
                    <dd class="text-stone-700">{{ ucfirst($order?->payment_status ?? '-') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Order Date</dt>
                    <dd class="text-stone-700">{{ $order?->created_at?->format('M d, Y h:i A') ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-[28px] border border-rose-200/60 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-stone-900">Customer Information</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Name</dt>
                    <dd class="font-medium text-stone-900">{{ $customer?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Email</dt>
                    <dd class="text-stone-700">{{ $customer?->email ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Phone</dt>
                    <dd class="text-stone-700">{{ $customer?->phone ?? '-' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-[28px] border border-rose-200/60 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-stone-900">Product Information</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Product</dt>
                    <dd class="font-medium text-stone-900">{{ $returnRequest->orderItem?->product?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Variant SKU</dt>
                    <dd class="text-stone-700">{{ $variant?->sku ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Size</dt>
                    <dd class="text-stone-700">{{ $variant?->size?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Color</dt>
                    <dd class="text-stone-700">{{ $variant?->color?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Reason</dt>
                    <dd class="text-stone-700">{{ $returnRequest->reason ?: 'No reason provided' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-[28px] border border-rose-200/60 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-stone-900">Shipping Address</h3>
            @if($shippingAddress)
                <p class="text-stone-700 leading-7">
                    {{ $shippingAddress['name'] ?? 'N/A' }}<br>
                    {{ $shippingAddress['address'] ?? 'N/A' }}<br>
                    {{ $shippingAddress['city'] ?? 'N/A' }}, {{ $shippingAddress['state'] ?? 'N/A' }} {{ $shippingAddress['pincode'] ?? '' }}<br>
                    {{ $shippingAddress['country'] ?? 'N/A' }}<br>
                    Phone: {{ $shippingAddress['phone'] ?? 'N/A' }}
                </p>
            @else
                <p class="text-stone-500">Shipping address is not available.</p>
            @endif
        </div>
    </div>

    <div class="rounded-[28px] border border-rose-200/60 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-stone-900">Uploaded Images</h3>
            <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-stone-600">
                {{ count($productImages) }} image(s)
            </span>
        </div>

        @if(count($productImages) > 0)
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                @foreach($productImages as $imagePath)
                    <a href="{{ asset('storage/'.$imagePath) }}" target="_blank" class="group block overflow-hidden rounded-2xl border border-rose-200/60 bg-stone-50">
                        <img src="{{ asset('storage/'.$imagePath) }}" alt="Return image" class="h-40 w-full object-cover transition duration-300 group-hover:scale-105">
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-stone-500">No images were uploaded by the user.</p>
        @endif
    </div>
</div>

<div id="returnUpdateModal" class="fixed inset-0 z-40 hidden items-center justify-center bg-stone-950/50 px-4 backdrop-blur-sm">
    <div class="w-full max-w-2xl rounded-[28px] border border-rose-200/60 bg-white p-6 shadow-xl">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <p class="text-[0.65rem] uppercase tracking-[0.35em] text-stone-400">Return Workflow</p>
                <h3 class="mt-2 text-xl font-semibold text-stone-900">Update Return Request</h3>
            </div>
            <button type="button" id="closeReturnUpdateModal" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-rose-200/70 text-stone-500 transition hover:border-rose-300 hover:text-stone-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.return-requests.update', $returnRequest) }}" method="POST" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label for="status" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Status</label>
                <select id="status" name="status" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                    <option value="requested" {{ old('status', $returnRequest->status) === 'requested' ? 'selected' : '' }}>Requested</option>
                    <option value="approved" {{ old('status', $returnRequest->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ old('status', $returnRequest->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="refunded" {{ old('status', $returnRequest->status) === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <div>
                <label for="requested_by" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Requested By</label>
                <select id="requested_by" name="requested_by" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                    <option value="" {{ old('requested_by', $returnRequest->requested_by) === null ? 'selected' : '' }}>Select requested by</option>
                    <option value="self" {{ old('requested_by', $returnRequest->requested_by) === 'self' ? 'selected' : '' }}>Self</option>
                    <option value="user" {{ old('requested_by', $returnRequest->requested_by) === 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>

            <div id="courierDetailsWrapper" class="hidden space-y-5">
                <label for="tracking_id" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Tracking ID</label>
                <input id="tracking_id" type="text" name="tracking_id" value="{{ old('tracking_id', $returnRequest->tracking_id) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" placeholder="Enter tracking id">
                <div>
                    <label for="courier_name" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Courier Name</label>
                    <input id="courier_name" type="text" name="courier_name" value="{{ old('courier_name', $returnRequest->courier_name) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" placeholder="Enter courier name">
                </div>
                <div>
                    <label for="link" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-stone-500">Link</label>
                    <input id="link" type="url" name="link" value="{{ old('link', $returnRequest->link) }}" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" placeholder="https://tracking.example.com/...">
                </div>
                <p class="mt-2 text-xs text-stone-500">Tracking, courier name, and link are editable later as well.</p>
            </div>

            <div class="flex flex-wrap justify-end gap-3 pt-2">
                <button type="button" id="cancelReturnUpdateModal" class="inline-flex items-center rounded-full border border-rose-200/70 bg-white px-5 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-600 transition hover:border-rose-300 hover:text-stone-800">
                    Cancel
                </button>
                <button type="submit" class="inline-flex items-center rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-5 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('returnUpdateModal');
        const openButton = document.getElementById('openReturnUpdateModal');
        const closeButton = document.getElementById('closeReturnUpdateModal');
        const cancelButton = document.getElementById('cancelReturnUpdateModal');
        const requestedByField = document.getElementById('requested_by');
        const courierDetailsWrapper = document.getElementById('courierDetailsWrapper');

        const toggleCourierFields = () => {
            const showCourierFields = requestedByField.value === 'self';
            courierDetailsWrapper.classList.toggle('hidden', !showCourierFields);
        };

        const openModal = () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            toggleCourierFields();
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        openButton.addEventListener('click', openModal);
        closeButton.addEventListener('click', closeModal);
        cancelButton.addEventListener('click', closeModal);
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });
        requestedByField.addEventListener('change', toggleCourierFields);
        toggleCourierFields();

        @if($errors->any())
            openModal();
        @endif
    });
</script>
@endpush
