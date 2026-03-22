<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyUser extends Model
{
    use SoftDeletes;

    protected $table = "company_user";

    protected $fillable = [
        "company_id", "user_id", "account_id", "permissions", "notifications",
        "settings", "is_owner", "is_admin", "is_locked", "react_settings",
    ];

    protected $casts = [
        "notifications" => "object", "settings" => "object", "react_settings" => "object",
        "permissions" => "string", "is_owner" => "boolean", "is_admin" => "boolean", "is_locked" => "boolean",
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function account() { return $this->belongsTo(Account::class); }
}
