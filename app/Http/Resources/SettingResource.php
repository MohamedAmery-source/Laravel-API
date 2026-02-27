<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'setting_id' => $this->setting_id,
            'site_name' => $this->site_name,
            'site_logo' => $this->site_logo,
            'system_status' => $this->system_status,
            'content_email' => $this->content_email,
            'content_phone' => $this->content_phone,
            'privacy_policy' => $this->privacy_policy,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
