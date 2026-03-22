<?php

namespace App\Jobs\Ninja;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AdjustEmailQuota implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Log::info('Adjusting email quotas for all companies');

        Company::query()->chunk(100, function ($companies) {
            foreach ($companies as $company) {
                $settings = $company->settings;

                if ($settings && property_exists($settings, 'email_quota_count')) {
                    $settings->email_quota_count = 0;
                    $company->settings = $settings;
                    $company->save();
                }
            }
        });

        Log::info('Email quota adjustment complete');
    }
}
