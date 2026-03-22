<?php

namespace App\Transformers;

use App\Models\CompanyGateway;
use League\Fractal\TransformerAbstract;

class CompanyGatewayTransformer extends TransformerAbstract
{
    public function transform(CompanyGateway $gateway): array
    {
        return [
            'id' => (int) $gateway->id,
            'gateway_key' => $gateway->gateway_key ?: '',
            'accepted_credit_cards' => (int) $gateway->accepted_credit_cards,
            'require_cvv' => (bool) $gateway->require_cvv,
            'label' => $gateway->label ?: '',
            'token_billing' => $gateway->token_billing ?: 'off',
            'is_deleted' => (bool) $gateway->is_deleted,
            'created_at' => $gateway->created_at ? $gateway->created_at->toDateTimeString() : '',
            'updated_at' => $gateway->updated_at ? $gateway->updated_at->toDateTimeString() : '',
        ];
    }
}
