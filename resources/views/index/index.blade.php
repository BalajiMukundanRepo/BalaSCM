<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config("ninja.app_name", "InvoiceManager") }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; padding: 0; background: #f7f8fc; color: #333; }
        .container { max-width: 800px; margin: 80px auto; padding: 40px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        h1 { color: #2d3748; margin-bottom: 10px; }
        p { color: #718096; line-height: 1.6; }
        .links { margin-top: 30px; }
        .links a { display: inline-block; margin-right: 15px; padding: 10px 20px; background: #4a5568; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px; }
        .links a:hover { background: #2d3748; }
        .version { color: #a0aec0; font-size: 12px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ config("ninja.app_name", "InvoiceManager") }}</h1>
        <p>A Laravel-based invoice management system integrated with PhantomShield for supply chain finance operations.</p>
        <div class="links">
            <a href="/api/documentation">API Documentation</a>
            <a href="{{ route("login") }}">Login</a>
        </div>
        <div class="version">Version {{ config("ninja.app_version", "1.0.0") }}</div>
    </div>
    <div id="app"></div>
</body>
</html>
