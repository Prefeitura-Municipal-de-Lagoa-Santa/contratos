<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@contratos.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMINISTRATOR,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Usuário administrador criado/atualizado com sucesso!');
        $this->command->info('Email: admin@contratos.com');
        $this->command->info('Senha: password');
    }
}
