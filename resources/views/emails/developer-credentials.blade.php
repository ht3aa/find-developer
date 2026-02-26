<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: rgb(0, 49, 173); color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .credentials { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd; }
        .label { font-weight: bold; color: #666; }
        .value { color: #333; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .btn { display: inline-block; padding: 10px 20px; background-color: rgb(0, 49, 173); color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Find Developer!</h1>
        </div>
        <div class="content">
            <p>Hello {{ $developer->name }},</p>
            <p>Thank you for registering as a developer on Find Developer. Your account has been created successfully!</p>
            <div class="credentials">
                <p><span class="label">Email:</span> <span class="value">{{ $developer->email }}</span></p>
                <p><span class="label">Temporary Password:</span> <span class="value">{{ $password }}</span></p>
            </div>
            <p>Please login and change your password immediately.</p>
            <a href="{{ url('/login') }}" class="btn">Login to your account</a>
            <p>Your profile is currently pending approval. You will be notified once it has been reviewed.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Find Developer. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
