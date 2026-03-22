<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "company_id", "user_id", "product_key", "notes", "cost", "price", "quantity",
        "tax_name1", "tax_rate1", "tax_name2", "tax_rate2", "tax_name3", "tax_rate3",
        "custom_value1", "custom_value2", "custom_value3", "custom_value4",
        "in_stock_quantity", "stock_notification", "stock_notification_threshold",
        "max_quantity", "product_image",
    ];

    protected $casts = [
        "is_deleted" => "boolean", "stock_notification" => "boolean",
        "cost" => "float", "price" => "float", "quantity" => "float",
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function user() { return $this->belongsTo(User::class); }
}
