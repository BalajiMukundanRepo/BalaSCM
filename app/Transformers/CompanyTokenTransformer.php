<?php

namespace App\Transformers;

use App\Models\CompanyToken;
use League\Fractal\TransformerAbstract;

class CompanyTokenTransformer extends TransformerAbstract
{
    public function transform(CompanyToken $token): array
    {
        return [
            'id' => (int) $token->id,
            'name' => $token->name ?: '',
            'token' => $token->token ?: '',
            'is_system' => (bool) $token->is_system,
            'created_at' => $token->created_at ? $token->created_at->toDateTimeString() : '',
            'updated_at' => $token->updated_at ? $token->updated_at->toDateTimeString() : '',
        ];
    }
}
