@extends('layouts.admin')

@section('title', 'إدارة الفئات')

@section('content')

    <h2>إدارة الفئات</h2>

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

    <h3>إضافة فئة جديدة</h3>

    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf

        <div>
            <label for="section_id">القسم</label>

            <select name="section_id" id="section_id" required>
                <option value="">اختر القسم</option>

                @foreach ($sections as $section)
                    <option
                        value="{{ $section->id }}"
                        @selected(old('section_id') == $section->id)
                    >
                        {{ $section->name_ar }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="name_ar">اسم الفئة</label>

            <input
                type="text"
                id="name_ar"
                name="name_ar"
                value="{{ old('name_ar') }}"
                required
            >
        </div>

        <div>
            <label for="slug">Slug</label>

            <input
                type="text"
                id="slug"
                name="slug"
                value="{{ old('slug') }}"
                required
            >
        </div>

        <div>
            <label for="sort_order">الترتيب</label>

            <input
                type="number"
                id="sort_order"
                name="sort_order"
                value="{{ old('sort_order', 0) }}"
                min="0"
            >
        </div>

        <div>
            <label>
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    checked
                >

                فعّالة
            </label>
        </div>

        <button type="submit">
            إضافة الفئة
        </button>
    </form>

    <hr>

    <h3>الفئات الحالية</h3>

    @forelse ($categories as $category)

        <div>

            <form
                method="POST"
                action="{{ route('admin.categories.update', $category) }}"
            >
                @csrf
                @method('PUT')

                <div>
                    <label>القسم</label>

                    <select name="section_id" required>
                        @foreach ($sections as $section)
                            <option
                                value="{{ $section->id }}"
                                @selected($category->section_id === $section->id)
                            >
                                {{ $section->name_ar }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>اسم الفئة</label>

                    <input
                        type="text"
                        name="name_ar"
                        value="{{ $category->name_ar }}"
                        required
                    >
                </div>

                <div>
                    <label>Slug</label>

                    <input
                        type="text"
                        name="slug"
                        value="{{ $category->slug }}"
                        required
                    >
                </div>

                <div>
                    <label>الترتيب</label>

                    <input
                        type="number"
                        name="sort_order"
                        value="{{ $category->sort_order }}"
                        min="0"
                    >
                </div>

                <div>
                    <label>
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked($category->is_active)
                        >

                        فعّالة
                    </label>
                </div>

                <button type="submit">
                    حفظ التعديلات
                </button>
            </form>

            <form
                method="POST"
                action="{{ route('admin.categories.destroy', $category) }}"
                onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة؟');"
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

        <p>لا توجد فئات.</p>

    @endforelse

@endsection