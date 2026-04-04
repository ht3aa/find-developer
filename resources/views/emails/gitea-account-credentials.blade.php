<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #0d9488; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .credentials { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd; font-family: ui-monospace, monospace; font-size: 14px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your Gitea account</h1>
        </div>
        <div class="content">
            <p>Hello {{ $user->name }},</p>
            <p>A Gitea account has been created for you so you can collaborate on remote work repositories.</p>
            <div class="credentials">
                <p style="margin: 0 0 8px;"><strong>Username</strong><br>{{ $giteaUsername }}</p>
                <p style="margin: 0;"><strong>Temporary password</strong><br>{{ $temporaryPassword }}</p>
            </div>
            <p>You must change this password when you first sign in to Gitea.</p>
            @if(filled($giteaUrl))
                <p><a href="{{ rtrim($giteaUrl, '/') }}">Open Gitea</a></p>
            @endif
            <p>Best regards,<br>{{ config('app.name') }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
