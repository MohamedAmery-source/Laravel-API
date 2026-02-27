<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LookupMaster;
use App\Models\LookupValue;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Type query parameter is required',
            ], 422);
        }

        $master = LookupMaster::where('lookup_code', $type)->first();

        if (!$master) {
            return response()->json([
                'success' => true,
                'data' => [],
            ], 200);
        }

        $values = LookupValue::where('lookup_id', $master->lookup_id)->get();

        return response()->json([
            'success' => true,
            'data' => $values,
        ], 200);
    }
}
