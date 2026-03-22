<?php

namespace Tests\Unit;

use App\DataMapper\ClientSettings;
use App\DataMapper\CompanySettings;
use Tests\TestCase;

class CompareObjectTest extends TestCase
{
    public function test_comparing_two_settings_objects(): void
    {
        $settings1 = CompanySettings::defaults();
        $settings2 = CompanySettings::defaults();

        $this->assertEquals($settings1, $settings2);
    }

    public function test_detecting_changed_properties(): void
    {
        $settings1 = CompanySettings::defaults();
        $settings2 = CompanySettings::defaults();
        $settings2->name = 'Changed Name';

        $this->assertNotEquals($settings1->name, $settings2->name);
        $this->assertEquals('Changed Name', $settings2->name);
    }

    public function test_handling_null_values(): void
    {
        $settings = ClientSettings::defaults();

        $this->assertNull($settings->currency_id);
        $this->assertNull($settings->language_id);
        $this->assertNull($settings->payment_terms);
    }

    public function test_company_settings_defaults_have_values(): void
    {
        $settings = CompanySettings::defaults();

        $this->assertEquals('1', $settings->currency_id);
        $this->assertEquals('1', $settings->language_id);
        $this->assertEquals('', $settings->name);
    }

    public function test_client_settings_override(): void
    {
        $clientSettings = ClientSettings::defaults();
        $clientSettings->currency_id = '5';

        $this->assertEquals('5', $clientSettings->currency_id);
    }
}
