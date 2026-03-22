<?php

namespace App\Transformers;

use App\Models\BankTransaction;
use League\Fractal\TransformerAbstract;

class BankTransactionTransformer extends TransformerAbstract
{
    public function transform(BankTransaction $bankTransaction): array
    {
        return [
            'id' => (int) $bankTransaction->id,
            'amount' => (float) $bankTransaction->amount,
            'date' => $bankTransaction->date ? $bankTransaction->date->format('Y-m-d') : '',
            'description' => $bankTransaction->description ?: '',
            'status_id' => (int) $bankTransaction->status_id,
            'is_deleted' => (bool) $bankTransaction->is_deleted,
            'created_at' => $bankTransaction->created_at ? $bankTransaction->created_at->toDateTimeString() : '',
            'updated_at' => $bankTransaction->updated_at ? $bankTransaction->updated_at->toDateTimeString() : '',
        ];
    }
}
