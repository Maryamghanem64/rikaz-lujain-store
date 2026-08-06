<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('store.admin.email');
        $password = config('store.admin.password');

        if (blank($email) || blank($password)) {
            throw new RuntimeException(
                'ADMIN_EMAIL and ADMIN_PASSWORD must be defined in the .env file.'
            );
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('store.admin.name', 'Store Admin'),
                'password' => $password,
                'role' => 'admin',
            ]
        );
    }
}