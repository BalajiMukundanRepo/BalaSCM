<?php

namespace App\Models;

use App\DataMapper\CompanySettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'name',
        'ip',
        'settings',
        'industry_id',
        'size_id',
        'is_large',
        'is_disabled',
        'enable_modules',
        'portal_mode',
        'portal_domain',
        'subdomain',
        'slack_webhook_url',
        'google_analytics_key',
        'custom_fields',
        'expense_mailbox',
        'expense_mailbox_active',
        'tax_data',
        'origin_tax_data',
    ];

    protected $casts = [
        'settings' => 'object',
        'custom_fields' => 'object',
        'tax_data' => 'object',
        'origin_tax_data' => 'object',
        'is_large' => 'boolean',
        'is_disabled' => 'boolean',
        'is_deleted' => 'boolean',
        'expense_mailbox_active' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot('permissions', 'notifications', 'settings', 'is_owner', 'is_admin', 'is_locked')
            ->withTimestamps();
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function groups()
    {
        return $this->hasMany(GroupSetting::class);
    }

    public function company_gateways()
    {
        return $this->hasMany(CompanyGateway::class);
    }

    public function tokens()
    {
        return $this->hasMany(CompanyToken::class);
    }

    public function bank_transactions()
    {
        return $this->hasMany(BankTransaction::class);
    }

    public function company_users()
    {
        return $this->hasMany(CompanyUser::class);
    }

    public function getSetting(string $setting)
    {
        if ($this->settings && property_exists($this->settings, $setting)) {
            return $this->settings->{$setting};
        }

        $defaults = CompanySettings::defaults();

        if (property_exists($defaults, $setting)) {
            return $defaults->{$setting};
        }

        return null;
    }

    public function getSettingEntity(string $setting)
    {
        if ($this->settings && property_exists($this->settings, $setting)) {
            return $this;
        }

        return null;
    }
}
