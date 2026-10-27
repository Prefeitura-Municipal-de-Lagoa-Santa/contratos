<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'admin@contratos.com';

        if (User::query()->where('email', $email)->exists()) {
            return;
        }

        $password = config('admin.initial_password');

        if (! is_string($password) || Str::length($password) < 32) {
            throw new RuntimeException(
                'ADMIN_INITIAL_PASSWORD must be configured with at least 32 characters before creating the initial administrator.'
            );
        }

        User::query()->create([
            'name' => 'Administrador',
            'email' => $email,
            'password' => $password,
            'role' => User::ROLE_ADMINISTRATOR,
            'email_verified_at' => now(),
        ]);
    }
}
