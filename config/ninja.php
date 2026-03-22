<?php

return [
    "app_name" => env("APP_NAME", "InvoiceManager"),
    "app_version" => trim(file_get_contents(base_path("VERSION.txt"))),
    "require_https" => env("REQUIRE_HTTPS", true),
    "app_url" => rtrim(env("APP_URL", ""), "/"),
    "production" => env("NINJA_PRODUCTION", false),
    "db" => [
        "default" => env("DB_CONNECTION", "mysql"),
        "multi_db_enabled" => env("MULTI_DB_ENABLED", false),
    ],
    "i18n" => [
        "timezone_id" => env("DEFAULT_TIMEZONE", 15),
        "country_id" => env("DEFAULT_COUNTRY", 840),
        "currency_id" => env("DEFAULT_CURRENCY", 1),
        "language_id" => env("DEFAULT_LANGUAGE", 1),
    ],
    "testvars" => [
        "username" => env("TEST_USERNAME", "user@example.com"),
        "password" => env("TEST_PASSWORD", "password"),
    ],
    "quotas" => [
        "daily_emails" => env("DAILY_EMAIL_QUOTA", 300),
    ],
];
