<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['rikaz', 'lujain'] as $slug) {
            $section = Section::where('slug', $slug)->firstOrFail();
            $credentials = config("store.admins.{$slug}");

            if (blank($credentials['email']) || blank($credentials['password'])) {
                throw new RuntimeException(strtoupper($slug).' admin email and password must be defined in the environment.');
            }

            User::updateOrCreate(
                ['email' => $credentials['email']],
                [
                    'name' => $credentials['name'],
                    'password' => $credentials['password'],
                    'role' => 'admin',
                    'section_id' => $section->id,
                ]
            );
        }
    }
}
