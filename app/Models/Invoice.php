<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_DRAFT = 1;
    const STATUS_SENT = 2;
    const STATUS_PARTIAL = 3;
    const STATUS_PAID = 4;
    const STATUS_CANCELLED = 5;
    const STATUS_OVERDUE = -1;

    protected $fillable = [
        "company_id", "client_id", "user_id", "status_id", "number", "discount",
        "is_amount_discount", "po_number", "date", "due_date", "line_items", "footer",
        "public_notes", "private_notes", "terms", "tax_name1", "tax_rate1", "tax_name2",
        "tax_rate2", "tax_name3", "tax_rate3", "custom_value1", "custom_value2",
        "custom_value3", "custom_value4", "amount", "balance", "partial",
        "partial_due_date", "exchange_rate", "last_sent_date", "next_send_date", "subscription_id",
    ];

    protected $casts = [
        "line_items" => "object", "backup" => "object", "is_amount_discount" => "boolean",
        "is_deleted" => "boolean", "date" => "date", "due_date" => "date",
        "partial_due_date" => "date", "last_sent_date" => "date", "last_viewed" => "datetime",
        "next_send_date" => "date", "amount" => "float", "balance" => "float",
        "partial" => "float", "exchange_rate" => "float", "paid_to_date" => "float",
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function activities() { return $this->hasMany(Activity::class); }
}
