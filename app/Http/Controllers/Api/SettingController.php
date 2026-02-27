<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = GeneralSetting::query()->first();

        if (!$setting) {
            return response()->json([
                'success' => true,
                'data' => null,
            ], 200);
        }

        return response()->json([
            'success' => true,
            'data' => new SettingResource($setting),
        ], 200);
    }

    public function update(Request $request)
    {
        $setting = GeneralSetting::query()->first();

        if (!$setting) {
            $setting = GeneralSetting::create($request->all());
        } else {
            $setting->update($request->all());
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الإعدادات بنجاح',
            'data' => new SettingResource($setting),
        ], 200);
    }
}
