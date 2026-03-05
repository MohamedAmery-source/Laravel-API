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

        return $this->success(OpportunityResource::collection($query->get()), null, 200);
    }

    public function store(StoreOpportunityRequest $request)
    {
        $data = $request->validated();

        if (empty($data['institution_id']) && $request->user()?->institution) {
            $data['institution_id'] = $request->user()->institution->institution_id;
        }

        if (empty($data['institution_id'])) {
            return $this->error('institution_id is required', 422);
        }

        $opportunity = TrainingOpportunity::create($data);

        return $this->success(new OpportunityResource($opportunity), 'Opportunity created successfully', 201);
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

        return $this->success(new OpportunityResource($opportunity), 'Opportunity updated successfully', 200);
    }

    public function destroy(string $id)
    {
        $opportunity = TrainingOpportunity::findOrFail($id);
        $opportunity->update(['is_active' => false]);

        return $this->success(null, 'Opportunity hidden successfully', 200);
    }
}
