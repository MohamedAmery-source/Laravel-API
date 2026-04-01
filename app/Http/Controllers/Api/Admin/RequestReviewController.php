<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Admin\AdminApproveRequestRequest;
use App\Http\Requests\Admin\AdminRejectRequestRequest;
use App\Models\Notification;
use App\Models\TrainingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestReviewController extends AdminController
{
    public function index(Request $request)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $status = $request->query('status');

        $query = TrainingRequest::query()
            ->with(['student.user', 'opportunity.institution.user'])
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            }, function ($q) {
                $q->whereIn('status', ['pending_admin', 'pending']);
            });

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($q) use ($search) {
                $q->whereHas('student.user', function ($u) use ($search) {
                    $u->where('full_name', 'like', "%{$search}%");
                })->orWhereHas('opportunity', function ($o) use ($search) {
                    $o->where('title', 'like', "%{$search}%");
                })->orWhereHas('opportunity.institution', function ($i) use ($search) {
                    $i->where('name', 'like', "%{$search}%");
                });
            });
        }

        $requests = $query->orderByDesc('request_id')->paginate($this->perPage($request));

        $data = [
            'items' => $requests->getCollection()->map(function (TrainingRequest $item) {
                return [
                    'request_id' => $item->request_id,
                    'status' => $item->status,
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
                        'institution_name' => $item->opportunity?->institution?->name,
                    ],
                ];
            })->values(),
            'meta' => $this->paginateMeta($requests),
        ];

        return $this->success($data);
    }

    public function show(Request $request, string $id)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $trainingRequest = TrainingRequest::query()
            ->with([
                'student.user',
                'opportunity.institution.user',
                'documents',
            ])
            ->find($id);
        if (!$trainingRequest) {
            return $this->error('Training request not found for the provided id.', 404);
        }

        $data = [
            'request_id' => $trainingRequest->request_id,
            'status' => $trainingRequest->status,
            'submission_date' => $trainingRequest->submission_date,
            'student_notes' => $trainingRequest->student_notes,
            'admin_notes' => $trainingRequest->admin_notes,
            'institution_notes' => $trainingRequest->institution_notes,
            'student' => [
                'student_id' => $trainingRequest->student?->student_id,
                'user_id' => $trainingRequest->student?->user?->user_id,
                'full_name' => $trainingRequest->student?->user?->full_name,
                'email' => $trainingRequest->student?->user?->email,
                'department' => $trainingRequest->student?->department,
                'level' => $trainingRequest->student?->level,
                'gpa' => $trainingRequest->student?->gpa,
            ],
            'opportunity' => [
                'opportunity_id' => $trainingRequest->opportunity?->opportunity_id,
                'title' => $trainingRequest->opportunity?->title,
                'description' => $trainingRequest->opportunity?->description,
                'required_skills' => $trainingRequest->opportunity?->required_skills,
                'institution' => [
                    'institution_id' => $trainingRequest->opportunity?->institution?->institution_id,
                    'name' => $trainingRequest->opportunity?->institution?->name,
                    'email' => $trainingRequest->opportunity?->institution?->user?->email,
                ],
            ],
            'documents' => $trainingRequest->documents->map(function ($document) {
                return [
                    'document_id' => $document->document_id,
                    'title' => $document->title,
                    'file_url' => $document->file_url,
                    'file_type' => $document->file_type,
                    'uploaded_at' => $document->created_at,
                ];
            })->values(),
        ];

        return $this->success($data);
    }

    public function approve(AdminApproveRequestRequest $request, string $id)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $trainingRequest = TrainingRequest::query()
            ->with(['student.user', 'opportunity.institution.user'])
            ->find($id);
        if (!$trainingRequest) {
            return $this->error('Training request not found for the provided id.', 404);
        }

        if (!in_array($trainingRequest->status, ['pending_admin', 'pending'], true)) {
            return $this->error('Only pending admin requests can be approved.', 422);
        }

        $data = $request->validated();

        DB::transaction(function () use ($trainingRequest, $data) {
            $trainingRequest->update([
                'status' => 'pending_institution',
                'admin_notes' => $data['admin_notes'] ?? null,
            ]);

            if ($trainingRequest->student?->user_id) {
                Notification::query()->create([
                    'user_id' => $trainingRequest->student->user_id,
                    'message' => 'Your request has been approved academically and forwarded to the institution.',
                    'notification_type' => 'request_forwarded',
                    'related_request_id' => $trainingRequest->request_id,
                    'is_read' => false,
                ]);
            }
        });

        return $this->success([
            'request_id' => $trainingRequest->request_id,
            'status' => 'pending_institution',
        ], 'Request approved and forwarded successfully.');
    }

    public function reject(AdminRejectRequestRequest $request, string $id)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $trainingRequest = TrainingRequest::query()->with('student.user')->find($id);
        if (!$trainingRequest) {
            return $this->error('Training request not found for the provided id.', 404);
        }

        if (!in_array($trainingRequest->status, ['pending_admin', 'pending'], true)) {
            return $this->error('Only pending admin requests can be rejected.', 422);
        }

        $data = $request->validated();

        DB::transaction(function () use ($trainingRequest, $data) {
            $trainingRequest->update([
                'status' => 'rejected',
                'admin_notes' => $data['admin_notes'],
            ]);

            if ($trainingRequest->student?->user_id) {
                Notification::query()->create([
                    'user_id' => $trainingRequest->student->user_id,
                    'message' => 'Your request was rejected academically. Reason: ' . $data['admin_notes'],
                    'notification_type' => 'request_rejected',
                    'related_request_id' => $trainingRequest->request_id,
                    'is_read' => false,
                ]);
            }
        });

        return $this->success([
            'request_id' => $trainingRequest->request_id,
            'status' => 'rejected',
            'admin_notes' => $data['admin_notes'],
        ], 'Request rejected successfully.');
    }
}
