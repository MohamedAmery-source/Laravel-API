<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::query()->get();

        return response()->json([
            'success' => true,
            'data' => ComplaintResource::collection($complaints),
        ], 200);
    }

    public function store(StoreComplaintRequest $request)
    {
        $complaint = Complaint::create([
            'user_id' => $request->user()->user_id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'pending',
            'resolved_at' => $request->resolved_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الشكوى بنجاح',
            'data' => new ComplaintResource($complaint),
        ], 201);
    }

    public function show(string $id)
    {
        $complaint = Complaint::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new ComplaintResource($complaint),
        ], 200);
    }
}
