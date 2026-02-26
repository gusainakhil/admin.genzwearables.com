<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 24px;
            color: #111827;
            font-size: 13px;
            line-height: 1.45;
        }

        .header,
        .summary,
        .addresses,
        .totals {
            margin-bottom: 20px;
        }

        .header {
            border: 1px solid #d1d5db;
            padding: 14px;
        }

        .header-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 16px;
            align-items: start;
        }

        .title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .muted {
            color: #4b5563;
            font-size: 12px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            font-size: 12px;
        }

        .totals {
            margin-left: auto;
            width: 320px;
        }

        .totals td {
            border: 0;
            border-bottom: 1px solid #d1d5db;
        }

        .totals .grand-total td {
            font-size: 15px;
            font-weight: 700;
            border-bottom: 0;
        }

        .actions {
            margin-bottom: 18px;
            display: flex;
            gap: 12px;
        }

        .actions a,
        .actions button {
            border: 1px solid #d1d5db;
            background: #fff;
            color: #111827;
            padding: 8px 12px;
            text-decoration: none;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        @media print {
            .actions {
                display: none;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    @php
        $shippingAddress = $order->address_snapshot ?? null;

        if (! $shippingAddress && $order->address) {
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
    @endphp

    <div class="actions">
        <button type="button" onclick="window.print()">Print Invoice</button>
        <a href="{{ route('admin.orders.show', $order) }}">Back to Order</a>
    </div>

    <div class="header">
        <div class="header-grid">
            <div>
                <div class="title">Tax Invoice</div>
                <div style="font-weight:700; font-size:16px; margin-bottom:4px;">{{ $companyDetail?->brand_name ?? config('app.name') }}</div>
                <div class="muted">
                    {{ $companyDetail?->address ?? 'Company address not set' }}<br>
                    @if($companyDetail?->city || $companyDetail?->district || $companyDetail?->state || $companyDetail?->pincode)
                        {{ $companyDetail?->city }}{{ $companyDetail?->city && $companyDetail?->district ? ', ' : '' }}{{ $companyDetail?->district }}{{ ($companyDetail?->city || $companyDetail?->district) && $companyDetail?->state ? ', ' : '' }}{{ $companyDetail?->state }} {{ $companyDetail?->pincode }}<br>
                    @endif
                    {{ $companyDetail?->country ?? '' }}
                </div>
            </div>
            <div>
                <div><strong>Invoice No:</strong> INV-{{ $order->order_number }}</div>
                <div><strong>Order No:</strong> {{ $order->order_number }}</div>
                <div><strong>Order Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</div>
                <div><strong>GST:</strong> {{ $companyDetail?->gst_number ?? 'N/A' }}</div>
                <div><strong>Contact:</strong> {{ $companyDetail?->phone_number1 ?? 'N/A' }}</div>
                @if($companyDetail?->email_primary || $companyDetail?->support_email)
                    <div><strong>Email:</strong> {{ $companyDetail?->email_primary ?? $companyDetail?->support_email }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="summary grid">
        <div>
            <div><strong>Billed To:</strong> {{ $order->user->name }}</div>
            <div><strong>Email:</strong> {{ $order->user->email }}</div>
            <div><strong>Phone:</strong> {{ $order->user->phone ?? 'N/A' }}</div>
        </div>
        <div>
            <div><strong>Payment:</strong> {{ strtoupper($order->payment_status) }}</div>
            <div><strong>Order Status:</strong> {{ strtoupper($order->order_status) }}</div>
            @if($companyDetail?->website_name)
                <div><strong>Website:</strong> {{ $companyDetail->website_name }}</div>
            @endif
        </div>
    </div>

    <div class="addresses">
        <div style="font-weight:700; margin-bottom:8px;">Shipping Address</div>
        <div>
            {{ $shippingAddress['name'] ?? 'N/A' }}, {{ $shippingAddress['phone'] ?? 'N/A' }}<br>
            {{ $shippingAddress['address'] ?? 'N/A' }}<br>
            {{ $shippingAddress['city'] ?? 'N/A' }}, {{ $shippingAddress['state'] ?? 'N/A' }} {{ $shippingAddress['pincode'] ?? '' }}<br>
            {{ $shippingAddress['country'] ?? 'N/A' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Variant</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->variant->size->name }} / {{ $item->variant->color->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₹{{ number_format($item->price, 2) }}</td>
                    <td>₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tbody>
            <tr>
                <td>Subtotal</td>
                <td style="text-align:right;">₹{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Shipping</td>
                <td style="text-align:right;">₹{{ number_format($order->shipping, 2) }}</td>
            </tr>
            <tr>
                <td>Discount</td>
                <td style="text-align:right;">-₹{{ number_format($order->discount, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td>Grand Total</td>
                <td style="text-align:right;">₹{{ number_format($order->total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
