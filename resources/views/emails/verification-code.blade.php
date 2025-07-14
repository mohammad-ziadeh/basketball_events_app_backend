<p>Your verification code is: <strong>{{ $code }}</strong></p>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
            color: #333333;
        }

        .email-container {
            background-color: #ffffff;
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #222222;
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            margin: 0 0 20px;
        }

        .code-box {
            display: block;
            background-color: #222222;
            color: #ffffff;
            font-size: 22px;
            font-weight: bold;
            padding: 10px 20px;
            text-align: center;
            min-width: 180px;
            border-radius: 6px;
            letter-spacing: 4px;
            user-select: all;
            width: fit-content;
            margin: 0 auto 20px auto;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #999999;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <h1>Email Verification</h1>
        <p>Thank you for registering! Please use the verification code below to complete your sign-up process:</p>
        <br>
        <p class="code-box">{{ $code }}</p>
        <br>
        <p>If you did not request this code, please ignore this email.</p>
        <div class="footer">
            &copy; {{ date('Y') }} FIBA. All rights reserved.
        </div>
    </div>
</body>

</html>
