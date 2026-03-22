<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyToken extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "company_tokens";
    protected $fillable = ["company_id", "user_id", "account_id", "token", "name", "is_system"];
    protected $casts = ["is_system" => "boolean"];

    public function company() { return $this->belongsTo(Company::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function account() { return $this->belongsTo(Account::class); }
}
