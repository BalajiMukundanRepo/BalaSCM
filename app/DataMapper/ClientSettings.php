<?php

namespace App\DataMapper;

class ClientSettings
{
    public ?string $currency_id = null;
    public ?string $language_id = null;
    public ?string $payment_terms = null;
    public ?string $custom_value1 = null;
    public ?string $custom_value2 = null;
    public ?string $custom_value3 = null;
    public ?string $custom_value4 = null;

    public static function defaults(): self
    {
        return new self();
    }
}
