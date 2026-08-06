@extends('layouts.admin')

@section('title', 'مناطق التوصيل')

@section('content')

    <h2>إدارة مناطق التوصيل</h2>

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

    <hr>

    <h3>إضافة منطقة جديدة</h3>

    <form
        method="POST"
        action="{{ route('admin.delivery-zones.store') }}"
    >
        @csrf

        <div>
            <label for="name_ar">
                اسم المنطقة
            </label>

            <input
                type="text"
                id="name_ar"
                name="name_ar"
                value="{{ old('name_ar') }}"
                placeholder="مثال: بيروت"
                required
            >
        </div>

        <div>
            <label for="fee">
                رسم التوصيل ($)
            </label>

            <input
                type="number"
                id="fee"
                name="fee"
                value="{{ old('fee') }}"
                step="0.01"
                min="0"
                required
            >
        </div>

        <div>
            <label for="sort_order">
                الترتيب
            </label>

            <input
                type="number"
                id="sort_order"
                name="sort_order"
                value="{{ old('sort_order', 0) }}"
                min="0"
            >
        </div>

        <label>
            <input
                type="checkbox"
                name="is_active"
                value="1"
                checked
            >

            فعّالة
        </label>

        <br>

        <button type="submit">
            إضافة المنطقة
        </button>
    </form>

    <hr>

    <h3>المناطق الحالية</h3>

    @forelse ($zones as $zone)

        <div>

            <form
                method="POST"
                action="{{ route(
                    'admin.delivery-zones.update',
                    $zone
                ) }}"
            >
                @csrf
                @method('PUT')

                <div>
                    <label>
                        اسم المنطقة
                    </label>

                    <input
                        type="text"
                        name="name_ar"
                        value="{{ $zone->name_ar }}"
                        required
                    >
                </div>

                <div>
                    <label>
                        رسم التوصيل ($)
                    </label>

                    <input
                        type="number"
                        name="fee"
                        value="{{ $zone->fee }}"
                        step="0.01"
                        min="0"
                        required
                    >
                </div>

                <div>
                    <label>
                        الترتيب
                    </label>

                    <input
                        type="number"
                        name="sort_order"
                        value="{{ $zone->sort_order }}"
                        min="0"
                    >
                </div>

                <label>
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked($zone->is_active)
                    >

                    فعّالة
                </label>

                <br>

                <button type="submit">
                    حفظ التعديلات
                </button>
            </form>

            <form
                method="POST"
                action="{{ route(
                    'admin.delivery-zones.destroy',
                    $zone
                ) }}"
                onsubmit="return confirm(
                    'هل أنت متأكد من حذف هذه المنطقة؟'
                );"
            >
                @csrf
                @method('DELETE')

                <button type="submit">
                    حذف
                </button>
            </form>

        </div>

        <hr>

    @empty

        <p>
            لم تتم إضافة مناطق توصيل بعد.
        </p>

    @endforelse

@endsection