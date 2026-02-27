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
            'address' => $this->address,
            'description' => $this->description,
            'website' => $this->website,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
