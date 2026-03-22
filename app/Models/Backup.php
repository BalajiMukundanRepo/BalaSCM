<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = ["activity_id", "html", "amount", "disk"];
    protected $casts = ["amount" => "float"];

    public function activity() { return $this->belongsTo(Activity::class); }
}
