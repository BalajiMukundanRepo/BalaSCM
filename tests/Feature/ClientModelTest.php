<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\GroupSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\MockAccountData;
use Tests\TestCase;

class ClientModelTest extends TestCase
{
    use MockAccountData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeTestData();
    }

    public function test_client_creation_with_valid_data(): void
    {
        $this->assertDatabaseHas('clients', [
            'name' => 'Test Client',
            'company_id' => $this->company->id,
        ]);

        $this->assertInstanceOf(Client::class, $this->client);
        $this->assertEquals('Test Client', $this->client->name);
    }

    public function test_client_settings_merge(): void
    {
        $companySettings = $this->company->settings;
        if (is_string($companySettings)) {
            $companySettings = json_decode($companySettings);
        }
        $companySettings = $companySettings ?: new \stdClass();
        $companySettings->currency_id = '1';
        $this->company->settings = $companySettings;
        $this->company->save();

        $this->client->settings = (object) ['currency_id' => '2'];
        $this->client->save();

        $this->client->refresh();
        $setting = $this->client->getSetting('currency_id');
        $this->assertEquals('2', $setting);
    }

    public function test_client_soft_delete(): void
    {
        $clientId = $this->client->id;
        $this->client->delete();

        $this->assertSoftDeleted('clients', ['id' => $clientId]);

        $found = Client::withTrashed()->find($clientId);
        $this->assertNotNull($found);
    }

    public function test_client_relationships(): void
    {
        $this->assertInstanceOf(Company::class, $this->client->company);
        $this->assertEquals($this->company->id, $this->client->company->id);
    }

    public function test_client_inherits_company_settings(): void
    {
        $companySettings = $this->company->settings;
        if (is_string($companySettings)) {
            $companySettings = json_decode($companySettings);
        }
        $companySettings = $companySettings ?: new \stdClass();
        $companySettings->language_id = '5';
        $this->company->settings = $companySettings;
        $this->company->save();

        $this->client->settings = (object) [];
        $this->client->save();
        $this->client->refresh();

        $setting = $this->client->getSetting('language_id');
        $this->assertEquals('5', $setting);
    }
}
