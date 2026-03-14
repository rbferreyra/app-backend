<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>{{ $subject ?? 'Security Alert' }}</title>
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
            background: #dc2626;
            padding: 32px 40px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            font-size: 18px;
            font-weight: 600;
        }

        .header p {
            color: #fecaca;
            font-size: 13px;
            margin-top: 6px;
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

        .alert-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 16px 20px;
            margin: 24px 0;
        }

        .alert-box p {
            margin: 0;
            font-size: 14px;
            color: #b91c1c;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }

        .meta-table td {
            padding: 8px 0;
            font-size: 13px;
            color: #52525b;
            border-bottom: 1px solid #f4f4f5;
        }

        .meta-table td:first-child {
            font-weight: 600;
            width: 40%;
        }

        .btn {
            display: inline-block;
            margin: 24px 0;
            padding: 12px 28px;
            background: #dc2626;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-secondary {
            background: #18181b;
        }

        .divider {
            border: none;
            border-top: 1px solid #e4e4e7;
            margin: 32px 0;
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
            <h1>⚠️ Security Alert</h1>
            <p>{{ config('app.name') }}</p>
        </div>
        <div class="body">
            @yield('content')
        </div>
        <div class="footer">
            <p>If you did not perform this action, please <a href="{{ config('app.frontend_url') }}/support">contact
                    support</a> immediately.</p>
            <p style="margin-top:8px">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
