<?php

namespace App\Transformers;

use App\Models\Activity;
use League\Fractal\TransformerAbstract;

class ActivityTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'user',
        'client',
        'invoice',
        'payment',
        'backup',
    ];

    public function transform(Activity $activity): array
    {
        return [
            'id' => (int) $activity->id,
            'activity_type_id' => (int) $activity->activity_type_id,
            'client_id' => $activity->client_id ? (int) $activity->client_id : null,
            'invoice_id' => $activity->invoice_id ? (int) $activity->invoice_id : null,
            'payment_id' => $activity->payment_id ? (int) $activity->payment_id : null,
            'user_id' => $activity->user_id ? (int) $activity->user_id : null,
            'notes' => $activity->notes ?: '',
            'ip' => $activity->ip ?: '',
            'is_system' => (bool) $activity->is_system,
            'created_at' => $activity->created_at ? $activity->created_at->toDateTimeString() : '',
            'updated_at' => $activity->updated_at ? $activity->updated_at->toDateTimeString() : '',
        ];
    }

    public function includeUser(Activity $activity)
    {
        $user = $activity->user;

        return $user ? $this->item($user, new UserTransformer()) : $this->null();
    }

    public function includeClient(Activity $activity)
    {
        $client = $activity->client;

        return $client ? $this->item($client, new ClientTransformer()) : $this->null();
    }

    public function includeInvoice(Activity $activity)
    {
        $invoice = $activity->invoice;

        return $invoice ? $this->item($invoice, new InvoiceTransformer()) : $this->null();
    }

    public function includePayment(Activity $activity)
    {
        $payment = $activity->payment;

        return $payment ? $this->item($payment, new PaymentTransformer()) : $this->null();
    }

    public function includeBackup(Activity $activity)
    {
        $backup = $activity->backup;

        return $backup ? $this->item($backup, new BackupTransformer()) : $this->null();
    }
}
