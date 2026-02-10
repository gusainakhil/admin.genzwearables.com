<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed</title>
</head>
<body>
    <p>Hi {{ $order->user?->name ?? 'Customer' }},</p>
    <p>Your order has been placed successfully.</p>
    <p><strong>Order number:</strong> {{ $order->order_number }}</p>
    <p><strong>Total:</strong> {{ $order->total }}</p>
    <p><strong>Payment status:</strong> {{ $order->payment_status }}</p>
    <p>We will update you after payment confirmation.</p>
</body>
</html>
