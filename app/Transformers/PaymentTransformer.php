<?php

namespace App\Transformers;

use App\Models\Payment;
use League\Fractal\TransformerAbstract;

class PaymentTransformer extends TransformerAbstract
{
    public function transform(Payment $payment): array
    {
        return [
            'id' => (int) $payment->id,
            'status_id' => (int) $payment->status_id,
            'amount' => (float) $payment->amount,
            'refunded' => (float) $payment->refunded,
            'applied' => (float) $payment->applied,
            'date' => $payment->date ? $payment->date->format('Y-m-d') : '',
            'number' => $payment->number ?: '',
            'transaction_reference' => $payment->transaction_reference ?: '',
            'is_deleted' => (bool) $payment->is_deleted,
            'created_at' => $payment->created_at ? $payment->created_at->toDateTimeString() : '',
            'updated_at' => $payment->updated_at ? $payment->updated_at->toDateTimeString() : '',
        ];
    }
}
