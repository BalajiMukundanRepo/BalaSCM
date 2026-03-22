<?php

namespace App\Transformers;

use App\Models\GroupSetting;
use League\Fractal\TransformerAbstract;

class GroupSettingTransformer extends TransformerAbstract
{
    public function transform(GroupSetting $groupSetting): array
    {
        return [
            'id' => (int) $groupSetting->id,
            'name' => $groupSetting->name ?: '',
            'settings' => $groupSetting->settings ?: new \stdClass(),
            'is_deleted' => (bool) $groupSetting->is_deleted,
            'created_at' => $groupSetting->created_at ? $groupSetting->created_at->toDateTimeString() : '',
            'updated_at' => $groupSetting->updated_at ? $groupSetting->updated_at->toDateTimeString() : '',
        ];
    }
}
