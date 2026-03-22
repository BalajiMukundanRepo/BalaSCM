<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "company_id", "user_id", "bank_integration_id", "transaction_id", "amount",
        "currency_id", "category_id", "category_type", "date", "bank_account_id",
        "description", "invoice_ids", "expense_id", "vendor_id", "status_id",
    ];

    protected $casts = ["is_deleted" => "boolean", "amount" => "float", "date" => "date"];

    public function company() { return $this->belongsTo(Company::class); }
    public function user() { return $this->belongsTo(User::class); }
}
