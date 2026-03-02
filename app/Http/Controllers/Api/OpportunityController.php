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

        return $this->success(OpportunityResource::collection($opportunities), null, 200);
    }

    public function store(StoreOpportunityRequest $request)
    {
        $opportunity = TrainingOpportunity::create($request->validated());

        return $this->success(new OpportunityResource($opportunity), 'تم إضافة الفرصة التدريبية بنجاح', 201);
    }

    public function show(string $id)
    {
        $opportunity = TrainingOpportunity::findOrFail($id);

        return $this->success(new OpportunityResource($opportunity), null, 200);
    }

    public function update(UpdateOpportunityRequest $request, string $id)
    {
        $opportunity = TrainingOpportunity::findOrFail($id);
        $opportunity->update($request->validated());

        return $this->success(new OpportunityResource($opportunity), 'تم تحديث الفرصة التدريبية بنجاح', 200);
    }

    public function destroy(string $id)
    {
        $opportunity = TrainingOpportunity::findOrFail($id);
        $opportunity->update(['is_active' => false]);

        return $this->success(null, 'تم إخفاء الفرصة التدريبية بنجاح', 200);
    }
}
