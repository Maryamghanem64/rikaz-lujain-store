@extends('layouts.admin')

@section('title', 'إدارة المنتجات')

@section('content')

    <h2>إدارة المنتجات</h2>

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

    <p>
        <a href="{{ route('admin.products.create') }}">
            إضافة منتج جديد
        </a>
    </p>

    @forelse ($products as $product)

        <article>

            @if ($product->primaryImage)
                <img
                    src="{{ $product->primaryImage->url }}"
                    alt="{{ $product->name_ar }}"
                    width="120"
                >
            @endif

            <h3>
                {{ $product->name_ar }}
            </h3>

            <p>
                القسم:
                {{ $product->category->section->name_ar }}
            </p>

            <p>
                الفئة:
                {{ $product->category->name_ar }}
            </p>

            <p>
                السعر:
                ${{ $product->price }}
            </p>

            <p>
                المخزون:
                {{ $product->stock_quantity }}
            </p>

            <p>
                المحجوز:
                {{ $product->reserved_quantity }}
            </p>

            <p>
                المتاح:
                {{ $product->available_quantity }}
            </p>

            <p>
                الحالة:
                {{ $product->is_active ? 'فعّال' : 'مخفي' }}
            </p>

            @if ($product->is_featured)
                <p>
                    ⭐ منتج مميز
                </p>
            @endif

            <a href="{{ route('admin.products.edit', $product) }}">
                تعديل
            </a>

            <form
                method="POST"
                action="{{ route('admin.products.destroy', $product) }}"
                onsubmit="return confirm('هل أنت متأكد؟');"
            >
                @csrf
                @method('DELETE')

                <button type="submit">
                    حذف
                </button>
            </form>

        </article>

        <hr>

    @empty

        <p>لا توجد منتجات حتى الآن.</p>

    @endforelse

    {{ $products->links() }}

@endsection