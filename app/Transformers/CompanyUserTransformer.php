<?php

namespace App\Transformers;

use App\Models\CompanyUser;
use League\Fractal\TransformerAbstract;

class CompanyUserTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'user',
        'company',
        'account',
    ];

    public function transform(CompanyUser $companyUser): array
    {
        return [
            'permissions' => $companyUser->permissions ?: '',
            'notifications' => $companyUser->notifications ?: new \stdClass(),
            'settings' => $companyUser->settings ?: new \stdClass(),
            'is_owner' => (bool) $companyUser->is_owner,
            'is_admin' => (bool) $companyUser->is_admin,
            'is_locked' => (bool) $companyUser->is_locked,
            'react_settings' => $companyUser->react_settings ?: new \stdClass(),
            'updated_at' => $companyUser->updated_at ? $companyUser->updated_at->toDateTimeString() : '',
        ];
    }

    public function includeUser(CompanyUser $companyUser)
    {
        $user = $companyUser->user;

        return $user ? $this->item($user, new UserTransformer()) : $this->null();
    }

    public function includeCompany(CompanyUser $companyUser)
    {
        $company = $companyUser->company;

        return $company ? $this->item($company, new CompanyTransformer()) : $this->null();
    }

    public function includeAccount(CompanyUser $companyUser)
    {
        $account = $companyUser->account;

        return $account ? $this->item($account, new AccountTransformer()) : $this->null();
    }
}
