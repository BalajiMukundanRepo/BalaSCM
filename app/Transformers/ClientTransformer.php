<?php

namespace App\Transformers;

use App\Models\Client;
use League\Fractal\TransformerAbstract;

class ClientTransformer extends TransformerAbstract
{
    public function transform(Client $client): array
    {
        return [
            'id' => (int) $client->id,
            'name' => $client->name ?: '',
            'website' => $client->website ?: '',
            'phone' => $client->phone ?: '',
            'address1' => $client->address1 ?: '',
            'address2' => $client->address2 ?: '',
            'city' => $client->city ?: '',
            'state' => $client->state ?: '',
            'postal_code' => $client->postal_code ?: '',
            'country_id' => $client->country_id ? (int) $client->country_id : null,
            'balance' => (float) $client->balance,
            'paid_to_date' => (float) $client->paid_to_date,
            'credit_balance' => (float) $client->credit_balance,
            'custom_value1' => $client->custom_value1 ?: '',
            'custom_value2' => $client->custom_value2 ?: '',
            'custom_value3' => $client->custom_value3 ?: '',
            'custom_value4' => $client->custom_value4 ?: '',
            'vat_number' => $client->vat_number ?: '',
            'id_number' => $client->id_number ?: '',
            'is_deleted' => (bool) $client->is_deleted,
            'created_at' => $client->created_at ? $client->created_at->toDateTimeString() : '',
            'updated_at' => $client->updated_at ? $client->updated_at->toDateTimeString() : '',
        ];
    }
}
