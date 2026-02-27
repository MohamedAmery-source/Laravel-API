<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InternshipResource;
use App\Models\Internship;
use Illuminate\Http\Request;

class InternshipController extends Controller
{
    public function index(Request $request)
    {
        $query = Internship::query();

        if ($request->filled('request_id')) {
            $query->where('request_id', $request->request_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $internships = $query->get();

        return response()->json([
            'success' => true,
            'data' => InternshipResource::collection($internships),
        ], 200);
    }
}
