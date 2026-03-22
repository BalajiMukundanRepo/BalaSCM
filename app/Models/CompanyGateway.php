<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyGateway extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "company_id", "user_id", "gateway_key", "accepted_credit_cards", "require_cvv",
        "require_billing_address", "require_shipping_address", "config", "fees_and_limits",
        "custom_value1", "custom_value2", "custom_value3", "custom_value4", "label", "token_billing",
    ];

    protected $casts = [
        "fees_and_limits" => "object", "require_cvv" => "boolean",
        "require_billing_address" => "boolean", "require_shipping_address" => "boolean",
        "is_deleted" => "boolean",
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function user() { return $this->belongsTo(User::class); }
}
