<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $rikaz = Section::updateOrCreate(
            ['slug' => 'rikaz'],
            [
                'name_ar' => 'ركاز',
                'audience' => 'men',
                'is_active' => true,
            ]
        );

        $lujain = Section::updateOrCreate(
            ['slug' => 'lujain'],
            [
                'name_ar' => 'لجين',
                'audience' => 'women',
                'is_active' => true,
            ]
        );

        Category::updateOrCreate(
            [
                'section_id' => $rikaz->id,
                'slug' => 'rings',
            ],
            [
                'name_ar' => 'الخواتم الرجالية',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $lujainCategories = [
            [
                'name_ar' => 'الخواتم',
                'slug' => 'rings',
                'sort_order' => 1,
            ],
            [
                'name_ar' => 'السلاسل',
                'slug' => 'chains',
                'sort_order' => 2,
            ],
            [
                'name_ar' => 'الأساور',
                'slug' => 'bracelets',
                'sort_order' => 3,
            ],
            [
                'name_ar' => 'الأطقم',
                'slug' => 'sets',
                'sort_order' => 4,
            ],
        ];

        foreach ($lujainCategories as $category) {
            Category::updateOrCreate(
                [
                    'section_id' => $lujain->id,
                    'slug' => $category['slug'],
                ],
                [
                    'name_ar' => $category['name_ar'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}