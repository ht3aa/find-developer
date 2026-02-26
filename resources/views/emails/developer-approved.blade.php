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
        .message { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd; }
        .btn { display: inline-block; padding: 10px 20px; background-color: rgb(0, 49, 173); color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Developer Profile Approved</h1>
        </div>
        <div class="content">
            <p>Hello {{ $developer->name }},</p>
            <div class="message">
                <p>Congratulations! Your developer profile has been approved.</p>
            </div>
            <p>Best Regards,<br>Hasan Tahseen an Admin in {{ config('app.url') }} platform</p>
            <a href="{{ url('/developers/'.$developer->slug) }}" class="btn">View Your Profile</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Find Developer. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
