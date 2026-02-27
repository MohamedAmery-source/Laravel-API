<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Http\Resources\TrainingReportResource;
use App\Models\TrainingReport;
use Illuminate\Http\Request;

class TrainingReportController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainingReport::query();

        if ($request->filled('internship_id')) {
            $query->where('internship_id', $request->internship_id);
        }

        $reports = $query->get();

        return response()->json([
            'success' => true,
            'data' => TrainingReportResource::collection($reports),
        ], 200);
    }

    public function store(StoreReportRequest $request)
    {
        $data = $request->validated();
        $data['submission_date'] = $data['submission_date'] ?? now()->toDateString();

        $report = TrainingReport::create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم رفع التقرير بنجاح',
            'data' => new TrainingReportResource($report),
        ], 201);
    }
}
