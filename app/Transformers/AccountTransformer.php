<?php

namespace App\Transformers;

use App\Models\Account;
use League\Fractal\TransformerAbstract;

class AccountTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'companies',
        'default_company',
    ];

    public function transform(Account $account): array
    {
        return [
            'id' => (int) $account->id,
            'plan' => $account->plan ?: '',
            'plan_term' => $account->plan_term ?: '',
            'default_company_id' => (int) $account->default_company_id,
            'key' => $account->key ?: '',
            'created_at' => $account->created_at ? $account->created_at->toDateTimeString() : '',
            'updated_at' => $account->updated_at ? $account->updated_at->toDateTimeString() : '',
        ];
    }

    public function includeCompanies(Account $account)
    {
        return $this->collection($account->companies, new CompanyTransformer());
    }

    public function includeDefaultCompany(Account $account)
    {
        $company = $account->default_company;

        if (! $company) {
            return $this->null();
        }

        return $this->item($company, new CompanyTransformer());
    }
}
