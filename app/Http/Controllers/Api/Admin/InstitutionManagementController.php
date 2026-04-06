<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Admin\AdminChangeInstitutionStatusRequest;
use App\Http\Requests\Admin\AdminStoreInstitutionRequest;
use App\Http\Requests\Admin\AdminUpdateInstitutionRequest;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstitutionManagementController extends AdminController
{
    public function index(Request $request)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $query = Institution::query()->with('user');

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $status = (string) $request->query('status');
            $mappedStatus = $status === 'pending_approval' ? 'inactive' : $status;
            $query->whereHas('user', function ($q) use ($mappedStatus) {
                $q->where('status', $mappedStatus);
            });
        }

        $institutions = $query->orderByDesc('institution_id')->paginate($this->perPage($request));

        $data = [
            'items' => $institutions->getCollection()->map(function (Institution $institution) {
                return [
                    'institution_id' => $institution->institution_id,
                    'user_id' => $institution->user_id,
                    'name' => $institution->name,
                    'email' => $institution->user?->email,
                    'full_name' => $institution->user?->full_name,
                    'phone' => $institution->user?->phone,
                    'status' => $institution->user?->status === 'inactive' ? 'pending_approval' : $institution->user?->status,
                    'address' => $institution->address,
                    'contact_person' => $institution->contact_person,
                    'contact_phone' => $institution->contact_phone,
                    'is_active' => $institution->is_active,
                    'created_at' => $institution->created_at,
                ];
            })->values(),
            'meta' => $this->paginateMeta($institutions),
        ];

        return $this->success($data);
    }

    public function store(AdminStoreInstitutionRequest $request)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $data = $request->validated();
        $statusInput = $data['status'] ?? 'pending_approval';
        $mappedStatus = $statusInput === 'pending_approval' ? 'inactive' : $statusInput;
        $isActive = $mappedStatus === 'active';

        $institution = DB::transaction(function () use ($data, $mappedStatus, $isActive) {
            $user = User::query()->create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'user_type' => 'institution',
                'status' => $mappedStatus,
                'is_active' => $isActive,
            ]);

            return Institution::query()->create([
                'user_id' => $user->user_id,
                'name' => $data['name'],
                'address' => $data['address'] ?? null,
                'description' => $data['description'] ?? null,
                'website' => $data['website'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'is_active' => $isActive,
            ])->load('user');
        });

        return $this->success([
            'institution_id' => $institution->institution_id,
            'name' => $institution->name,
            'status' => $institution->user?->status === 'inactive' ? 'pending_approval' : $institution->user?->status,
        ], 'تم إنشاء الجهة التدريبية بنجاح.', 201);
    }

    public function update(AdminUpdateInstitutionRequest $request, string $id)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $institution = Institution::query()->with('user')->find($id);
        if (!$institution) {
            return $this->error('لم يتم العثور على الجهة التدريبية للمعرف المرسل.', 404);
        }
        $data = $request->validated();

        DB::transaction(function () use ($institution, $data) {
            $userUpdates = [];
            foreach (['full_name', 'email', 'phone'] as $field) {
                if (array_key_exists($field, $data)) {
                    $userUpdates[$field] = $data[$field];
                }
            }

            if (array_key_exists('status', $data)) {
                $mappedStatus = $data['status'] === 'pending_approval' ? 'inactive' : $data['status'];
                $userUpdates['status'] = $mappedStatus;
                $userUpdates['is_active'] = $mappedStatus === 'active';
                $institution->is_active = $mappedStatus === 'active';
            }

            if (!empty($userUpdates)) {
                $institution->user()->update($userUpdates);
            }

            $institutionUpdates = [];
            foreach (['name', 'address', 'description', 'website', 'contact_person', 'contact_phone'] as $field) {
                if (array_key_exists($field, $data)) {
                    $institutionUpdates[$field] = $data[$field];
                }
            }

            if (!empty($institutionUpdates) || array_key_exists('status', $data)) {
                $institution->fill($institutionUpdates);
                $institution->save();
            }
        });

        $institution->refresh()->load('user');

        return $this->success([
            'institution_id' => $institution->institution_id,
            'name' => $institution->name,
            'status' => $institution->user?->status === 'inactive' ? 'pending_approval' : $institution->user?->status,
        ], 'تم تحديث بيانات الجهة التدريبية بنجاح.');
    }

    public function approve(Request $request, string $id)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $institution = Institution::query()->with('user')->find($id);
        if (!$institution) {
            return $this->error('لم يتم العثور على الجهة التدريبية للمعرف المرسل.', 404);
        }

        $institution->user()->update([
            'status' => 'active',
            'is_active' => true,
        ]);
        $institution->update(['is_active' => true]);

        return $this->success([
            'institution_id' => $institution->institution_id,
            'status' => 'active',
        ], 'تم اعتماد الجهة التدريبية بنجاح.');
    }

    public function changeStatus(AdminChangeInstitutionStatusRequest $request, string $id)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $institution = Institution::query()->with('user')->find($id);
        if (!$institution) {
            return $this->error('لم يتم العثور على الجهة التدريبية للمعرف المرسل.', 404);
        }
        $status = $request->validated()['status'];
        $isActive = $status === 'active';

        DB::transaction(function () use ($institution, $status, $isActive) {
            $institution->update(['is_active' => $isActive]);
            $institution->user()->update([
                'status' => $status,
                'is_active' => $isActive,
            ]);
        });

        return $this->success([
            'institution_id' => $institution->institution_id,
            'status' => $status,
        ], 'تم تحديث حالة الجهة التدريبية بنجاح.');
    }
}
