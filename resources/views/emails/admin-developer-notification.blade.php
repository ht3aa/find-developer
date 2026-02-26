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
        .details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd; }
        .detail-row { display: flex; padding: 8px 0; border-bottom: 1px solid #eee; }
        .detail-row:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #666; width: 140px; }
        .value { color: #333; flex: 1; }
        .btn { display: inline-block; padding: 10px 20px; background-color: rgb(207, 207, 207); color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $subject }}</h1>
        </div>
        <div class="content">
            <p>A developer action has occurred. Here are the details:</p>
            <div class="details">
                <div class="detail-row">
                    <span class="label">Name:</span>
                    <span class="value">{{ $developer->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Email:</span>
                    <span class="value">{{ $developer->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Status:</span>
                    <span class="value">{{ $developer->status->getLabel() }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Years of Experience:</span>
                    <span class="value">{{ $developer->years_of_experience }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Job Title:</span>
                    <span class="value">{{ $developer->jobTitle?->name ?? 'N/A' }}</span>
                </div>
            </div>
            <a href="{{ route('developers.edit', $developer->slug) }}" class="btn">View Developer Profile</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Find Developer. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
