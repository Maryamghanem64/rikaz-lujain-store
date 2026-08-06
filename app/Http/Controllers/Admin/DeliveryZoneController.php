<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryZoneController extends Controller
{
    public function index(): View
    {
        $zones = DeliveryZone::orderBy('sort_order')
            ->orderBy('name_ar')
            ->get();

        return view('admin.delivery-zones.index', compact('zones'));
    }
public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'name_ar' => [
            'required',
            'string',
            'max:255',
        ],

        'fee' => [
            'required',
            'numeric',
            'min:0',
        ],

        'sort_order' => [
            'nullable',
            'integer',
            'min:0',
        ],
    ]);

    $validated['is_active'] = $request->boolean('is_active');

    $validated['sort_order'] =
        $validated['sort_order'] ?? 0;

    DeliveryZone::create($validated);

    return back()->with(
        'success',
        'تمت إضافة منطقة التوصيل بنجاح.'
    );
}
    // public function store(Request $request): RedirectResponse
    // {
    //     $validated = $request->validate([
    //         'name_ar' => [
    //             'required',
    //             'string',
    //             'max:255',
    //         ],

    //         'fee' => [
    //             'required',
    //             'numeric',
    //             'min:0',
    //         ],

    //         'sort_order' => [
    //             'nullable',
    //             'integer',
    //             'min:0',
    //         ],
    //     ]);

    //     $validated['is_active'] =
    //         $request->boolean('is_active');

    //     $validated['sort_order'] =
    //         $validated['sort_order'] ?? 0;

    //     DeliveryZone::create($validated);

    //     return back()->with(
    //         'success',
    //         'تمت إضافة منطقة التوصيل بنجاح.'
    //     );
    // }

public function update(
        Request $request,
        DeliveryZone $deliveryZone
    ): RedirectResponse {
        $validated = $request->validate([
            'name_ar' => [
                'required',
                'string',
                'max:255',
            ],

            'fee' => [
                'required',
                'numeric',
                'min:0',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated['sort_order'] =
            $validated['sort_order'] ?? 0;

        $deliveryZone->update($validated);

        return back()->with(
            'success',
            'تم تحديث منطقة التوصيل بنجاح.'
        );
    }

    public function destroy(
        DeliveryZone $deliveryZone
    ): RedirectResponse {
        if ($deliveryZone->orders()->exists()) {
            $deliveryZone->update([
                'is_active' => false,
            ]);

            return back()->with(
                'success',
                'هذه المنطقة مرتبطة بطلبات سابقة، لذلك تم تعطيلها بدل حذفها.'
            );
        }

        $deliveryZone->delete();

        return back()->with(
            'success',
            'تم حذف منطقة التوصيل بنجاح.'
        );
    }
}