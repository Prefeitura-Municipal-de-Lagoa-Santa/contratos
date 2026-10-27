<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Support\Facades\Hash;

it('does not seed a developer account with a known password', function () {
    $this->seed();

    $this->assertDatabaseMissing('users', [
        'email' => 'leocfl@gmail.com',
    ]);
});

it('requires a 32-character external password before creating the initial administrator', function (?string $password) {
    config(['admin.initial_password' => $password]);

    expect(fn () => $this->seed(AdminUserSeeder::class))
        ->toThrow(RuntimeException::class, 'ADMIN_INITIAL_PASSWORD must be configured with at least 32 characters');

    $this->assertDatabaseMissing('users', [
        'email' => 'admin@contratos.com',
    ]);
})->with([
    'missing' => [null],
    'too short' => ['a'.str_repeat('b', 30)],
]);

it('creates the initial administrator with the configured password and does not reset it on reruns', function () {
    $initialPassword = str_repeat('initial-secret-', 3);
    config(['admin.initial_password' => $initialPassword]);

    $this->seed(AdminUserSeeder::class);

    $administrator = User::query()->where('email', 'admin@contratos.com')->firstOrFail();

    expect(Hash::check($initialPassword, $administrator->password))->toBeTrue();

    $originalPasswordHash = $administrator->password;
    config(['admin.initial_password' => str_repeat('replacement-secret-', 2)]);

    $this->seed(AdminUserSeeder::class);

    expect($administrator->fresh()->password)->toBe($originalPasswordHash);
});

it('does not require the initial password or change an existing administrator', function () {
    $administrator = User::factory()->create([
        'name' => 'Existing Administrator',
        'email' => 'admin@contratos.com',
        'role' => User::ROLE_USER,
    ]);
    $originalPasswordHash = $administrator->password;
    config(['admin.initial_password' => null]);

    $this->seed(AdminUserSeeder::class);

    expect($administrator->fresh()->name)->toBe('Existing Administrator')
        ->and($administrator->fresh()->role)->toBe(User::ROLE_USER)
        ->and($administrator->fresh()->password)->toBe($originalPasswordHash);
});
