<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: hsl(38, 92%, 50%); color: hsl(38, 15%, 10%); padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .message { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd; }
        .btn { display: inline-block; padding: 10px 20px; background-color: hsl(38, 92%, 50%); color: hsl(38, 15%, 10%); text-decoration: none; border-radius: 5px; margin-top: 15px; font-weight: 500; }
        .btn:hover { text-decoration: underline; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Unread Messages</h1>
        </div>
        <div class="content">
            <p>Hello {{ $recipient->name }},</p>
            <div class="message">
                <p>You have <strong>{{ $unreadCount }} {{ Str::plural('unread message', $unreadCount) }}</strong> on Find Developer.</p>
                <p>Log in to view and reply to your messages.</p>
            </div>
            <a href="{{ route('messages.index') }}" class="btn">View Messages</a>
            <p style="margin-top: 20px;">Best Regards,<br>The Find Developer Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Find Developer. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
