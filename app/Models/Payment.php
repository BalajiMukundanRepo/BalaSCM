<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 4;
    const STATUS_PARTIALLY_REFUNDED = 5;
    const STATUS_REFUNDED = 6;

    protected $fillable = [
        "company_id", "client_id", "user_id", "status_id", "type_id", "amount",
        "refunded", "applied", "date", "transaction_reference", "number", "private_notes",
        "custom_value1", "custom_value2", "custom_value3", "custom_value4",
        "is_manual", "exchange_rate", "currency_id", "exchange_currency_id",
    ];

    protected $casts = [
        "is_deleted" => "boolean", "is_manual" => "boolean", "date" => "date",
        "amount" => "float", "refunded" => "float", "applied" => "float", "exchange_rate" => "float",
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function activities() { return $this->hasMany(Activity::class); }
}
