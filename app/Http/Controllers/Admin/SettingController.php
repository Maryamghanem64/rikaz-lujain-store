<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $setting = Setting::firstOrCreate(
            ['id' => 1],
            [
                'store_name_ar' => 'ركاز × لجين',
                'currency' => 'USD',
                'reservation_hours' => 24,
            ]
        );

        return view(
            'admin.settings.edit',
            compact('setting')
        );
    }

    public function update(Request $request): RedirectResponse
    {
        $setting = Setting::firstOrCreate(['id' => 1]);

        $validated = $request->validate([
            'store_name_ar' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'whatsapp' => [
                'nullable',
                'string',
                'max:50',
            ],

            'instagram_url' => [
                'nullable',
                'url',
                'max:500',
            ],

            'whish_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'reservation_hours' => [
                'required',
                'integer',
                'min:1',
            ],

            'about_text_ar' => [
                'nullable',
                'string',
            ],

            'policy_text_ar' => [
                'nullable',
                'string',
            ],
        ]);

        $setting->update($validated);

        return back()->with(
            'success',
            'تم تحديث إعدادات المتجر بنجاح.'
        );
    }
}
