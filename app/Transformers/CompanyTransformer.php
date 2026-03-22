<?php

namespace App\Transformers;

use App\Models\Company;
use League\Fractal\TransformerAbstract;

class CompanyTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'users',
        'clients',
        'invoices',
        'payments',
        'products',
        'activities',
        'groups',
        'company_gateways',
        'tokens',
        'bank_transactions',
    ];

    public function transform(Company $company): array
    {
        return [
            'id' => (int) $company->id,
            'name' => $company->name ?: '',
            'settings' => $company->settings ?: new \stdClass(),
            'custom_fields' => $company->custom_fields ?: new \stdClass(),
            'size_id' => $company->size_id ? (int) $company->size_id : null,
            'industry_id' => $company->industry_id ? (int) $company->industry_id : null,
            'is_large' => (bool) $company->is_large,
            'is_disabled' => (bool) $company->is_disabled,
            'portal_mode' => $company->portal_mode ?: 'domain',
            'portal_domain' => $company->portal_domain ?: '',
            'subdomain' => $company->subdomain ?: '',
            'enabled_modules' => (int) $company->enable_modules,
            'created_at' => $company->created_at ? $company->created_at->toDateTimeString() : '',
            'updated_at' => $company->updated_at ? $company->updated_at->toDateTimeString() : '',
        ];
    }

    public function includeUsers(Company $company)
    {
        return $this->collection($company->users, new UserTransformer());
    }

    public function includeClients(Company $company)
    {
        return $this->collection($company->clients, new ClientTransformer());
    }

    public function includeInvoices(Company $company)
    {
        return $this->collection($company->invoices, new InvoiceTransformer());
    }

    public function includePayments(Company $company)
    {
        return $this->collection($company->payments, new PaymentTransformer());
    }

    public function includeProducts(Company $company)
    {
        return $this->collection($company->products, new ProductTransformer());
    }

    public function includeActivities(Company $company)
    {
        return $this->collection($company->activities, new ActivityTransformer());
    }

    public function includeGroups(Company $company)
    {
        return $this->collection($company->groups, new GroupSettingTransformer());
    }

    public function includeCompanyGateways(Company $company)
    {
        return $this->collection($company->company_gateways, new CompanyGatewayTransformer());
    }

    public function includeTokens(Company $company)
    {
        return $this->collection($company->tokens, new CompanyTokenTransformer());
    }

    public function includeBankTransactions(Company $company)
    {
        return $this->collection($company->bank_transactions, new BankTransactionTransformer());
    }
}
