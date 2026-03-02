<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstitutionRequest;
use App\Http\Requests\UpdateInstitutionRequest;
use App\Http\Resources\InstitutionResource;
use App\Models\Institution;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Institution::query()->get();

        return $this->success(InstitutionResource::collection($institutions), null, 200);
    }

    public function store(StoreInstitutionRequest $request)
    {
        $institution = Institution::create($request->validated());

        return $this->success(new InstitutionResource($institution), 'تم إنشاء المؤسسة بنجاح', 201);
    }

    public function show(string $id)
    {
        $institution = Institution::findOrFail($id);

        return $this->success(new InstitutionResource($institution), null, 200);
    }

    public function update(UpdateInstitutionRequest $request, string $id)
    {
        $institution = Institution::findOrFail($id);
        $institution->update($request->validated());

        return $this->success(new InstitutionResource($institution), 'تم تحديث بيانات المؤسسة بنجاح', 200);
    }

    public function destroy(string $id)
    {
        $institution = Institution::findOrFail($id);
        $institution->update(['is_active' => false]);

        return $this->success(null, 'تم تعطيل المؤسسة بنجاح', 200);
    }
}
