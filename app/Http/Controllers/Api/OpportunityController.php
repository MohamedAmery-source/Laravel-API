<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOpportunityRequest;
use App\Http\Requests\UpdateOpportunityRequest;
use App\Http\Resources\OpportunityResource;
use App\Models\TrainingOpportunity;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainingOpportunity::query()->where('is_active', true);

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        $opportunities = $query->get();

        return response()->json([
            'success' => true,
            'data' => OpportunityResource::collection($opportunities),
        ], 200);
    }

    public function store(StoreOpportunityRequest $request)
    {
        $opportunity = TrainingOpportunity::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الفرصة التدريبية بنجاح',
            'data' => new OpportunityResource($opportunity),
        ], 201);
    }

    public function show(string $id)
    {
        $opportunity = TrainingOpportunity::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new OpportunityResource($opportunity),
        ], 200);
    }

    public function update(UpdateOpportunityRequest $request, string $id)
    {
        $opportunity = TrainingOpportunity::findOrFail($id);
        $opportunity->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الفرصة التدريبية بنجاح',
            'data' => new OpportunityResource($opportunity),
        ], 200);
    }

    public function destroy(string $id)
    {
        $opportunity = TrainingOpportunity::findOrFail($id);
        $opportunity->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'تم إخفاء الفرصة التدريبية بنجاح',
        ], 200);
    }
}
