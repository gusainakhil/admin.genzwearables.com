<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} OTP Verification</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <p>Hello,</p>
    <p>Your OTP for signup on <strong>{{ $brandName }}</strong> is:</p>
    <p style="font-size: 24px; font-weight: 700; letter-spacing: 4px; margin: 12px 0;">{{ $otp }}</p>
    <p>This OTP is valid for 10 minutes.</p>
    <p>If you did not request this, please ignore this email.</p>
    <p>Thanks,<br>{{ $brandName }}</p>
</body>
</html>
