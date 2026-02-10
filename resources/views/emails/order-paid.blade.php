<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Paid</title>
</head>
<body>
    <p>Hi {{ $order->user?->name ?? 'Customer' }},</p>
    <p>We have received your payment.</p>
    <p><strong>Order number:</strong> {{ $order->order_number }}</p>
    <p><strong>Total:</strong> {{ $order->total }}</p>
    <p><strong>Payment status:</strong> {{ $order->payment_status }}</p>
    <p>Thank you for your purchase.</p>
</body>
</html>
