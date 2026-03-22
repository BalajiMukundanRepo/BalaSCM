<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'company_users',
    ];

    public function transform(User $user): array
    {
        return [
            'id' => (int) $user->id,
            'first_name' => $user->first_name ?: '',
            'last_name' => $user->last_name ?: '',
            'email' => $user->email ?: '',
            'phone' => $user->phone ?: '',
            'custom_value1' => $user->custom_value1 ?: '',
            'custom_value2' => $user->custom_value2 ?: '',
            'custom_value3' => $user->custom_value3 ?: '',
            'custom_value4' => $user->custom_value4 ?: '',
            'is_deleted' => (bool) $user->is_deleted,
            'last_login' => $user->last_login ? $user->last_login->toDateTimeString() : '',
            'created_at' => $user->created_at ? $user->created_at->toDateTimeString() : '',
            'updated_at' => $user->updated_at ? $user->updated_at->toDateTimeString() : '',
            'oauth_provider_id' => $user->oauth_provider_id ?: '',
        ];
    }

    public function includeCompanyUsers(User $user)
    {
        return $this->collection($user->company_users, new CompanyUserTransformer());
    }
}
