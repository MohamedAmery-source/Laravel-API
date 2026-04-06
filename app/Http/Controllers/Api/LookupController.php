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
            return $this->error('المعامل type مطلوب في الاستعلام.', 422);
        }

        $master = LookupMaster::where('lookup_code', $type)->first();

        if (!$master) {
            return $this->success([], null, 200);
        }

        $values = LookupValue::where('lookup_id', $master->lookup_id)->get();

        return $this->success($values, null, 200);
    }
}
