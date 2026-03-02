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

        return $this->success(StudentResource::collection($students), null, 200);
    }

    public function store(StoreStudentRequest $request)
    {
        $student = Student::create($request->validated());

        return $this->success(new StudentResource($student), 'تم إنشاء ملف الطالب بنجاح', 201);
    }

    public function show(string $id)
    {
        $student = Student::findOrFail($id);

        return $this->success(new StudentResource($student), null, 200);
    }

    public function update(UpdateStudentRequest $request, string $id)
    {
        $student = Student::findOrFail($id);
        $student->update($request->validated());

        return $this->success(new StudentResource($student), 'تم تحديث بيانات الطالب بنجاح', 200);
    }

    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        $student->update(['is_active' => false]);

        return $this->success(null, 'تم تعطيل حساب الطالب بنجاح', 200);
    }
}
