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
        .developer { background: white; padding: 15px; border-radius: 5px; margin: 12px 0; border: 1px solid #ddd; }
        .developer-name { font-weight: bold; font-size: 1.1em; margin-bottom: 4px; }
        .developer-meta { font-size: 0.9em; color: #666; margin-bottom: 8px; }
        .recommended { display: inline-block; background-color: hsl(38, 85%, 92%); color: hsl(38, 92%, 40%); border: 1px solid hsl(38, 92%, 70%); padding: 2px 8px; border-radius: 4px; font-size: 0.85em; margin-left: 6px; font-weight: 600; }
        .btn { display: inline-block; padding: 8px 16px; background-color: hsl(38, 92%, 50%); color: hsl(38, 15%, 10%); text-decoration: none; border-radius: 5px; margin-top: 8px; font-size: 0.9em; font-weight: 500; }
        .btn:hover { text-decoration: underline; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .insight-box { border-left: 4px solid hsl(38, 92%, 50%); background-color: hsl(38, 25%, 97%); padding: 18px 20px; margin: 20px 0 24px; border-radius: 0 8px 8px 0; font-style: italic; color: #444; }
        .insight-box .insight-label { display: block; font-style: normal; font-size: 0.75em; text-transform: uppercase; letter-spacing: 0.08em; color: hsl(38, 92%, 40%); font-weight: 600; margin-bottom: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Developer spotlight</h1>
        </div>
        <div class="content">
            <p>Here are some available developers you might want to check out:</p>
            <div class="insight-box">
                <span class="insight-label">A quick tip</span>
                These developers are handpicked for their skills and fit. When you reach out to hire them, share the full picture—project scope, timeline, and what success looks like. The more detail you give, the better they can show you why they're the right choice.
            </div>
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
