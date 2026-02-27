<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'opportunity_id' => $this->opportunity_id,
            'institution_id' => $this->institution_id,
            'title' => $this->title,
            'description' => $this->description,
            'required_skills' => $this->required_skills,
            'available_seats' => $this->available_seats,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'application_deadline' => $this->application_deadline,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
