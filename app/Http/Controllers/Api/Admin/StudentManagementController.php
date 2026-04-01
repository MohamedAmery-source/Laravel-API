<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Admin\AdminChangeStudentStatusRequest;
use App\Http\Requests\Admin\AdminStoreStudentRequest;
use App\Http\Requests\Admin\AdminUpdateStudentRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentManagementController extends AdminController
{
    public function index(Request $request)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $query = Student::query()->with('user');

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->query('department'));
        }

        if ($request->filled('status')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('status', $request->query('status'));
            });
        }

        $students = $query->orderByDesc('student_id')->paginate($this->perPage($request));

        $data = [
            'items' => $students->getCollection()->map(function (Student $student) {
                return [
                    'student_id' => $student->student_id,
                    'user_id' => $student->user_id,
                    'full_name' => $student->user?->full_name,
                    'email' => $student->user?->email,
                    'phone' => $student->user?->phone,
                    'status' => $student->user?->status,
                    'student_number' => $student->student_number,
                    'department' => $student->department,
                    'level' => $student->level,
                    'gpa' => $student->gpa,
                    'is_active' => $student->is_active,
                    'created_at' => $student->created_at,
                ];
            })->values(),
            'meta' => $this->paginateMeta($students),
        ];

        return $this->success($data);
    }

    public function store(AdminStoreStudentRequest $request)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $data = $request->validated();
        $status = $data['status'] ?? 'active';
        $isActive = $status === 'active';

        $student = DB::transaction(function () use ($data, $status, $isActive) {
            $user = User::query()->create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'user_type' => 'student',
                'status' => $status,
                'is_active' => $isActive,
            ]);

            return Student::query()->create([
                'user_id' => $user->user_id,
                'student_number' => $data['student_number'],
                'department' => $data['department'],
                'level' => $data['level'],
                'gpa' => $data['gpa'] ?? null,
                'is_active' => $isActive,
            ])->load('user');
        });

        return $this->success([
            'student_id' => $student->student_id,
            'user_id' => $student->user_id,
            'full_name' => $student->user?->full_name,
            'email' => $student->user?->email,
            'status' => $student->user?->status,
        ], 'Student created successfully.', 201);
    }

    public function update(AdminUpdateStudentRequest $request, string $id)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $student = Student::query()->with('user')->find($id);
        if (!$student) {
            return $this->error('Student not found for the provided id.', 404);
        }
        $data = $request->validated();

        DB::transaction(function () use ($student, $data) {
            $userUpdates = [];

            foreach (['full_name', 'email', 'phone'] as $field) {
                if (array_key_exists($field, $data)) {
                    $userUpdates[$field] = $data[$field];
                }
            }

            if (array_key_exists('status', $data)) {
                $userUpdates['status'] = $data['status'];
                $userUpdates['is_active'] = $data['status'] === 'active';
                $student->is_active = $data['status'] === 'active';
            }

            if (!empty($userUpdates)) {
                $student->user()->update($userUpdates);
            }

            $studentUpdates = [];
            foreach (['department', 'level', 'gpa'] as $field) {
                if (array_key_exists($field, $data)) {
                    $studentUpdates[$field] = $data[$field];
                }
            }

            if (!empty($studentUpdates) || array_key_exists('status', $data)) {
                $student->fill($studentUpdates);
                $student->save();
            }
        });

        $student->refresh()->load('user');

        return $this->success([
            'student_id' => $student->student_id,
            'full_name' => $student->user?->full_name,
            'email' => $student->user?->email,
            'status' => $student->user?->status,
            'department' => $student->department,
            'level' => $student->level,
            'gpa' => $student->gpa,
        ], 'Student updated successfully.');
    }

    public function changeStatus(AdminChangeStudentStatusRequest $request, string $id)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $student = Student::query()->with('user')->find($id);
        if (!$student) {
            return $this->error('Student not found for the provided id.', 404);
        }
        $status = $request->validated()['status'];
        $isActive = $status === 'active';

        DB::transaction(function () use ($student, $status, $isActive) {
            $student->update(['is_active' => $isActive]);
            $student->user()->update([
                'status' => $status,
                'is_active' => $isActive,
            ]);
        });

        return $this->success([
            'student_id' => $student->student_id,
            'status' => $status,
        ], 'Student status updated successfully.');
    }
}
