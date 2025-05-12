<!DOCTYPE html>
<html>
<head>
    <title>OTP Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        h1 {
            color: #007BFF;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
<div class="email-container">
    <h1>Your OTP Code</h1>
    <p>Dear user,</p>
    <p>Your OTP code is: <strong>{{ $otp }}</strong></p>
    <p>This code is valid for 10 minutes. Please do not share it with anyone.</p>
    <p>If you didn’t request this, please ignore this email.</p>
    <hr>
    <p class="footer">Thank you for using our service! <br> &copy; {{ date('Y') }} YourCompanyName.</p>
</div>
</body>
</html>
