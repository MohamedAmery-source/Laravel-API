<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeRequestStatusRequest;
use App\Http\Requests\StoreTrainingRequest;
use App\Http\Resources\TrainingRequestResource;
use App\Models\TrainingRequest;
use Illuminate\Http\Request;

class TrainingRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainingRequest::query();

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('opportunity_id')) {
            $query->where('opportunity_id', $request->opportunity_id);
        }

        if ($request->user() && $request->user()->institution) {
            $institutionId = $request->user()->institution->institution_id;
            $query->whereHas('opportunity', function ($q) use ($institutionId) {
                $q->where('institution_id', $institutionId);
            });
        }

        $requests = $query->get();

        return $this->success(TrainingRequestResource::collection($requests), null, 200);
    }

    public function store(StoreTrainingRequest $request)
    {
        $data = $request->validated();
        $data['submission_date'] = $data['submission_date'] ?? now()->toDateString();

        $trainingRequest = TrainingRequest::create($data);

        return $this->success(new TrainingRequestResource($trainingRequest), 'تم تقديم الطلب بنجاح', 201);
    }

    public function changeStatus(ChangeRequestStatusRequest $request, string $id)
    {
        $trainingRequest = TrainingRequest::findOrFail($id);
        $trainingRequest->update($request->validated());

        return $this->success(new TrainingRequestResource($trainingRequest), 'تم تحديث حالة الطلب بنجاح', 200);
    }
}
