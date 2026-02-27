<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'request_id' => $this->request_id,
            'student_id' => $this->student_id,
            'opportunity_id' => $this->opportunity_id,
            'submission_date' => $this->submission_date,
            'status' => $this->status,
            'student_notes' => $this->student_notes,
            'admin_notes' => $this->admin_notes,
            'institution_notes' => $this->institution_notes,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
