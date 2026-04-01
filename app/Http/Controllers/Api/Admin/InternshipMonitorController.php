<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Internship;
use Illuminate\Http\Request;

class InternshipMonitorController extends AdminController
{
    public function index(Request $request)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $query = Internship::query()->with([
            'trainingRequest.student.user',
            'trainingRequest.opportunity.institution',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('student_id')) {
            $studentId = $request->query('student_id');
            $query->whereHas('trainingRequest', function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            });
        }

        if ($request->filled('institution_id')) {
            $institutionId = $request->query('institution_id');
            $query->whereHas('trainingRequest.opportunity', function ($q) use ($institutionId) {
                $q->where('institution_id', $institutionId);
            });
        }

        $internships = $query->orderByDesc('internship_id')->paginate($this->perPage($request));

        $data = [
            'items' => $internships->getCollection()->map(function (Internship $internship) {
                return [
                    'internship_id' => $internship->internship_id,
                    'request_id' => $internship->request_id,
                    'status' => $internship->status,
                    'actual_start_date' => $internship->actual_start_date,
                    'actual_end_date' => $internship->actual_end_date,
                    'mentor_name' => $internship->mentor_name,
                    'student' => [
                        'student_id' => $internship->trainingRequest?->student?->student_id,
                        'full_name' => $internship->trainingRequest?->student?->user?->full_name,
                    ],
                    'institution' => [
                        'institution_id' => $internship->trainingRequest?->opportunity?->institution?->institution_id,
                        'name' => $internship->trainingRequest?->opportunity?->institution?->name,
                    ],
                    'opportunity' => [
                        'opportunity_id' => $internship->trainingRequest?->opportunity?->opportunity_id,
                        'title' => $internship->trainingRequest?->opportunity?->title,
                    ],
                ];
            })->values(),
            'meta' => $this->paginateMeta($internships),
        ];

        return $this->success($data);
    }

    public function show(Request $request, string $id)
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $internship = Internship::query()
            ->with([
                'trainingRequest.student.user',
                'trainingRequest.opportunity.institution.user',
                'reports',
                'evaluations',
            ])
            ->find($id);
        if (!$internship) {
            return $this->error('Internship not found for the provided id.', 404);
        }

        $data = [
            'internship_id' => $internship->internship_id,
            'request_id' => $internship->request_id,
            'status' => $internship->status,
            'actual_start_date' => $internship->actual_start_date,
            'actual_end_date' => $internship->actual_end_date,
            'mentor_name' => $internship->mentor_name,
            'assigned_tasks' => $internship->assigned_tasks,
            'student' => [
                'student_id' => $internship->trainingRequest?->student?->student_id,
                'full_name' => $internship->trainingRequest?->student?->user?->full_name,
                'email' => $internship->trainingRequest?->student?->user?->email,
                'department' => $internship->trainingRequest?->student?->department,
            ],
            'institution' => [
                'institution_id' => $internship->trainingRequest?->opportunity?->institution?->institution_id,
                'name' => $internship->trainingRequest?->opportunity?->institution?->name,
                'email' => $internship->trainingRequest?->opportunity?->institution?->user?->email,
            ],
            'opportunity' => [
                'opportunity_id' => $internship->trainingRequest?->opportunity?->opportunity_id,
                'title' => $internship->trainingRequest?->opportunity?->title,
                'description' => $internship->trainingRequest?->opportunity?->description,
            ],
            'reports' => $internship->reports->map(function ($report) {
                return [
                    'report_id' => $report->report_id,
                    'title' => $report->title,
                    'content' => $report->content,
                    'report_file' => $report->report_file,
                    'submitted_by' => $report->submitted_by,
                    'submission_date' => $report->submission_date,
                    'is_approved' => $report->is_approved,
                    'supervisor_comments' => $report->supervisor_comments,
                ];
            })->values(),
            'evaluations' => $internship->evaluations->map(function ($evaluation) {
                return [
                    'evaluation_id' => $evaluation->evaluation_id,
                    'evaluator_type' => $evaluation->evaluator_type,
                    'technical_skills' => $evaluation->technical_skills,
                    'commitment' => $evaluation->commitment,
                    'teamwork' => $evaluation->teamwork,
                    'attendance' => $evaluation->attendance,
                    'overall_rating' => $evaluation->overall_rating,
                    'comments' => $evaluation->comments,
                    'evaluation_date' => $evaluation->evaluation_date,
                ];
            })->values(),
        ];

        return $this->success($data);
    }
}
