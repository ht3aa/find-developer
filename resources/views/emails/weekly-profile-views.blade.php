<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Matches app UI: primary amber, primary-foreground dark */
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: hsl(38, 92%, 50%); color: hsl(38, 15%, 10%); padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .stats { background: white; padding: 20px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd; text-align: center; }
        .view-count { font-size: 2em; font-weight: bold; color: hsl(38, 92%, 40%); }
        .message { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd; }
        .btn { display: inline-block; padding: 10px 20px; background-color: hsl(38, 92%, 50%); color: hsl(38, 15%, 10%); text-decoration: none; border-radius: 5px; margin-top: 15px; font-weight: 500; }
        .btn:hover { text-decoration: underline; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your weekly profile stats</h1>
        </div>
        <div class="content">
            <p>Hello {{ $developerName }},</p>
            <div class="stats">
                <p style="margin: 0 0 8px 0; color: #666;">Profile views in the past week</p>
                <p class="view-count">{{ $viewCount }}</p>
            </div>
            <div class="message">
                <p>Keep your profile up to date to attract more opportunities! A fresh bio, recent projects, and updated skills help visitors get a better sense of what you offer.</p>
                <p>Take a moment to review your profile and add any new experience or projects.</p>
            </div>
            <a href="{{ $profileUrl }}" class="btn">Update your profile</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Find Developer. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
