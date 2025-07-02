<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Token</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            text-align: center;
            background-color: #007bff;
            padding: 10px;
            color: #ffffff;
            border-radius: 10px 10px 0 0;
        }
        .email-header h2 {
            margin: 0;
        }
        .email-body {
            padding: 20px;
            text-align: center;
        }
        .email-body h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }
        .email-body p {
            font-size: 16px;
            line-height: 1.5;
        }
        .email-footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666666;
            text-align: center;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h2>Two-Factor Authentication</h2>
        </div>
        <div class="email-body">
            <p>Hello {{ $user->name }},</p>
            <p>Here is your Two Factor Authentication Code:</p>
            <h1>{{ $token }}</h1>
        </div>
    </div>
</body>
</html>
