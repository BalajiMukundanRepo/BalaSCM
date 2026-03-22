<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan',
        'plan_term',
        'plan_started',
        'plan_paid',
        'plan_expires',
        'default_company_id',
        'key',
    ];

    protected $casts = [
        'plan_started' => 'date',
        'plan_paid' => 'date',
        'plan_expires' => 'date',
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function default_company()
    {
        return $this->belongsTo(Company::class, 'default_company_id');
    }

    public function company_users()
    {
        return $this->hasMany(CompanyUser::class);
    }

    public function tokens()
    {
        return $this->hasMany(CompanyToken::class);
    }
}
