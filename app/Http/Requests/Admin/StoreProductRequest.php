<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin' && $this->user()->section_id !== null;
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('section_id', $this->user()->section_id),
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
                Rule::unique('products', 'slug'),
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
            },
        ];
    }
}
