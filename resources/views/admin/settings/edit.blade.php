@extends('layouts.admin')

@section('title', 'إعدادات المتجر')

@section('content')

    <h2>إعدادات المتجر</h2>

    @if (session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.settings.update') }}"
    >
        @csrf
        @method('PUT')

        <div>
            <label for="store_name_ar">
                اسم المتجر
            </label>

            <input
                type="text"
                id="store_name_ar"
                name="store_name_ar"
                value="{{ old('store_name_ar', $setting->store_name_ar) }}"
                required
            >
        </div>

        <div>
            <label for="phone">
                رقم الهاتف
            </label>

            <input
                type="text"
                id="phone"
                name="phone"
                value="{{ old('phone', $setting->phone) }}"
            >
        </div>

        <div>
            <label for="whatsapp">
                رقم WhatsApp
            </label>

            <input
                type="text"
                id="whatsapp"
                name="whatsapp"
                value="{{ old('whatsapp', $setting->whatsapp) }}"
            >
        </div>

        <div>
            <label for="instagram_url">
                رابط Instagram
            </label>

            <input
                type="url"
                id="instagram_url"
                name="instagram_url"
                value="{{ old('instagram_url', $setting->instagram_url) }}"
                placeholder="https://instagram.com/..."
            >
        </div>

        <div>
            <label for="whish_number">
                رقم Whish Money
            </label>

            <input
                type="text"
                id="whish_number"
                name="whish_number"
                value="{{ old('whish_number', $setting->whish_number) }}"
            >
        </div>

        <div>
            <label for="currency">
                العملة
            </label>

            <input
                type="text"
                id="currency"
                name="currency"
                value="{{ old('currency', $setting->currency) }}"
                maxlength="3"
                required
            >
        </div>

        <div>
            <label for="reservation_hours">
                مدة حجز المنتج بالساعات
            </label>

            <input
                type="number"
                id="reservation_hours"
                name="reservation_hours"
                value="{{ old(
                    'reservation_hours',
                    $setting->reservation_hours
                ) }}"
                min="1"
                required
            >
        </div>

        <div>
            <label for="about_text_ar">
                نص "من نحن"
            </label>

            <textarea
                id="about_text_ar"
                name="about_text_ar"
                rows="5"
            >{{ old('about_text_ar', $setting->about_text_ar) }}</textarea>
        </div>

        <div>
            <label for="policy_text_ar">
                سياسة المتجر
            </label>

            <textarea
                id="policy_text_ar"
                name="policy_text_ar"
                rows="7"
            >{{ old('policy_text_ar', $setting->policy_text_ar) }}</textarea>
        </div>

        <button type="submit">
            حفظ الإعدادات
        </button>
    </form>

@endsection