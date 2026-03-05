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
        .detail-row { display: flex; padding: 8px 0; border-bottom: 1px solid #eee; }
        .detail-row:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #666; width: 140px; flex-shrink: 0; }
        .value { color: #333; flex: 1; }
        .message-box { background: #f5f5f5; padding: 12px; border-radius: 5px; margin: 12px 0; white-space: pre-wrap; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>A Company Is Looking for Developers</h1>
        </div>
        <div class="content">
            <p>Hello {{ $developerName }},</p>
            <p>A company is looking for developers and your profile may meet their requirements.</p>
            <div class="details">
                <div class="detail-row">
                    <span class="label">Company:</span>
                    <span class="value">{{ $companyName }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Position:</span>
                    <span class="value">{{ $jobTitleName }}</span>
                </div>
                @if($salaryRange)
                <div class="detail-row">
                    <span class="label">Salary Range:</span>
                    <span class="value">{{ $salaryRange }}</span>
                </div>
                @endif
                @if($workType)
                <div class="detail-row">
                    <span class="label">Work Type:</span>
                    <span class="value">{{ $workType }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="label">Contact Email:</span>
                    <span class="value">{{ $contactEmail }}</span>
                </div>
            </div>
            @if($senderMessage)
            <p><strong>Message from the company:</strong></p>
            <div class="message-box">{{ $senderMessage }}</div>
            @endif
            <p>If you are interested in this opportunity, please reach out to the company at <strong>{{ $contactEmail }}</strong>.</p>
        </div>
        <div class="footer">
            <p>Best Regards,<br>{{ config('app.name') }} Team</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
