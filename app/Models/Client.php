<?php

namespace App\Models;

use App\DataMapper\CompanySettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'name',
        'website',
        'phone',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'industry_id',
        'size_id',
        'currency_id',
        'settings',
        'group_settings_id',
        'vat_number',
        'id_number',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
    ];

    protected $casts = [
        'settings' => 'object',
        'is_deleted' => 'boolean',
        'balance' => 'float',
        'paid_to_date' => 'float',
        'credit_balance' => 'float',
        'last_login' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function group_settings()
    {
        return $this->belongsTo(GroupSetting::class, 'group_settings_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function getSetting(string $setting)
    {
        if ($this->settings && property_exists($this->settings, $setting)) {
            $value = $this->settings->{$setting};
            if (! empty($value) || $value === 0 || $value === false) {
                return $value;
            }
        }

        if ($this->group_settings_id) {
            $group = $this->group_settings;
            if ($group && $group->settings && property_exists($group->settings, $setting)) {
                $value = $group->settings->{$setting};
                if (! empty($value) || $value === 0 || $value === false) {
                    return $value;
                }
            }
        }

        if ($this->company) {
            return $this->company->getSetting($setting);
        }

        $defaults = CompanySettings::defaults();

        if (property_exists($defaults, $setting)) {
            return $defaults->{$setting};
        }

        return null;
    }
}
