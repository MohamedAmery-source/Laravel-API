<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Evaluation;
use App\Models\Institution;
use App\Models\Internship;
use App\Models\TrainingOpportunity;
use App\Models\TrainingReport;
use App\Models\TrainingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InstitutionPortalController extends Controller
{
    private const ACCOUNT_BLOCKED_MESSAGE = 'Your account is pending admin review. Please wait until activation.';

    public function profile(Request $request): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, false);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        return $this->success($this->profilePayload($institution));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, false);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['nullable', 'url', 'max:255'],
        ]);

        $institution->update($data);
        $institution->refresh()->load('user');

        return $this->success($this->profilePayload($institution), 'Institution profile updated successfully.');
    }

    public function uploadLogo(Request $request): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, false);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $validated = $request->validate([
            'logo' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $path = $validated['logo']->store('institution-logos', 'public');
        $institution->user()->update([
            'profile_picture' => $path,
        ]);

        return $this->success([
            'logo_path' => $path,
            'logo_url' => Storage::disk('public')->url($path),
        ], 'Institution logo uploaded successfully.');
    }

    public function dashboardStats(Request $request): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $institutionId = $institution->institution_id;

        $totalOpportunities = TrainingOpportunity::query()
            ->where('institution_id', $institutionId)
            ->count();

        $activeInterns = Internship::query()
            ->where('status', 'active')
            ->whereHas('trainingRequest.opportunity', function ($q) use ($institutionId) {
                $q->where('institution_id', $institutionId);
            })
            ->count();

        $pendingRequests = TrainingRequest::query()
            ->where('status', 'pending_institution')
            ->whereHas('opportunity', function ($q) use ($institutionId) {
                $q->where('institution_id', $institutionId);
            });

        $latestPendingRequests = (clone $pendingRequests)
            ->with(['student.user', 'opportunity'])
            ->orderByDesc('request_id')
            ->limit(5)
            ->get()
            ->map(function (TrainingRequest $item) {
                return [
                    'request_id' => $item->request_id,
                    'submission_date' => $item->submission_date,
                    'student' => [
                        'student_id' => $item->student?->student_id,
                        'full_name' => $item->student?->user?->full_name,
                        'department' => $item->student?->department,
                        'gpa' => $item->student?->gpa,
                    ],
                    'opportunity' => [
                        'opportunity_id' => $item->opportunity?->opportunity_id,
                        'title' => $item->opportunity?->title,
                    ],
                ];
            })->values();

        return $this->success([
            'total_opportunities' => $totalOpportunities,
            'active_interns' => $activeInterns,
            'pending_requests_count' => $pendingRequests->count(),
            'latest_pending_requests' => $latestPendingRequests,
        ]);
    }

    public function listOpportunities(Request $request): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $opportunities = TrainingOpportunity::query()
            ->where('institution_id', $institution->institution_id)
            ->orderByDesc('opportunity_id')
            ->paginate($this->perPage($request));

        return $this->success([
            'items' => $opportunities->items(),
            'meta' => $this->paginateMeta($opportunities),
        ]);
    }

    public function storeOpportunity(Request $request): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'department' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'required_skills' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'application_deadline' => ['nullable', 'date', 'before_or_equal:start_date'],
            'available_seats' => ['required', 'integer', 'min:1'],
            'city' => ['nullable', 'string', 'max:120'],
            'training_type' => ['nullable', Rule::in(['summer', 'cooperative'])],
            'custom_questions' => ['nullable', 'array'],
            'custom_questions.*' => ['required', 'string', 'max:500'],
            'status' => ['nullable', Rule::in(['active', 'closed'])],
        ]);

        $status = $data['status'] ?? 'active';
        $data['status'] = $status;
        $data['is_active'] = $status === 'active';
        $data['institution_id'] = $institution->institution_id;

        $opportunity = TrainingOpportunity::query()->create($data);

        return $this->success($opportunity, 'Training opportunity created successfully.', 201);
    }

    public function showOpportunity(Request $request, string $id): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $opportunity = TrainingOpportunity::query()
            ->where('institution_id', $institution->institution_id)
            ->find($id);

        if (!$opportunity) {
            return $this->error('Training opportunity not found or access denied.', 404);
        }

        return $this->success($opportunity);
    }

    public function updateOpportunity(Request $request, string $id): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $opportunity = TrainingOpportunity::query()
            ->where('institution_id', $institution->institution_id)
            ->find($id);

        if (!$opportunity) {
            return $this->error('Training opportunity not found or update access denied.', 404);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:200'],
            'department' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'required_skills' => ['nullable', 'string'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'application_deadline' => ['nullable', 'date'],
            'available_seats' => ['sometimes', 'integer', 'min:1'],
            'city' => ['nullable', 'string', 'max:120'],
            'training_type' => ['nullable', Rule::in(['summer', 'cooperative'])],
            'custom_questions' => ['nullable', 'array'],
            'custom_questions.*' => ['required', 'string', 'max:500'],
            'status' => ['sometimes', Rule::in(['active', 'closed'])],
        ]);

        if (array_key_exists('status', $data)) {
            $data['is_active'] = $data['status'] === 'active';
        }

        $opportunity->update($data);

        return $this->success($opportunity->fresh(), 'Training opportunity updated successfully.');
    }

    public function changeOpportunityStatus(Request $request, string $id): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'closed'])],
        ]);

        $opportunity = TrainingOpportunity::query()
            ->where('institution_id', $institution->institution_id)
            ->find($id);

        if (!$opportunity) {
            return $this->error('Training opportunity not found or update access denied.', 404);
        }

        $opportunity->update([
            'status' => $data['status'],
            'is_active' => $data['status'] === 'active',
        ]);

        return $this->success([
            'opportunity_id' => $opportunity->opportunity_id,
            'status' => $opportunity->status,
        ], 'Training opportunity status updated successfully.');
    }

    public function listRequests(Request $request): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $requests = TrainingRequest::query()
            ->with(['student.user', 'opportunity'])
            ->where('status', 'pending_institution')
            ->whereHas('opportunity', function ($q) use ($institution) {
                $q->where('institution_id', $institution->institution_id);
            })
            ->orderByDesc('request_id')
            ->paginate($this->perPage($request));

        return $this->success([
            'items' => collect($requests->items())->map(function (TrainingRequest $item) {
                return [
                    'request_id' => $item->request_id,
                    'submission_date' => $item->submission_date,
                    'status' => $item->status,
                    'student' => [
                        'student_id' => $item->student?->student_id,
                        'full_name' => $item->student?->user?->full_name,
                        'department' => $item->student?->department,
                        'gpa' => $item->student?->gpa,
                    ],
                    'opportunity' => [
                        'opportunity_id' => $item->opportunity?->opportunity_id,
                        'title' => $item->opportunity?->title,
                    ],
                ];
            })->values(),
            'meta' => $this->paginateMeta($requests),
        ]);
    }

    public function showRequest(Request $request, string $id): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $trainingRequest = TrainingRequest::query()
            ->with(['student.user', 'opportunity', 'documents'])
            ->whereHas('opportunity', function ($q) use ($institution) {
                $q->where('institution_id', $institution->institution_id);
            })
            ->find($id);

        if (!$trainingRequest) {
            return $this->error('Training request not found or access denied.', 404);
        }

        $cv = $trainingRequest->documents
            ->first(fn ($document) => str_contains(strtolower((string) $document->file_type), 'pdf'));

        return $this->success([
            'request_id' => $trainingRequest->request_id,
            'status' => $trainingRequest->status,
            'submission_date' => $trainingRequest->submission_date,
            'student_answers' => $trainingRequest->student_answers,
            'student' => [
                'student_id' => $trainingRequest->student?->student_id,
                'full_name' => $trainingRequest->student?->user?->full_name,
                'email' => $trainingRequest->student?->user?->email,
                'phone' => $trainingRequest->student?->user?->phone,
                'university' => $trainingRequest->student?->university,
                'gpa' => $trainingRequest->student?->gpa,
                'department' => $trainingRequest->student?->department,
                'level' => $trainingRequest->student?->level,
            ],
            'opportunity' => [
                'opportunity_id' => $trainingRequest->opportunity?->opportunity_id,
                'title' => $trainingRequest->opportunity?->title,
            ],
            'cv' => [
                'document_id' => $cv?->document_id,
                'title' => $cv?->title,
                'file_url' => $cv?->file_url,
                'file_type' => $cv?->file_type,
            ],
        ]);
    }

    public function acceptRequest(Request $request, string $id): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $trainingRequest = TrainingRequest::query()
            ->whereHas('opportunity', function ($q) use ($institution) {
                $q->where('institution_id', $institution->institution_id);
            })
            ->find($id);

        if (!$trainingRequest) {
            return $this->error('Training request not found or access denied.', 404);
        }

        if ($trainingRequest->status !== 'pending_institution') {
            return $this->error('This request cannot be accepted in its current status.', 422);
        }

        DB::transaction(function () use ($trainingRequest) {
            $trainingRequest->update([
                'status' => 'approved',
            ]);

            Internship::query()->firstOrCreate(
                ['request_id' => $trainingRequest->request_id],
                [
                    'status' => 'active',
                    'actual_start_date' => now()->toDateString(),
                    'is_active' => true,
                ]
            );
        });

        return $this->success([
            'request_id' => $trainingRequest->request_id,
            'status' => 'approved',
        ], 'Student accepted and moved to active internship.');
    }

    public function rejectRequest(Request $request, string $id): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $data = $request->validate([
            'institution_notes' => ['nullable', 'string'],
        ]);

        $trainingRequest = TrainingRequest::query()
            ->whereHas('opportunity', function ($q) use ($institution) {
                $q->where('institution_id', $institution->institution_id);
            })
            ->find($id);

        if (!$trainingRequest) {
            return $this->error('Training request not found or access denied.', 404);
        }

        if (!in_array($trainingRequest->status, ['pending_institution', 'pending_admin', 'pending'], true)) {
            return $this->error('This request cannot be rejected in its current status.', 422);
        }

        $trainingRequest->update([
            'status' => 'rejected',
            'institution_notes' => $data['institution_notes'] ?? null,
        ]);

        return $this->success([
            'request_id' => $trainingRequest->request_id,
            'status' => 'rejected',
        ], 'Training request rejected successfully.');
    }

    public function listInternships(Request $request): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $internships = Internship::query()
            ->with(['trainingRequest.student.user', 'trainingRequest.opportunity'])
            ->whereHas('trainingRequest.opportunity', function ($q) use ($institution) {
                $q->where('institution_id', $institution->institution_id);
            })
            ->orderByDesc('internship_id')
            ->paginate($this->perPage($request));

        return $this->success([
            'items' => collect($internships->items())->map(function (Internship $item) {
                return [
                    'internship_id' => $item->internship_id,
                    'status' => $item->status,
                    'actual_start_date' => $item->actual_start_date,
                    'actual_end_date' => $item->actual_end_date,
                    'student' => [
                        'student_id' => $item->trainingRequest?->student?->student_id,
                        'full_name' => $item->trainingRequest?->student?->user?->full_name,
                        'department' => $item->trainingRequest?->student?->department,
                    ],
                    'opportunity' => [
                        'opportunity_id' => $item->trainingRequest?->opportunity?->opportunity_id,
                        'title' => $item->trainingRequest?->opportunity?->title,
                    ],
                ];
            })->values(),
            'meta' => $this->paginateMeta($internships),
        ]);
    }

    public function internshipReports(Request $request, string $id): JsonResponse
    {
        $internship = $this->ownedInternship($request, $id);
        if ($internship instanceof JsonResponse) {
            return $internship;
        }

        $reports = TrainingReport::query()
            ->where('internship_id', $internship->internship_id)
            ->where('is_active', true)
            ->orderByDesc('report_id')
            ->get();

        return $this->success($reports);
    }

    public function evaluateInternship(Request $request, string $id): JsonResponse
    {
        $internship = $this->ownedInternship($request, $id);
        if ($internship instanceof JsonResponse) {
            return $internship;
        }

        $data = $request->validate([
            'score' => ['required', 'integer', 'min:1', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $evaluation = Evaluation::query()->updateOrCreate(
            [
                'internship_id' => $internship->internship_id,
                'evaluator_type' => 'institution',
            ],
            [
                'final_score' => $data['score'],
                'overall_rating' => max(1, (int) ceil($data['score'] / 20)),
                'comments' => $data['notes'] ?? null,
                'evaluation_date' => now()->toDateString(),
                'is_active' => true,
            ]
        );

        return $this->success($evaluation, 'Evaluation saved and submitted successfully.');
    }

    public function listComplaints(Request $request): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $complaints = Complaint::query()
            ->where('user_id', $institution->user_id)
            ->orderByDesc('complaint_id')
            ->paginate($this->perPage($request));

        return $this->success([
            'items' => $complaints->items(),
            'meta' => $this->paginateMeta($complaints),
        ]);
    }

    public function storeComplaint(Request $request): JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
        ]);

        $complaint = Complaint::query()->create([
            'user_id' => $institution->user_id,
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => 'pending',
        ]);

        return $this->success($complaint, 'Complaint submitted successfully.', 201);
    }

    public function studentStoreRequest(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || $user->user_type !== 'student' || !$user->student) {
            return $this->error('This endpoint is for students only.', 403);
        }

        $data = $request->validate([
            'opportunity_id' => ['required', 'exists:training_opportunities,opportunity_id'],
            'student_notes' => ['nullable', 'string'],
            'student_answers_block' => ['nullable', 'string'],
        ]);

        $exists = TrainingRequest::query()
            ->where('student_id', $user->student->student_id)
            ->where('opportunity_id', $data['opportunity_id'])
            ->whereIn('status', ['pending_admin', 'pending_institution', 'approved'])
            ->exists();

        if ($exists) {
            return $this->error('A previous request for this opportunity already exists.', 422);
        }

        $trainingRequest = TrainingRequest::query()->create([
            'student_id' => $user->student->student_id,
            'opportunity_id' => $data['opportunity_id'],
            'submission_date' => now()->toDateString(),
            'status' => 'pending_admin',
            'student_notes' => $data['student_notes'] ?? null,
            'student_answers' => $data['student_answers_block'] ?? null,
            'is_active' => true,
        ]);

        return $this->success($trainingRequest, 'Training request submitted successfully.', 201);
    }

    private function institutionFromRequest(Request $request, bool $requireActive): Institution|JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->error('Unauthorized. Please login first.', 401);
        }

        if ($user->user_type !== 'institution') {
            return $this->error('This endpoint is for institutions only.', 403);
        }

        $institution = $user->institution()->with('user')->first();
        if (!$institution) {
            return $this->error('No institution profile found for this account.', 404);
        }

        if ($requireActive && (!$user->is_active || $user->status !== 'active')) {
            return $this->error(self::ACCOUNT_BLOCKED_MESSAGE, 403, [
                'status' => $user->status,
                'requires_activation' => true,
            ]);
        }

        return $institution;
    }

    private function ownedInternship(Request $request, string $id): Internship|JsonResponse
    {
        $institution = $this->institutionFromRequest($request, true);
        if ($institution instanceof JsonResponse) {
            return $institution;
        }

        $internship = Internship::query()
            ->where('internship_id', $id)
            ->whereHas('trainingRequest.opportunity', function ($q) use ($institution) {
                $q->where('institution_id', $institution->institution_id);
            })
            ->first();

        if (!$internship) {
            return $this->error('Internship record not found or access denied.', 404);
        }

        return $internship;
    }

    private function profilePayload(Institution $institution): array
    {
        return [
            'institution_id' => $institution->institution_id,
            'user_id' => $institution->user_id,
            'status' => $institution->user?->status,
            'is_active' => (bool) $institution->user?->is_active,
            'name' => $institution->name,
            'description' => $institution->description,
            'website' => $institution->website,
            'contact_person' => $institution->contact_person,
            'contact_phone' => $institution->contact_phone,
            'address' => $institution->address,
            'social_links' => $institution->social_links,
            'commercial_register' => $institution->commercial_register,
            'logo_path' => $institution->user?->profile_picture,
            'logo_url' => $institution->user?->profile_picture
                ? Storage::disk('public')->url($institution->user->profile_picture)
                : null,
        ];
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

