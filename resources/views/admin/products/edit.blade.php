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

    <h3>صور المنتج</h3>

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