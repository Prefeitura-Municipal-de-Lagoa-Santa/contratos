<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('allows administrators to list users and change their profiles', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $employee = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Users/Index')
            ->has('users.data', 2)
            ->where('roles.manager', 'Gestor'));

    $this->patch(route('users.role.update', $employee), [
        'role' => User::ROLE_MANAGER,
    ])->assertRedirect();

    $this->assertDatabaseHas('users', [
        'id' => $employee->id,
        'role' => User::ROLE_MANAGER,
        'is_admin' => false,
    ]);
});

it('prevents non-administrators from managing user profiles', function () {
    $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
    $employee = User::factory()->create();

    $this->actingAs($manager)
        ->get(route('users.index'))
        ->assertForbidden();

    $this->patch(route('users.role.update', $employee), [
        'role' => User::ROLE_ADMINISTRATOR,
    ])->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $employee->id,
        'role' => User::ROLE_USER,
    ]);
});

it('prevents the only administrator from removing their own administrator profile', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);

    $this->actingAs($admin)
        ->patch(route('users.role.update', $admin), [
            'role' => User::ROLE_MANAGER,
        ])
        ->assertSessionHasErrors('role');

    $this->assertDatabaseHas('users', [
        'id' => $admin->id,
        'role' => User::ROLE_ADMINISTRATOR,
        'is_admin' => true,
    ]);
});

it('allows managers to manage tickets and vacation approvals but not user profiles', function () {
    $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

    $this->actingAs($manager)
        ->get(route('ferias.approvals'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Ferias/Approvals'));

    $this->get(route('users.index'))->assertForbidden();
});
