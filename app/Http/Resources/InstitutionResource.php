<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitutionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'institution_id' => $this->institution_id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'commercial_register' => $this->commercial_register,
            'address' => $this->address,
            'description' => $this->description,
            'website' => $this->website,
            'social_links' => $this->social_links,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'status' => $this->whenLoaded('user', fn () => $this->user?->status),
            'logo_path' => $this->whenLoaded('user', fn () => $this->user?->profile_picture),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
