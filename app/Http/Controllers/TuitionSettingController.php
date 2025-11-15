<?php

namespace App\Http\Controllers;

use App\Models\TuitionSetting;
use Illuminate\Http\Request;

class TuitionSettingController extends Controller
{
    public function index()
    {
        $setting = TuitionSetting::first();

        if (! $setting) {
            $setting = TuitionSetting::create([
                'per_credit_amount' => 0,
            ]);
        }

        return view('tuition_settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'per_credit_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $setting = TuitionSetting::first();

        if (! $setting) {
            $setting = TuitionSetting::create($validated);
        } else {
            $setting->update($validated);
        }

        return redirect()
            ->route('tuition-settings.index')
            ->with('success', 'Cập nhật học phí mỗi tín chỉ thành công.');
    }
}
