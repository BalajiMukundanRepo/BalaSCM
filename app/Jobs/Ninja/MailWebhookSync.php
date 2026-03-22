<?php

namespace App\Jobs\Ninja;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MailWebhookSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $payload;

    public function __construct(array $payload = [])
    {
        $this->payload = $payload;
    }

    public function handle(): void
    {
        Log::info('Processing mail webhook event', ['payload' => $this->payload]);

        $eventType = $this->payload['event_type'] ?? null;

        switch ($eventType) {
            case 'bounce':
                $this->processBounce();
                break;
            case 'complaint':
                $this->processComplaint();
                break;
            case 'delivery':
                $this->processDelivery();
                break;
            default:
                Log::warning('Unknown mail webhook event type', ['type' => $eventType]);
        }
    }

    protected function processBounce(): void
    {
        Log::info('Processing bounce event', ['email' => $this->payload['email'] ?? 'unknown']);
    }

    protected function processComplaint(): void
    {
        Log::info('Processing complaint event', ['email' => $this->payload['email'] ?? 'unknown']);
    }

    protected function processDelivery(): void
    {
        Log::info('Processing delivery event', ['email' => $this->payload['email'] ?? 'unknown']);
    }
}
