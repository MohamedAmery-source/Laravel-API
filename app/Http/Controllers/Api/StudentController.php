<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::query()->get();

        return response()->json([
            'success' => true,
            'data' => StudentResource::collection($students),
        ], 200);
    }

    public function store(StoreStudentRequest $request)
    {
        $student = Student::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء ملف الطالب بنجاح',
            'data' => new StudentResource($student),
        ], 201);
    }

    public function show(string $id)
    {
        $student = Student::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new StudentResource($student),
        ], 200);
    }

    public function update(UpdateStudentRequest $request, string $id)
    {
        $student = Student::findOrFail($id);
        $student->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الطالب بنجاح',
            'data' => new StudentResource($student),
        ], 200);
    }

    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        $student->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'تم تعطيل حساب الطالب بنجاح',
        ], 200);
    }
}
