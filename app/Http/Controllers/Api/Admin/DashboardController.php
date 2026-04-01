<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Complaint;
use App\Models\Institution;
use App\Models\Student;
use App\Models\TrainingOpportunity;
use App\Models\TrainingRequest;
use Illuminate\Http\Request;

class DashboardController extends AdminController
{
    public function stats(Request $request)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $data = [
            'total_students' => Student::query()->count(),
            'approved_institutions' => Institution::query()
                ->where('is_active', true)
                ->whereHas('user', function ($query) {
                    $query->where('status', 'active');
                })
                ->count(),
            'active_opportunities' => TrainingOpportunity::query()
                ->where('is_active', true)
                ->count(),
            'pending_admin_requests' => TrainingRequest::query()
                ->whereIn('status', ['pending_admin', 'pending'])
                ->count(),
            'open_complaints' => Complaint::query()
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
        ];

        return $this->success($data);
    }
}
