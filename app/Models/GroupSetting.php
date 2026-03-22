<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ["company_id", "user_id", "name", "settings"];
    protected $casts = ["settings" => "object", "is_deleted" => "boolean"];

    public function company() { return $this->belongsTo(Company::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function clients() { return $this->hasMany(Client::class, "group_settings_id"); }
}
