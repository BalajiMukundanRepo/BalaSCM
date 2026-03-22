<?php

namespace App\Transformers;

use App\Models\Invoice;
use League\Fractal\TransformerAbstract;

class InvoiceTransformer extends TransformerAbstract
{
    public function transform(Invoice $invoice): array
    {
        return [
            'id' => (int) $invoice->id,
            'status_id' => (int) $invoice->status_id,
            'number' => $invoice->number ?: '',
            'amount' => (float) $invoice->amount,
            'balance' => (float) $invoice->balance,
            'date' => $invoice->date ? $invoice->date->format('Y-m-d') : '',
            'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '',
            'is_deleted' => (bool) $invoice->is_deleted,
            'created_at' => $invoice->created_at ? $invoice->created_at->toDateTimeString() : '',
            'updated_at' => $invoice->updated_at ? $invoice->updated_at->toDateTimeString() : '',
        ];
    }
}
