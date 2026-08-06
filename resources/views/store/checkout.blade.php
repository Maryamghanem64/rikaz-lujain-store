@extends('layouts.store')

@section('title', 'إتمام الطلب | ركاز × لجين')

@section('content')

@php
    $selectedZone = $zones->firstWhere(
        'id',
        (int) old('delivery_zone_id')
    );

    $initialDeliveryFee =
        (float) ($selectedZone?->fee ?? 0);
@endphp


<section class="mx-auto max-w-7xl px-4 py-12">

    <div class="mb-8">

        <h1 class="text-3xl font-bold">
            إتمام الطلب
        </h1>

        <p class="mt-2 text-stone-500">
            لا تحتاج إلى إنشاء حساب.
        </p>

    </div>


    @if ($errors->any())

        <div class="mb-6 rounded-xl bg-red-50 p-4 text-red-700">

            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('checkout.store') }}"
        enctype="multipart/form-data"

        x-data="{
            subtotal: {{ (float) $subtotal }},
            deliveryFee: {{ $initialDeliveryFee }},
            paymentMethod: @js(old('payment_method', 'cash'))
        }"
    >
        @csrf


        <div class="grid gap-8 lg:grid-cols-[1fr_380px]">


            {{-- CUSTOMER INFORMATION --}}
            <div class="space-y-8">


                <div
                    class="rounded-2xl border border-stone-200 bg-white p-6"
                >

                    <h2 class="mb-6 text-xl font-bold">
                        معلومات التواصل
                    </h2>


                    <div class="grid gap-5 md:grid-cols-2">

                        <div>

                            <label
                                for="customer_name"
                                class="mb-2 block font-medium"
                            >
                                الاسم الكامل *
                            </label>

                            <input
                                type="text"
                                id="customer_name"
                                name="customer_name"
                                value="{{ old('customer_name') }}"
                                class="w-full rounded-xl border border-stone-300 px-4 py-3"
                                required
                            >

                        </div>


                        <div>

                            <label
                                for="customer_phone"
                                class="mb-2 block font-medium"
                            >
                                رقم الهاتف *
                            </label>

                            <input
                                type="text"
                                id="customer_phone"
                                name="customer_phone"
                                value="{{ old('customer_phone') }}"
                                class="w-full rounded-xl border border-stone-300 px-4 py-3"
                                required
                            >

                        </div>


                        <div class="md:col-span-2">

                            <label
                                for="customer_whatsapp"
                                class="mb-2 block font-medium"
                            >
                                WhatsApp
                            </label>

                            <input
                                type="text"
                                id="customer_whatsapp"
                                name="customer_whatsapp"
                                value="{{ old('customer_whatsapp') }}"
                                class="w-full rounded-xl border border-stone-300 px-4 py-3"
                            >

                            <p class="mt-1 text-sm text-stone-500">
                                إذا تركته فارغًا سنستخدم رقم الهاتف.
                            </p>

                        </div>

                    </div>

                </div>



                {{-- DELIVERY --}}
                <div
                    class="rounded-2xl border border-stone-200 bg-white p-6"
                >

                    <h2 class="mb-6 text-xl font-bold">
                        معلومات التوصيل
                    </h2>


                    <div class="space-y-5">

                        <div>

                            <label
                                for="delivery_zone_id"
                                class="mb-2 block font-medium"
                            >
                                منطقة التوصيل *
                            </label>

                            <select
                                id="delivery_zone_id"
                                name="delivery_zone_id"
                                class="w-full rounded-xl border border-stone-300 px-4 py-3"
                                required

                                @change="
                                    deliveryFee = Number(
                                        $event
                                            .target
                                            .selectedOptions[0]
                                            ?.dataset
                                            .fee || 0
                                    )
                                "
                            >

                                <option
                                    value=""
                                    data-fee="0"
                                >
                                    اختر المنطقة
                                </option>


                                @foreach ($zones as $zone)

                                    <option
                                        value="{{ $zone->id }}"
                                        data-fee="{{ $zone->fee }}"
                                        @selected(
                                            old('delivery_zone_id')
                                                == $zone->id
                                        )
                                    >
                                        {{ $zone->name_ar }}
                                        —
                                        ${{ number_format((float) $zone->fee, 2) }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div>

                            <label
                                for="address"
                                class="mb-2 block font-medium"
                            >
                                العنوان بالتفصيل *
                            </label>

                            <textarea
                                id="address"
                                name="address"
                                rows="4"
                                class="w-full rounded-xl border border-stone-300 px-4 py-3"
                                required
                            >{{ old('address') }}</textarea>

                        </div>


                        <div>

                            <label
                                for="notes"
                                class="mb-2 block font-medium"
                            >
                                ملاحظات
                            </label>

                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                class="w-full rounded-xl border border-stone-300 px-4 py-3"
                            >{{ old('notes') }}</textarea>

                        </div>

                    </div>

                </div>



                {{-- PAYMENT --}}
                <div
                    class="rounded-2xl border border-stone-200 bg-white p-6"
                >

                    <h2 class="mb-6 text-xl font-bold">
                        طريقة الدفع
                    </h2>


                    <div class="space-y-4">

                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-xl border border-stone-200 p-4"
                        >

                            <input
                                type="radio"
                                name="payment_method"
                                value="cash"
                                x-model="paymentMethod"
                                required
                            >

                            <div>
                                <strong>
                                    الدفع نقدًا
                                </strong>

                                <p class="text-sm text-stone-500">
                                    سيتم التواصل معك لتأكيد الطلب.
                                </p>
                            </div>

                        </label>


                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-xl border border-stone-200 p-4"
                        >

                            <input
                                type="radio"
                                name="payment_method"
                                value="whish"
                                x-model="paymentMethod"
                            >

                            <div>
                                <strong>
                                    Whish Money
                                </strong>

                                <p class="text-sm text-stone-500">
                                    حوّل المبلغ وارفع صورة الإيصال.
                                </p>
                            </div>

                        </label>

                    </div>


                    {{-- WHISH --}}
                    <div
                        x-show="paymentMethod === 'whish'"
                        class="mt-6 rounded-xl bg-stone-50 p-5"
                    >

                        @if ($settings?->whish_number)

                            <p class="text-sm text-stone-500">
                                التحويل إلى رقم:
                            </p>

                            <p class="mt-1 text-lg font-bold">
                                {{ $settings->whish_number }}
                            </p>

                        @else

                            <p class="text-red-600">
                                لم يتم ضبط رقم Whish بعد من لوحة الإدارة.
                            </p>

                        @endif


                        <p class="mt-4 text-sm text-stone-500">
                            المبلغ المطلوب:
                        </p>

                        <strong
                            class="text-xl"
                            x-text="
                                '$' +
                                (subtotal + deliveryFee)
                                    .toFixed(2)
                            "
                        ></strong>


                        <div class="mt-5">

                            <label
                                for="payment_proof"
                                class="mb-2 block font-medium"
                            >
                                صورة إيصال التحويل *
                            </label>

                            <input
                                type="file"
                                id="payment_proof"
                                name="payment_proof"
                                accept=".jpg,.jpeg,.png,.webp"
                                :required="
                                    paymentMethod === 'whish'
                                "
                            >

                            <p class="mt-2 text-xs text-stone-500">
                                JPG / PNG / WebP — حتى 5MB
                            </p>

                        </div>

                    </div>

                </div>



                {{-- POLICY --}}
                <label class="flex items-start gap-3">

                    <input
                        type="checkbox"
                        name="policy_agreement"
                        value="1"
                        class="mt-1"
                        required
                        @checked(old('policy_agreement'))
                    >

                    <span class="text-sm text-stone-600">
                        أوافق على سياسة الدفع والتوصيل
                        وسياسة المتجر.
                    </span>

                </label>

            </div>



            {{-- SUMMARY --}}
            <aside>

                <div
                    class="sticky top-6 rounded-2xl border border-stone-200 bg-white p-6"
                >

                    <h2 class="text-xl font-bold">
                        ملخص الطلب
                    </h2>


                    <div class="mt-6 space-y-4">

                        @foreach ($items as $item)

                            <div class="flex justify-between gap-4">

                                <div>

                                    <p class="font-medium">
                                        {{ $item['product']->name_ar }}
                                    </p>

                                    <p class="text-sm text-stone-500">
                                        × {{ $item['quantity'] }}
                                    </p>

                                </div>

                                <strong>
                                    ${{ number_format($item['subtotal'], 2) }}
                                </strong>

                            </div>

                        @endforeach

                    </div>


                    <hr class="my-6">


                    <div class="flex justify-between">

                        <span>
                            مجموع المنتجات
                        </span>

                        <span>
                            ${{ number_format($subtotal, 2) }}
                        </span>

                    </div>


                    <div class="mt-3 flex justify-between">

                        <span>
                            التوصيل
                        </span>

                        <span
                            x-text="
                                '$' +
                                deliveryFee.toFixed(2)
                            "
                        ></span>

                    </div>


                    <div
                        class="mt-5 flex justify-between border-t border-stone-200 pt-5"
                    >

                        <strong>
                            المجموع النهائي
                        </strong>

                        <strong
                            class="text-xl"
                            x-text="
                                '$' +
                                (subtotal + deliveryFee)
                                    .toFixed(2)
                            "
                        ></strong>

                    </div>


                    <button
                        type="submit"
                        class="mt-6 w-full rounded-xl bg-stone-900 px-6 py-4 font-semibold text-white"
                    >
                        تأكيد الطلب
                    </button>


                    <p class="mt-3 text-center text-xs text-stone-500">
                        السعر ورسوم التوصيل يتم التحقق منهما
                        مرة أخرى من النظام عند إرسال الطلب.
                    </p>

                </div>

            </aside>

        </div>

    </form>

</section>

@endsection