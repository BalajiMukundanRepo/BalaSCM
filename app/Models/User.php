<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'avatar',
        'signature',
        'oauth_user_id',
        'oauth_provider_id',
        'google_2fa_secret',
        'accepted_terms_version',
        'ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google_2fa_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot('permissions', 'notifications', 'settings', 'is_owner', 'is_admin', 'is_locked')
            ->withTimestamps();
    }

    public function company_users()
    {
        return $this->hasMany(CompanyUser::class);
    }

    public function tokens()
    {
        return $this->hasMany(CompanyToken::class);
    }

    public function getCompany(): ?Company
    {
        $companyUser = $this->company_users()->first();

        return $companyUser ? $companyUser->company : null;
    }

    public function isAdmin(): bool
    {
        $companyUser = $this->company_users()->first();

        return $companyUser ? (bool) $companyUser->is_admin : false;
    }

    public function isOwner(): bool
    {
        $companyUser = $this->company_users()->first();

        return $companyUser ? (bool) $companyUser->is_owner : false;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin() || $this->isOwner()) {
            return true;
        }

        $companyUser = $this->company_users()->first();

        if (! $companyUser) {
            return false;
        }

        if ($companyUser->is_locked) {
            return false;
        }

        $permissionsRaw = $companyUser->permissions;

        if (empty($permissionsRaw)) {
            return false;
        }

        $permissions = array_map('trim', explode(',', $permissionsRaw));

        return in_array($permission, $permissions);
    }
}
