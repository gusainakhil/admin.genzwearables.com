<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parcel Sheet - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #111827;
            line-height: 1.4;
            font-size: 13px;
        }

        .actions {
            margin-bottom: 14px;
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

        .sheet {
            border: 2px solid #111827;
            padding: 16px;
        }

        .row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
            margin-bottom: 12px;
        }

        .section {
            border: 1px solid #111827;
            padding: 10px;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .code {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: left;
            vertical-align: top;
            font-size: 12px;
        }

        th {
            background: #f3f4f6;
        }

        .meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        @media print {
            .actions {
                display: none;
            }

            body {
                margin: 0;
            }

            .sheet {
                border: 2px solid #000;
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
        <button type="button" onclick="window.print()">Print Parcel Sheet</button>
        <a href="{{ route('admin.orders.show', $order) }}">Back to Order</a>
    </div>

    <div class="sheet">
        <div class="row">
            <div class="section">
                <div class="section-title">Shipping Label</div>
                <div style="margin-bottom: 10px;">
                    <strong>To:</strong><br>
                    {{ $shippingAddress['name'] ?? 'N/A' }} ({{ $shippingAddress['phone'] ?? 'N/A' }})<br>
                    {{ $shippingAddress['address'] ?? 'N/A' }}<br>
                    {{ $shippingAddress['city'] ?? 'N/A' }}, {{ $shippingAddress['state'] ?? 'N/A' }} {{ $shippingAddress['pincode'] ?? '' }}<br>
                    {{ $shippingAddress['country'] ?? 'N/A' }}
                </div>

                <div>
                    <strong>From:</strong><br>
                    {{ $companyDetail?->brand_name ?? config('app.name') }}<br>
                    {{ $companyDetail?->address ?? 'Warehouse Address Not Set' }}<br>
                    @if($companyDetail?->city || $companyDetail?->district || $companyDetail?->state || $companyDetail?->pincode)
                        {{ $companyDetail?->city }}{{ $companyDetail?->city && $companyDetail?->district ? ', ' : '' }}{{ $companyDetail?->district }}{{ ($companyDetail?->city || $companyDetail?->district) && $companyDetail?->state ? ', ' : '' }}{{ $companyDetail?->state }} {{ $companyDetail?->pincode }}<br>
                    @endif
                    {{ $companyDetail?->country ?? '' }}
                    <br>Phone: {{ $companyDetail?->phone_number1 ?? 'N/A' }}
                </div>
            </div>

            <div class="section" style="text-align:center;">
                <div class="section-title">Scan Code</div>
                <img
                    src="https://api.qrserver.com/v1/create-qr-code/?size=170x170&data={{ urlencode($scanPayload) }}"
                    alt="Order QR"
                    width="170"
                    height="170"
                >
                <div style="font-size:11px; margin-top:8px;">Scan for order details</div>
            </div>
        </div>

        <div class="section" style="margin-bottom: 12px;">
            <div class="code">ORDER: {{ $order->order_number }}</div>
            <div class="meta">
                <div><strong>Status:</strong> {{ strtoupper($order->order_status) }}</div>
                <div><strong>Payment:</strong> {{ strtoupper($order->payment_status) }}</div>
                <div><strong>Total:</strong> ₹{{ number_format($order->total, 2) }}</div>
                <div><strong>Courier:</strong> {{ $order->shipment?->courier_name ?? 'Not assigned' }}</div>
                <div><strong>Tracking:</strong> {{ $order->shipment?->tracking_number ?? 'Not assigned' }}</div>
                <div><strong>Order Date:</strong> {{ $order->created_at->format('d M Y') }}</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Package Contents</div>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Variant</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->variant->size->name }} / {{ $item->variant->color->name }}</td>
                            <td>{{ $item->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
