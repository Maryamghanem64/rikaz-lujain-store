<div>
    <label for="category_id">الفئة</label>

    <select name="category_id" id="category_id" required>
        <option value="">اختر الفئة</option>

        @foreach ($categories as $category)
            <option
                value="{{ $category->id }}"
                @selected(
                    old('category_id', $product->category_id ?? '') == $category->id
                )
            >
                {{ $category->section->name_ar }}
                —
                {{ $category->name_ar }}
            </option>
        @endforeach
    </select>
</div>

<div>
    <label for="name_ar">اسم المنتج</label>

    <input
        type="text"
        name="name_ar"
        id="name_ar"
        value="{{ old('name_ar', $product->name_ar ?? '') }}"
        required
    >
</div>

<div>
    <label for="slug">Slug</label>

    <input
        type="text"
        name="slug"
        id="slug"
        value="{{ old('slug', $product->slug ?? '') }}"
        required
    >
</div>

<div>
    <label for="description_ar">الوصف</label>

    <textarea
        name="description_ar"
        id="description_ar"
        rows="4"
    >{{ old('description_ar', $product->description_ar ?? '') }}</textarea>
</div>

<div>
    <label for="stone_name">اسم الحجر</label>

    <input
        type="text"
        name="stone_name"
        id="stone_name"
        value="{{ old('stone_name', $product->stone_name ?? '') }}"
    >
</div>

<div>
    <label for="stone_weight">وزن الحجر</label>

    <input
        type="number"
        step="0.01"
        min="0"
        name="stone_weight"
        id="stone_weight"
        value="{{ old('stone_weight', $product->stone_weight ?? '') }}"
    >
</div>

<div>
    <label for="silver_purity">عيار الفضة</label>

    <input
        type="text"
        name="silver_purity"
        id="silver_purity"
        value="{{ old('silver_purity', $product->silver_purity ?? '') }}"
        placeholder="مثال: 925"
        required
    >
</div>

<div>
    <label for="size">المقاس</label>

    <input
        type="text"
        name="size"
        id="size"
        value="{{ old('size', $product->size ?? '') }}"
    >

    <small>
        المقاس إلزامي للخواتم فقط.
    </small>
</div>

<div>
    <label for="price">السعر ($)</label>

    <input
        type="number"
        step="0.01"
        min="0"
        name="price"
        id="price"
        value="{{ old('price', $product->price ?? '') }}"
        required
    >
</div>

<div>
    <label for="stock_quantity">الكمية</label>

    <input
        type="number"
        min="0"
        name="stock_quantity"
        id="stock_quantity"
        value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}"
        required
    >
</div>

@if (isset($product))
    <div>
        <strong>الكمية المحجوزة:</strong>
        {{ $product->reserved_quantity }}

        <br>

        <strong>الكمية المتاحة:</strong>
        {{ $product->available_quantity }}
    </div>
@endif

<div>
    <label>
        <input
            type="checkbox"
            name="is_active"
            value="1"
            @checked(old('is_active', $product->is_active ?? true))
        >

        المنتج فعّال
    </label>
</div>

<div>
    <label>
        <input
            type="checkbox"
            name="is_featured"
            value="1"
            @checked(old('is_featured', $product->is_featured ?? false))
        >

        منتج مميز
    </label>
</div>