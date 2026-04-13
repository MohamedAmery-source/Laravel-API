<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Document;
use App\Models\Evaluation;
use App\Models\Internship;
use App\Models\Student;
use App\Models\TrainingOpportunity;
use App\Models\TrainingReport;
use App\Models\TrainingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentPortalController extends Controller
{
    public function profile(Request $request): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        return $this->success($this->profilePayload($student));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $data = $request->validate([
            'department' => ['sometimes', 'string', 'max:100'],
            'level' => ['sometimes', 'string', 'max:20'],
            'gpa' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'city' => ['nullable', 'string', 'max:100'],
            'university' => ['nullable', 'string', 'max:150'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['required', 'string', 'max:100'],
        ]);

        $student->update($data);
        $student->refresh()->load('user');

        return $this->success($this->profilePayload($student), 'تم تحديث الملف الشخصي بنجاح.');
    }

    public function uploadCv(Request $request): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $validated = $request->validate([
            'cv' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $path = $validated['cv']->store('student-cv', 'public');

        Document::query()->updateOrCreate(
            [
                'user_id' => $student->user_id,
                'title' => 'CV',
                'request_id' => null,
            ],
            [
                'file_url' => $path,
                'file_type' => 'pdf',
                'is_active' => true,
            ]
        );

        return $this->success([
            'cv_path' => $path,
            'cv_url' => Storage::disk('public')->url($path),
        ], 'تم رفع السيرة الذاتية بنجاح.');
    }

    public function dashboardStats(Request $request): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $availableOpportunities = TrainingOpportunity::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            })
            ->count();

        $myRequests = TrainingRequest::query()->where('student_id', $student->student_id);
        $pendingRequestsCount = (clone $myRequests)
            ->whereIn('status', ['pending_admin', 'pending_institution'])
            ->count();

        $latestRequest = (clone $myRequests)->orderByDesc('request_id')->first();

        $recommended = TrainingOpportunity::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            })
            ->when($student->city, function ($q) use ($student) {
                $q->where('city', $student->city);
            })
            ->when($student->department, function ($q) use ($student) {
                $q->where('department', $student->department);
            })
            ->with('institution')
            ->orderByDesc('opportunity_id')
            ->limit(3)
            ->get()
            ->map(function (TrainingOpportunity $opportunity) {
                return [
                    'opportunity_id' => $opportunity->opportunity_id,
                    'title' => $opportunity->title,
                    'city' => $opportunity->city,
                    'training_type' => $opportunity->training_type,
                    'institution_name' => $opportunity->institution?->name,
                ];
            })->values();

        return $this->success([
            'available_opportunities' => $availableOpportunities,
            'pending_requests_count' => $pendingRequestsCount,
            'latest_request_status' => $latestRequest?->status,
            'latest_request_status_label' => $this->statusLabel($latestRequest?->status),
            'recommended_opportunities' => $recommended,
        ]);
    }

    public function timeline(Request $request): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $latestRequest = TrainingRequest::query()
            ->with('opportunity')
            ->where('student_id', $student->student_id)
            ->orderByDesc('request_id')
            ->first();

        if (!$latestRequest) {
            return $this->success([
                'current_status' => null,
                'current_status_label' => 'لا يوجد طلب تدريب حتى الآن.',
                'steps' => [],
            ]);
        }

        $status = $latestRequest->status;
        $steps = [
            ['key' => 'submitted', 'label' => 'تم إرسال الطلب', 'done' => true],
            ['key' => 'pending_admin', 'label' => 'بانتظار موافقة الجامعة', 'done' => in_array($status, ['pending_admin', 'pending_institution', 'approved', 'rejected', 'completed'], true)],
            ['key' => 'pending_institution', 'label' => 'بانتظار رد الجهة التدريبية', 'done' => in_array($status, ['pending_institution', 'approved', 'rejected', 'completed'], true)],
            ['key' => 'approved', 'label' => 'تم القبول', 'done' => in_array($status, ['approved', 'completed'], true)],
        ];

        return $this->success([
            'request_id' => $latestRequest->request_id,
            'opportunity' => [
                'opportunity_id' => $latestRequest->opportunity?->opportunity_id,
                'title' => $latestRequest->opportunity?->title,
            ],
            'current_status' => $status,
            'current_status_label' => $this->statusLabel($status),
            'steps' => $steps,
        ]);
    }

    public function opportunities(Request $request): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $query = TrainingOpportunity::query()
            ->with('institution')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            });

        if ($request->filled('city')) {
            $query->where('city', (string) $request->query('city'));
        }
        if ($request->filled('company')) {
            $company = (string) $request->query('company');
            $query->whereHas('institution', function ($q) use ($company) {
                $q->where('name', 'like', "%{$company}%");
            });
        }
        if ($request->filled('type')) {
            $query->where('training_type', (string) $request->query('type'));
        }

        $items = $query->orderByDesc('opportunity_id')->paginate($this->perPage($request));

        return $this->success([
            'items' => $items->items(),
            'meta' => $this->paginateMeta($items),
        ]);
    }

    public function showOpportunity(Request $request, string $id): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $opportunity = TrainingOpportunity::query()
            ->with(['institution.user'])
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            })
            ->find($id);

        if (!$opportunity) {
            return $this->error('الفرصة التدريبية المطلوبة غير موجودة.', 404);
        }

        return $this->success([
            'opportunity_id' => $opportunity->opportunity_id,
            'title' => $opportunity->title,
            'description' => $opportunity->description,
            'department' => $opportunity->department,
            'city' => $opportunity->city,
            'training_type' => $opportunity->training_type,
            'available_seats' => $opportunity->available_seats,
            'start_date' => $opportunity->start_date,
            'end_date' => $opportunity->end_date,
            'application_deadline' => $opportunity->application_deadline,
            'custom_questions' => $opportunity->custom_questions ?? [],
            'institution' => [
                'institution_id' => $opportunity->institution?->institution_id,
                'name' => $opportunity->institution?->name,
                'website' => $opportunity->institution?->website,
                'logo_path' => $opportunity->institution?->user?->profile_picture,
                'logo_url' => $opportunity->institution?->user?->profile_picture
                    ? Storage::disk('public')->url($opportunity->institution->user->profile_picture)
                    : null,
            ],
        ]);
    }

    public function apply(Request $request, string $id): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $data = $request->validate([
            'student_answers_block' => ['required', 'string'],
            'student_notes' => ['nullable', 'string'],
        ]);

        $opportunity = TrainingOpportunity::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            })
            ->find($id);

        if (!$opportunity) {
            return $this->error('الفرصة التدريبية المطلوبة غير متاحة للتقديم.', 404);
        }

        $completion = $this->profileCompletion($student);
        if (!$completion['is_complete']) {
            return $this->error('لا يمكن التقديم قبل إكمال الملف الشخصي بنسبة 100%.', 422, [
                'completion_percentage' => $completion['completion_percentage'],
                'missing_fields' => $completion['missing_fields'],
            ]);
        }

        $activeInternshipExists = Internship::query()
            ->where('status', 'active')
            ->whereHas('trainingRequest', function ($q) use ($student) {
                $q->where('student_id', $student->student_id);
            })
            ->exists();

        if ($activeInternshipExists) {
            return $this->error('لديك تدريب نشط حالياً ولا يمكنك التقديم على فرصة جديدة.', 422);
        }

        $pendingRequestExists = TrainingRequest::query()
            ->where('student_id', $student->student_id)
            ->whereIn('status', ['pending_admin', 'pending_institution'])
            ->exists();

        if ($pendingRequestExists) {
            return $this->error('لديك طلب قيد المراجعة بالفعل ولا يمكن إرسال طلب جديد حالياً.', 422);
        }

        $alreadyApplied = TrainingRequest::query()
            ->where('student_id', $student->student_id)
            ->where('opportunity_id', $opportunity->opportunity_id)
            ->whereIn('status', ['pending_admin', 'pending_institution', 'approved', 'completed'])
            ->exists();

        if ($alreadyApplied) {
            return $this->error('تم التقديم مسبقاً على هذه الفرصة.', 422);
        }

        $trainingRequest = TrainingRequest::query()->create([
            'student_id' => $student->student_id,
            'opportunity_id' => $opportunity->opportunity_id,
            'submission_date' => now()->toDateString(),
            'status' => 'pending_admin',
            'student_notes' => $data['student_notes'] ?? null,
            'student_answers' => $data['student_answers_block'],
            'is_active' => true,
        ]);

        return $this->success([
            'request_id' => $trainingRequest->request_id,
            'status' => $trainingRequest->status,
            'status_label' => $this->statusLabel($trainingRequest->status),
        ], 'تم إرسال طلب التقديم بنجاح.', 201);
    }

    public function requests(Request $request): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $items = TrainingRequest::query()
            ->with(['opportunity.institution'])
            ->where('student_id', $student->student_id)
            ->orderByDesc('request_id')
            ->paginate($this->perPage($request));

        return $this->success([
            'items' => collect($items->items())->map(function (TrainingRequest $item) {
                return [
                    'request_id' => $item->request_id,
                    'submission_date' => $item->submission_date,
                    'status' => $item->status,
                    'status_label' => $this->statusLabel($item->status),
                    'rejection_reason' => $item->status === 'rejected'
                        ? ($item->institution_notes ?: $item->admin_notes)
                        : null,
                    'opportunity' => [
                        'opportunity_id' => $item->opportunity?->opportunity_id,
                        'title' => $item->opportunity?->title,
                        'institution_name' => $item->opportunity?->institution?->name,
                    ],
                ];
            })->values(),
            'meta' => $this->paginateMeta($items),
        ]);
    }

    public function showRequest(Request $request, string $id): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $trainingRequest = TrainingRequest::query()
            ->with(['opportunity.institution'])
            ->where('student_id', $student->student_id)
            ->find($id);

        if (!$trainingRequest) {
            return $this->error('طلب التقديم المطلوب غير موجود.', 404);
        }

        return $this->success([
            'request_id' => $trainingRequest->request_id,
            'submission_date' => $trainingRequest->submission_date,
            'status' => $trainingRequest->status,
            'status_label' => $this->statusLabel($trainingRequest->status),
            'student_answers' => $trainingRequest->student_answers,
            'student_notes' => $trainingRequest->student_notes,
            'admin_notes' => $trainingRequest->admin_notes,
            'institution_notes' => $trainingRequest->institution_notes,
            'rejection_reason' => $trainingRequest->status === 'rejected'
                ? ($trainingRequest->institution_notes ?: $trainingRequest->admin_notes)
                : null,
            'opportunity' => [
                'opportunity_id' => $trainingRequest->opportunity?->opportunity_id,
                'title' => $trainingRequest->opportunity?->title,
                'institution_name' => $trainingRequest->opportunity?->institution?->name,
            ],
        ]);
    }

    public function myInternship(Request $request): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $internship = Internship::query()
            ->with('trainingRequest.opportunity.institution')
            ->whereHas('trainingRequest', function ($q) use ($student) {
                $q->where('student_id', $student->student_id)->where('status', 'approved');
            })
            ->orderByDesc('internship_id')
            ->first();

        if (!$internship) {
            return $this->error('لا يوجد تدريب نشط مرتبط بحسابك حالياً.', 404);
        }

        return $this->success([
            'internship_id' => $internship->internship_id,
            'status' => $internship->status,
            'actual_start_date' => $internship->actual_start_date,
            'actual_end_date' => $internship->actual_end_date,
            'mentor_name' => $internship->mentor_name,
            'assigned_tasks' => $internship->assigned_tasks,
            'opportunity' => [
                'opportunity_id' => $internship->trainingRequest?->opportunity?->opportunity_id,
                'title' => $internship->trainingRequest?->opportunity?->title,
                'institution_name' => $internship->trainingRequest?->opportunity?->institution?->name,
            ],
        ]);
    }

    public function storeReport(Request $request): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $internship = Internship::query()
            ->where('status', 'active')
            ->whereHas('trainingRequest', function ($q) use ($student) {
                $q->where('student_id', $student->student_id)->where('status', 'approved');
            })
            ->first();

        if (!$internship) {
            return $this->error('لا يمكنك رفع التقارير قبل بدء التدريب الفعلي.', 422);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'week_number' => ['nullable', 'integer', 'min:1', 'max:60'],
        ]);

        $path = $data['file']->store('internship-reports', 'public');

        $report = TrainingReport::query()->create([
            'internship_id' => $internship->internship_id,
            'title' => $data['title'],
            'report_file' => $path,
            'submitted_by' => 'student',
            'submission_date' => now()->toDateString(),
            'week_number' => $data['week_number'] ?? null,
            'is_active' => true,
        ]);

        return $this->success($report, 'تم رفع التقرير بنجاح.', 201);
    }

    public function evaluation(Request $request): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $internship = Internship::query()
            ->whereHas('trainingRequest', function ($q) use ($student) {
                $q->where('student_id', $student->student_id)->where('status', 'approved');
            })
            ->orderByDesc('internship_id')
            ->first();

        if (!$internship) {
            return $this->error('لا يوجد تدريب مرتبط بحسابك لعرض التقييم.', 404);
        }

        $evaluation = Evaluation::query()
            ->where('internship_id', $internship->internship_id)
            ->whereIn('evaluator_type', ['institution', 'supervisor'])
            ->orderByDesc('evaluation_id')
            ->first();

        if (!$evaluation) {
            return $this->error('التقييم النهائي غير متاح حالياً.', 404);
        }

        return $this->success([
            'evaluation_id' => $evaluation->evaluation_id,
            'score' => $evaluation->final_score,
            'notes' => $evaluation->comments,
            'evaluation_date' => $evaluation->evaluation_date,
        ]);
    }

    public function complaints(Request $request): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $items = Complaint::query()
            ->where('user_id', $student->user_id)
            ->orderByDesc('complaint_id')
            ->paginate($this->perPage($request));

        return $this->success([
            'items' => $items->items(),
            'meta' => $this->paginateMeta($items),
        ]);
    }

    public function storeComplaint(Request $request): JsonResponse
    {
        $student = $this->studentFromRequest($request);
        if ($student instanceof JsonResponse) {
            return $student;
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'category' => ['nullable', Rule::in(['academic', 'institution', 'technical'])],
        ]);

        $title = $data['title'];
        if (!empty($data['category'])) {
            $title = '['.$data['category'].'] '.$title;
        }

        $complaint = Complaint::query()->create([
            'user_id' => $student->user_id,
            'title' => $title,
            'description' => $data['description'],
            'status' => 'pending',
        ]);

        return $this->success($complaint, 'تم إرسال الشكوى بنجاح.', 201);
    }

    private function studentFromRequest(Request $request): Student|JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return $this->error('غير مصرح. يرجى تسجيل الدخول.', 401);
        }
        if ($user->user_type !== 'student') {
            return $this->error('هذه الواجهة مخصصة للطلاب فقط.', 403);
        }

        $student = $user->student()->with('user')->first();
        if (!$student) {
            return $this->error('لم يتم العثور على ملف الطالب المرتبط بهذا الحساب.', 404);
        }

        if (!$student->is_active || !$user->is_active || !in_array($user->status, ['active'], true)) {
            return $this->error('حساب الطالب غير نشط حالياً.', 403);
        }

        return $student;
    }

    private function profilePayload(Student $student): array
    {
        $cv = Document::query()
            ->where('user_id', $student->user_id)
            ->where('title', 'CV')
            ->whereNull('request_id')
            ->latest('document_id')
            ->first();

        $completion = $this->profileCompletion($student, $cv);

        return [
            'student_id' => $student->student_id,
            'user_id' => $student->user_id,
            'full_name' => $student->user?->full_name,
            'email' => $student->user?->email,
            'phone' => $student->user?->phone,
            'student_number' => $student->student_number,
            'university' => $student->university,
            'department' => $student->department,
            'level' => $student->level,
            'gpa' => $student->gpa,
            'city' => $student->city,
            'skills' => $student->skills ?? [],
            'cv' => [
                'document_id' => $cv?->document_id,
                'file_path' => $cv?->file_url,
                'file_url' => $cv?->file_url ? Storage::disk('public')->url($cv->file_url) : null,
            ],
            'completion_percentage' => $completion['completion_percentage'],
            'is_profile_complete' => $completion['is_complete'],
            'missing_fields' => $completion['missing_fields'],
        ];
    }

    private function profileCompletion(Student $student, ?Document $cv = null): array
    {
        $cv ??= Document::query()
            ->where('user_id', $student->user_id)
            ->where('title', 'CV')
            ->whereNull('request_id')
            ->first();

        $checks = [
            'student_number' => !empty($student->student_number),
            'department' => !empty($student->department),
            'level' => !empty($student->level),
            'gpa' => !is_null($student->gpa),
            'city' => !empty($student->city),
            'skills' => is_array($student->skills) && count($student->skills) > 0,
            'cv' => !empty($cv?->file_url),
        ];

        $total = count($checks);
        $done = count(array_filter($checks));
        $percentage = (int) floor(($done / max($total, 1)) * 100);

        return [
            'completion_percentage' => $percentage,
            'is_complete' => $percentage === 100,
            'missing_fields' => collect($checks)->filter(fn ($ok) => !$ok)->keys()->values()->all(),
        ];
    }

    private function statusLabel(?string $status): ?string
    {
        return match ($status) {
            'pending_admin' => 'بانتظار موافقة الجامعة',
            'pending_institution' => 'بانتظار رد الجهة التدريبية',
            'approved' => 'تم القبول - ابدأ التدريب',
            'rejected' => 'تم الرفض',
            'completed' => 'تم إنهاء التدريب',
            default => $status,
        };
    }

    private function perPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 15);

        return max(1, min($perPage, 100));
    }

    private function paginateMeta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }
}
