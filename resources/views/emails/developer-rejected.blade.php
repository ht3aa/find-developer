<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #dc2626; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .message { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd; }
        .reason { background: #fef2f2; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #dc2626; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Developer Profile Rejected</h1>
        </div>
        <div class="content">
            <p>Hello {{ $developer->name }},</p>
            <div class="message">
                <p>Unfortunately, your developer profile has been rejected.</p>
                @if(!empty($reason))
                <div class="reason">
                    <p><strong>Reason:</strong></p>
                    <p>{{ nl2br(e($reason)) }}</p>
                </div>
                @endif
                <p>If you believe this was done in error or would like to appeal this decision, please contact us at <a href="mailto:ht3aa2001@gmail.com">ht3aa2001@gmail.com</a>.</p>
            </div>
            <p>Best Regards,<br>{{ config('app.name') }} Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Find Developer. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
