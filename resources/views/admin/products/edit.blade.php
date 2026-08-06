@extends('layouts.admin')

@section('title', 'تعديل المنتج')

@section('content')

    <h2>تعديل المنتج</h2>

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
        action="{{ route('admin.products.update', $product) }}"
    >
        @csrf
        @method('PUT')

        @include('admin.products._form')

        <button type="submit">
            حفظ التعديلات
        </button>
    </form>

    <hr>
<hr>

<h3>صور المنتج</h3>

<form
    method="POST"
    action="{{ route('admin.products.images.store', $product) }}"
    enctype="multipart/form-data"
>
    @csrf

    <div>
        <label for="images">
            إضافة صور
        </label>

        <input
            type="file"
            id="images"
            name="images[]"
            accept=".jpg,.jpeg,.png,.webp"
            multiple
            required
        >

        <small>
            يمكنك اختيار أكثر من صورة.
            الحد الأقصى للصورة الواحدة 5MB.
        </small>
    </div>

    <button type="submit">
        رفع الصور
    </button>
</form>

<hr>

@if ($product->images->isEmpty())

    <p>
        لا توجد صور للمنتج حتى الآن.
    </p>

@else

    <form
        method="POST"
        action="{{ route('admin.products.images.order', $product) }}"
    >
        @csrf
        @method('PATCH')

        @foreach ($product->images as $image)

            <div>

                <img
                    src="{{ $image->url }}"
                    alt="{{ $product->name_ar }}"
                    width="150"
                >

                @if ($image->is_primary)
                    <strong>
                        الصورة الرئيسية
                    </strong>
                @endif

                <div>
                    <label>
                        الترتيب
                    </label>

                    <input
                        type="number"
                        name="orders[{{ $image->id }}]"
                        value="{{ $image->sort_order }}"
                        min="0"
                    >
                </div>

            </div>

        @endforeach

        <button type="submit">
            حفظ ترتيب الصور
        </button>
    </form>

    <hr>

    @foreach ($product->images as $image)

        @unless ($image->is_primary)

            <form
                method="POST"
                action="{{ route(
                    'admin.products.images.primary',
                    [$product, $image]
                ) }}"
            >
                @csrf
                @method('PATCH')

                <button type="submit">
                    تعيين كصورة رئيسية
                </button>
            </form>

        @endunless

        <form
            method="POST"
            action="{{ route(
                'admin.products.images.destroy',
                [$product, $image]
            ) }}"
            onsubmit="return confirm(
                'هل أنت متأكد من حذف الصورة؟'
            );"
        >
            @csrf
            @method('DELETE')

            <button type="submit">
                حذف الصورة
            </button>
        </form>

        <hr>

    @endforeach

@endif

    @if ($product->images->isEmpty())
        <p>
            لا توجد صور حاليًا.
        </p>
    @else
        @foreach ($product->images as $image)
            <div>
                <img
                    src="{{ $image->url }}"
                    alt="{{ $product->name_ar }}"
                    width="120"
                >

                @if ($image->is_primary)
                    <strong>الصورة الرئيسية</strong>
                @endif
            </div>
        @endforeach
    @endif

    <p>
        إدارة الصور سنضيفها في الخطوة التالية.
    </p>

@endsection