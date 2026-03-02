<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = GeneralSetting::query()->first();

        if (!$setting) {
            return $this->success(null, null, 200);
        }

        return $this->success(new SettingResource($setting), null, 200);
    }

    public function update(UpdateSettingRequest $request)
    {
        $setting = GeneralSetting::query()->first();

        if (!$setting) {
            $setting = GeneralSetting::create($request->validated());
        } else {
            $setting->update($request->validated());
        }

        return $this->success(new SettingResource($setting), 'تم تحديث الإعدادات بنجاح', 200);
    }
}
