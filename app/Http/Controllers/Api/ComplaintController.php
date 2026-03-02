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

        return $this->success(ComplaintResource::collection($complaints), null, 200);
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

        return $this->success(new ComplaintResource($complaint), 'تم إرسال الشكوى بنجاح', 201);
    }

    public function show(string $id)
    {
        $complaint = Complaint::findOrFail($id);

        return $this->success(new ComplaintResource($complaint), null, 200);
    }
}
