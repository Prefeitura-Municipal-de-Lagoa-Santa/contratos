<?php

use App\Models\Patrimonio;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

it('allows regular users to view the asset list but not manage assets', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);
    $patrimonio = Patrimonio::create([
        'numero_patrimonio' => 'PAT-001',
        'nome' => 'Computador',
    ]);

    $this->actingAs($user)
        ->get(route('patrimonios.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Patrimonio/Index')
            ->where('canManage', false));

    $this->get(route('patrimonios.create'))->assertForbidden();
    $this->get(route('patrimonios.edit', $patrimonio))->assertForbidden();
    $this->post(route('patrimonios.store'), [
        'numero_patrimonio' => 'PAT-002',
        'nome' => 'Impressora',
    ])->assertForbidden();
    $this->patch(route('patrimonios.update', $patrimonio), [
        'numero_patrimonio' => 'PAT-003',
        'nome' => 'Notebook',
    ])->assertForbidden();
    $this->delete(route('patrimonios.destroy', $patrimonio))->assertForbidden();

    $this->assertDatabaseHas('patrimonios', [
        'id' => $patrimonio->id,
        'numero_patrimonio' => 'PAT-001',
        'nome' => 'Computador',
    ]);
    $this->assertDatabaseMissing('patrimonios', [
        'numero_patrimonio' => 'PAT-002',
    ]);
});

it('does not register an asset detail route without a show action', function () {
    $user = User::factory()->create(['role' => User::ROLE_MANAGER]);

    $this->actingAs($user)
        ->get('/patrimonios/1')
        ->assertMethodNotAllowed();

    expect(Route::has('patrimonios.show'))->toBeFalse();
});

it('returns only user summaries with assets', function () {
    $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
    $employee = User::factory()->create();
    Patrimonio::create([
        'numero_patrimonio' => 'PAT-PRIVACY',
        'nome' => 'Computador',
        'user_id' => $employee->id,
    ]);

    $this->actingAs($manager)
        ->get(route('patrimonios.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Patrimonio/Index')
            ->where('patrimonios.data.0.user.id', $employee->id)
            ->where('patrimonios.data.0.user.name', $employee->name)
            ->missing('patrimonios.data.0.user.email')
            ->missing('patrimonios.data.0.user.username')
            ->missing('patrimonios.data.0.user.role')
            ->missing('patrimonios.data.0.user.is_admin')
            ->missing('auth.user.email')
            ->missing('auth.user.username')
            ->missing('auth.user.role')
            ->missing('auth.user.is_admin'));
});

it('allows managers and administrators to manage assets', function (string $role) {
    $user = User::factory()->create(['role' => $role]);
    $patrimonio = Patrimonio::create([
        'numero_patrimonio' => 'PAT-101',
        'nome' => 'Computador',
    ]);

    $this->actingAs($user)
        ->get(route('patrimonios.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('canManage', true));

    $this->get(route('patrimonios.create'))->assertOk();
    $this->get(route('patrimonios.edit', $patrimonio))->assertOk();

    $this->post(route('patrimonios.store'), [
        'numero_patrimonio' => 'PAT-102',
        'nome' => 'Impressora',
    ])->assertRedirect(route('patrimonios.index'));

    $this->patch(route('patrimonios.update', $patrimonio), [
        'numero_patrimonio' => 'PAT-103',
        'nome' => 'Notebook',
    ])->assertRedirect(route('patrimonios.index'));

    $this->delete(route('patrimonios.destroy', $patrimonio))
        ->assertRedirect(route('patrimonios.index'));

    $this->assertDatabaseHas('patrimonios', [
        'numero_patrimonio' => 'PAT-102',
        'nome' => 'Impressora',
    ]);
    $this->assertDatabaseMissing('patrimonios', [
        'id' => $patrimonio->id,
    ]);
})->with([User::ROLE_MANAGER, User::ROLE_ADMINISTRATOR]);
