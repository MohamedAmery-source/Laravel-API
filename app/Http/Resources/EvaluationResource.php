<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvaluationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'evaluation_id' => $this->evaluation_id,
            'internship_id' => $this->internship_id,
            'evaluator_type' => $this->evaluator_type,
            'technical_skills' => $this->technical_skills,
            'commitment' => $this->commitment,
            'teamwork' => $this->teamwork,
            'attendance' => $this->attendance,
            'overall_rating' => $this->overall_rating,
            'comments' => $this->comments,
            'evaluation_date' => $this->evaluation_date,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
