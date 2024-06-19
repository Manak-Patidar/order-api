
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP is Ready</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #3498db;
        }

        p {
            margin-bottom: 20px;
        }

        .cta-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            color: #fff;
            background-color: #3498db;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your OTP is Ready</h1>
        <p>Your OTP is: <strong>{{ $otp }}</strong></p>
        <p>Please use this OTP to complete your action.</p>
        <p>If you didn't request this OTP, please ignore this email.</p>
        <a href="#" class="cta-button">{{ $otp }}</a>
    </div>
</body>
</html>
