<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: rgb(0, 49, 173); color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .developer { background: white; padding: 15px; border-radius: 5px; margin: 12px 0; border: 1px solid #ddd; }
        .developer-name { font-weight: bold; font-size: 1.1em; margin-bottom: 4px; }
        .developer-meta { font-size: 0.9em; color: #666; margin-bottom: 8px; }
        .recommended { display: inline-block; background-color: #0d9488; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.85em; margin-left: 6px; }
        .btn { display: inline-block; padding: 8px 16px; background-color: rgb(187, 187, 187); color: white; text-decoration: none; border-radius: 5px; margin-top: 8px; font-size: 0.9em; }
        .btn:hover { text-decoration: underline; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Developer spotlight</h1>
        </div>
        <div class="content">
            <p>Here are some available developers you might want to check out:</p>
            @foreach($developers as $developer)
                <div class="developer">
                    <div class="developer-name">
                        {{ $developer['name'] }}
                        @if($developer['recommended_by_us'] ?? false)
                            <span class="recommended">Recommended by us</span>
                        @endif
                    </div>
                    <div class="developer-meta">
                        @if($developer['job_title'] ?? null)
                            {{ $developer['job_title'] }}
                            ·
                        @endif
                        {{ $developer['years_of_experience'] }} year(s) of experience
                    </div>
                    <a href="{{ $developer['profile_url'] }}" class="btn">View profile</a>
                </div>
            @endforeach
            <p style="margin-top: 20px;">Good luck finding the right developer for your team!</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Find Developer. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
