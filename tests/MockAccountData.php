<?php

namespace Tests;

use App\Models\Account;
use App\Models\Client;
use App\Models\Company;
use App\Models\CompanyToken;
use App\Models\CompanyUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait MockAccountData
{
    public $account;

    public $company;

    public $user;

    public $client;

    public $token;

    public function makeTestData()
    {
        $this->account = Account::create([
            'plan' => 'pro',
            'plan_term' => 'year',
            'key' => 'test-account-key-'.uniqid(),
        ]);

        $this->company = Company::create([
            'account_id' => $this->account->id,
            'name' => 'Test Company',
            'settings' => \App\DataMapper\CompanySettings::defaults(),
            'custom_fields' => (object) [],
            'tax_data' => (object) [],
            'origin_tax_data' => (object) [],
        ]);

        $this->account->default_company_id = $this->company->id;
        $this->account->save();

        $this->user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test'.uniqid().'@example.com',
            'password' => Hash::make('password'),
        ]);

        CompanyUser::create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'is_admin' => true,
            'is_owner' => true,
            'permissions' => '',
            'notifications' => (object) [],
            'settings' => (object) [],
            'react_settings' => (object) [],
        ]);

        $this->token = CompanyToken::create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'name' => 'test-token',
            'token' => 'test-token-'.uniqid(),
            'is_system' => true,
        ]);

        $this->client = Client::create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'name' => 'Test Client',
            'address1' => '123 Test St',
            'city' => 'Test City',
            'state' => 'TS',
            'postal_code' => '12345',
        ]);
    }
}
