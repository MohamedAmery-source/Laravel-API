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
        $authUser = $request->user();

        if ($authUser && $authUser->user_type === 'institution' && (!$authUser->is_active || $authUser->status !== 'active')) {
            return $this->error('حساب الجهة التدريبية غير معتمد أو موقوف حالياً.', 403);
        }

        if (empty($data['institution_id']) && $request->user()?->institution) {
            $data['institution_id'] = $request->user()->institution->institution_id;
        }

        if (empty($data['institution_id'])) {
            return $this->error('حقل institution_id مطلوب.', 422);
        }

        $opportunity = TrainingOpportunity::create($data);

        return $this->success(new OpportunityResource($opportunity), 'تم إنشاء الفرصة بنجاح.', 201);
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

        return $this->success(new OpportunityResource($opportunity), 'تم تحديث الفرصة بنجاح.', 200);
    }

    public function destroy(string $id)
    {
        $opportunity = TrainingOpportunity::findOrFail($id);
        $opportunity->update(['is_active' => false]);

        return $this->success(null, 'تم إخفاء الفرصة بنجاح.', 200);
    }
}
