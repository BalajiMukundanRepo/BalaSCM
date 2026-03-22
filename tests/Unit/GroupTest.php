<?php

namespace Tests\Unit;

use App\DataMapper\CompanySettings;
use App\Models\Client;
use App\Models\GroupSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\MockAccountData;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use MockAccountData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeTestData();
    }

    public function test_group_settings_creation(): void
    {
        $group = GroupSetting::create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'name' => 'Test Group',
            'settings' => (object) ['currency_id' => '3'],
        ]);

        $this->assertDatabaseHas('group_settings', [
            'id' => $group->id,
            'name' => 'Test Group',
        ]);
    }

    public function test_client_inherits_group_settings(): void
    {
        $group = GroupSetting::create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'name' => 'Test Group',
            'settings' => (object) ['currency_id' => '7'],
        ]);

        $this->client->group_settings_id = $group->id;
        $this->client->settings = (object) [];
        $this->client->save();
        $this->client->refresh();

        $setting = $this->client->getSetting('currency_id');
        $this->assertEquals('7', $setting);
    }

    public function test_client_settings_override_group_settings(): void
    {
        $group = GroupSetting::create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'name' => 'Test Group',
            'settings' => (object) ['currency_id' => '7'],
        ]);

        $this->client->group_settings_id = $group->id;
        $this->client->settings = (object) ['currency_id' => '9'];
        $this->client->save();
        $this->client->refresh();

        $setting = $this->client->getSetting('currency_id');
        $this->assertEquals('9', $setting);
    }

    public function test_settings_cascade_company_group_client(): void
    {
        $companySettings = CompanySettings::defaults();
        $companySettings->language_id = '10';
        $this->company->settings = $companySettings;
        $this->company->save();

        $group = GroupSetting::create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'name' => 'Test Group',
            'settings' => (object) [],
        ]);

        $this->client->group_settings_id = $group->id;
        $this->client->settings = (object) [];
        $this->client->save();
        $this->client->refresh();

        $setting = $this->client->getSetting('language_id');
        $this->assertEquals('10', $setting);
    }
}
