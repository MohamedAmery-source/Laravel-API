<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstitutionRequest;
use App\Http\Requests\UpdateInstitutionRequest;
use App\Http\Resources\InstitutionResource;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Institution::query()->get();

        return $this->success(InstitutionResource::collection($institutions), null, 200);
    }

    public function store(StoreInstitutionRequest $request)
    {
        $data = $request->validated();

        $institution = DB::transaction(function () use ($data) {
            $user = User::create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'user_type' => 'institution',
                'status' => 'pending_approval',
                'is_active' => false,
            ]);

            return Institution::create([
                'user_id' => $user->user_id,
                'name' => $data['name'],
                'address' => $data['address'] ?? null,
                'description' => $data['description'] ?? null,
                'website' => $data['website'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'is_active' => $data['is_active'] ?? false,
            ]);
        });

        return $this->success(new InstitutionResource($institution), 'تم إنشاء حساب المؤسسة بنجاح وهو الآن بانتظار اعتماد الإدارة.', 201);
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

        return $this->success(new InstitutionResource($institution), 'تم تحديث بيانات المؤسسة بنجاح.', 200);
    }

    public function destroy(string $id)
    {
        $institution = Institution::findOrFail($id);
        $institution->update(['is_active' => false]);

        return $this->success(null, 'تم تعطيل المؤسسة بنجاح.', 200);
    }
}
