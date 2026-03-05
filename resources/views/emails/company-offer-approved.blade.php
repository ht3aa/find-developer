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
        .details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd; }
        .developer-list { margin: 8px 0; }
        .developer-item { margin: 12px 0; padding-bottom: 12px; border-bottom: 1px solid #eee; }
        .developer-item:last-child { border-bottom: none; padding-bottom: 0; }
        .developer-link { color: hsl(38, 92%, 40%); text-decoration: none; font-weight: 500; }
        .developer-meta { display: block; font-size: 13px; color: #666; margin-top: 4px; }
        .developer-link:hover { text-decoration: underline; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Developers You May Be Interested In</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>We have developers on our platform that match your needs for the position of <strong>{{ $jobTitleName }}</strong>. Here are developers you may be interested in connecting with:</p>
            <div class="details">
                <div class="developer-list">
                    @foreach($developers as $developer)
                    <div class="developer-item">
                        @if($developer['profile_url'] ?? null)
                        <a href="{{ $developer['profile_url'] }}" class="developer-link">{{ $developer['name'] }}</a>
                        @else
                        <strong>{{ $developer['name'] }}</strong>
                        @endif
                        @php
                            $metaParts = array_filter([
                                $developer['job_title'] ?? null,
                                isset($developer['years_of_experience']) && $developer['years_of_experience'] !== null
                                    ? $developer['years_of_experience'] . ' ' . Str::plural('year', $developer['years_of_experience']) . ' of experience'
                                    : null,
                            ]);
                        @endphp
                        @if(!empty($metaParts))
                        <span class="developer-meta">{{ implode(' · ', $metaParts) }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            <p>These developers have been notified and may reach out to you at <strong>{{ $contactEmail }}</strong> if they are interested in the opportunity.</p>
        </div>
        <div class="footer">
            <p>Best Regards,<br>{{ config('app.name') }} Team</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
