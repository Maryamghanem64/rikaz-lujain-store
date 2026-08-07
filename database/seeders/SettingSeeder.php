<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'store_name_ar' => 'ركاز × لجين',
                'currency' => 'USD',
                'reservation_hours' => 24,
            ]
        );
    }
}
