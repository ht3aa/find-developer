<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 – {{ config('app.name') }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background-color: hsl(40 20% 99%);
            color: hsl(220 15% 12%);
        }
        @media (prefers-color-scheme: dark) {
            body { background-color: hsl(222 18% 9%); color: hsl(40 20% 96%); }
            .code { color: hsl(38 92% 55%); }
            p { color: hsl(40 15% 62%); }
            a { color: hsl(222 18% 8%); background-color: hsl(38 92% 55%); }
        }
        .card {
            text-align: center;
            padding: 2rem 2.5rem;
            max-width: 28rem;
        }
        .code {
            font-size: 4rem;
            font-weight: 600;
            line-height: 1;
            color: hsl(38 92% 50%);
        }
        .dark .code { color: hsl(38 92% 55%); }
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 1rem 0 0.5rem;
        }
        p {
            color: hsl(220 10% 42%);
            margin: 0 0 1.5rem;
            line-height: 1.5;
        }
        .dark p { color: hsl(40 15% 62%); }
        a {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            color: hsl(220 15% 10%);
            background-color: hsl(38 92% 50%);
            border-radius: 0.5rem;
            text-decoration: none;
            transition: opacity 0.15s;
        }
        a:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="card">
        <div class="code" aria-hidden="true">500</div>
        <h1>Something went wrong</h1>
        <p>We're sorry. An unexpected error occurred. Please try again later.</p>
        <a href="{{ url('/') }}">Back to home</a>
    </div>
</body>
</html>
