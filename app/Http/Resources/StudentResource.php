<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'student_id' => $this->student_id,
            'user_id' => $this->user_id,
            'student_number' => $this->student_number,
            'university' => $this->university,
            'department' => $this->department,
            'level' => $this->level,
            'gpa' => $this->gpa,
            'city' => $this->city,
            'skills' => $this->skills,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
