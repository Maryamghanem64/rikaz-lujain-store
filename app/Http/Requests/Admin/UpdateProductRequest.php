<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'name_ar' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug')
                    ->ignore($product?->id),
            ],

            'description_ar' => [
                'nullable',
                'string',
            ],

            'stone_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'stone_weight' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'silver_purity' => [
                'required',
                'string',
                'max:50',
            ],

            'size' => [
                'nullable',
                'string',
                'max:100',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'stock_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'is_featured' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $category = Category::find($this->category_id);

                if (
                    $category &&
                    $category->slug === 'rings' &&
                    blank($this->size)
                ) {
                    $validator->errors()->add(
                        'size',
                        'المقاس مطلوب للخواتم.'
                    );
                }

                $product = $this->route('product');

                if (
                    $product &&
                    (int) $this->stock_quantity <
                    (int) $product->reserved_quantity
                ) {
                    $validator->errors()->add(
                        'stock_quantity',
                        'لا يمكن جعل الكمية أقل من الكمية المحجوزة.'
                    );
                }
            },
        ];
    }
}
