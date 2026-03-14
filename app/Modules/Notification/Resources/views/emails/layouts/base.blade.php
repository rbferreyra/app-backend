<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f4f4f5;
            color: #18181b;
        }

        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
        }

        .header {
            background: #18181b;
            padding: 32px 40px;
            text-align: center;
        }

        .header img {
            height: 36px;
        }

        .header h1 {
            color: #ffffff;
            font-size: 18px;
            font-weight: 600;
            margin-top: 12px;
        }

        .body {
            padding: 40px;
        }

        .body p {
            font-size: 15px;
            line-height: 1.7;
            color: #3f3f46;
            margin-bottom: 16px;
        }

        .btn {
            display: inline-block;
            margin: 24px 0;
            padding: 12px 28px;
            background: #18181b;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        .divider {
            border: none;
            border-top: 1px solid #e4e4e7;
            margin: 32px 0;
        }

        .meta {
            font-size: 12px;
            color: #a1a1aa;
        }

        .footer {
            background: #f4f4f5;
            padding: 24px 40px;
            text-align: center;
            font-size: 12px;
            color: #a1a1aa;
            line-height: 1.6;
        }

        .footer a {
            color: #71717a;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="body">
            @yield('content')
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p><a href="{{ config('app.frontend_url') }}">{{ config('app.frontend_url') }}</a></p>
        </div>
    </div>
</body>

</html>
