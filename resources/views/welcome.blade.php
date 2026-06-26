<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            text-align: center;
        }

        .badge {
            display: inline-block;
            background: #1e293b;
            border: 1px solid #334155;
            color: #94a3b8;
            font-size: 12px;
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 24px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        h1 {
            font-size: 48px;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 16px;
        }

        h1 span {
            color: #6366f1;
        }

        p {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 40px;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #1e293b;
            border: 1px solid #334155;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            color: #94a3b8;
        }

        .dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="badge">Backend API</div>
    <h1>Welcome to <span>{{ config('app.name') }}</span></h1>
    <p>The backend application is up and running.</p>
    <div class="status">
        <div class="dot"></div>
        All systems operational
    </div>
</div>
</body>
</html>
