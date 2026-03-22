<?php

namespace App\Transformers;

use App\Models\Backup;
use League\Fractal\TransformerAbstract;

class BackupTransformer extends TransformerAbstract
{
    public function transform(Backup $backup): array
    {
        return [
            'id' => (int) $backup->id,
            'activity_id' => (int) $backup->activity_id,
            'amount' => (float) $backup->amount,
            'disk' => $backup->disk ?: '',
            'created_at' => $backup->created_at ? $backup->created_at->toDateTimeString() : '',
            'updated_at' => $backup->updated_at ? $backup->updated_at->toDateTimeString() : '',
        ];
    }
}
