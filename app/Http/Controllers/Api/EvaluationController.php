<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEvaluationRequest;
use App\Http\Resources\EvaluationResource;
use App\Models\Evaluation;

class EvaluationController extends Controller
{
    public function index()
    {
        $evaluations = Evaluation::query()->get();

        return response()->json([
            'success' => true,
            'data' => EvaluationResource::collection($evaluations),
        ], 200);
    }

    public function store(StoreEvaluationRequest $request)
    {
        $data = $request->validated();
        $data['evaluation_date'] = $data['evaluation_date'] ?? now()->toDateString();

        $evaluation = Evaluation::create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ التقييم بنجاح',
            'data' => new EvaluationResource($evaluation),
        ], 201);
    }
}
