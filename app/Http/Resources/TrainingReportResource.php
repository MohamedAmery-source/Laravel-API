<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'report_id' => $this->report_id,
            'internship_id' => $this->internship_id,
            'title' => $this->title,
            'content' => $this->content,
            'report_file' => $this->report_file,
            'week_number' => $this->week_number,
            'submitted_by' => $this->submitted_by,
            'submission_date' => $this->submission_date,
            'is_approved' => $this->is_approved,
            'supervisor_comments' => $this->supervisor_comments,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
