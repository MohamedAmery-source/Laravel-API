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

        return response()->json([
            'success' => true,
            'data' => InstitutionResource::collection($institutions),
        ], 200);
    }

    public function store(StoreInstitutionRequest $request)
    {
        $institution = Institution::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المؤسسة بنجاح',
            'data' => new InstitutionResource($institution),
        ], 201);
    }

    public function show(string $id)
    {
        $institution = Institution::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new InstitutionResource($institution),
        ], 200);
    }

    public function update(UpdateInstitutionRequest $request, string $id)
    {
        $institution = Institution::findOrFail($id);
        $institution->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات المؤسسة بنجاح',
            'data' => new InstitutionResource($institution),
        ], 200);
    }

    public function destroy(string $id)
    {
        $institution = Institution::findOrFail($id);
        $institution->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'تم تعطيل المؤسسة بنجاح',
        ], 200);
    }
}
