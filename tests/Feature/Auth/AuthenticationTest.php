<?php

use App\Models\User;
use App\Exceptions\LdapUnavailableException;
use App\Services\LdapAuthenticator;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('unverified users are redirected to the dashboard after login', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Dashboard'));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('login throttle error is attached to the username field shown by the login form', function () {
    $identifier = 'rate-limited-user';

    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post('/login', [
            'username' => $identifier,
            'password' => 'wrong-password',
        ]);
    }

    $this->post('/login', [
        'username' => $identifier,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('username');

    expect(session('errors')->getBag('default')->has('email'))->toBeFalse();
});

test('users can authenticate using their LDAP username', function () {
    config(['ldap.enabled' => true]);
    $ldap = Mockery::mock(LdapAuthenticator::class);
    $ldap->shouldReceive('authenticate')
        ->once()
        ->with('leonardoleite', 'network-password')
        ->andReturn([
            'name' => 'Leonardo Leite',
            'email' => 'leonardo@example.test',
            'username' => 'leonardoleite',
        ]);
    $this->instance(LdapAuthenticator::class, $ldap);

    $response = $this->post('/login', [
        'username' => 'leonardoleite',
        'password' => 'network-password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertDatabaseHas('users', [
        'username' => 'leonardoleite',
        'email' => 'leonardo@example.test',
        'role' => User::ROLE_USER,
    ]);
});

test('LDAP transport requires encryption unless insecure mode is explicitly enabled', function () {
    config([
        'ldap.ssl' => false,
        'ldap.tls' => false,
        'ldap.allow_insecure' => false,
    ]);

    expect(fn () => app(LdapAuthenticator::class)->authenticate('user', 'password'))
        ->toThrow(LdapUnavailableException::class, 'requires SSL or StartTLS');
});

test('LDAP can use plaintext transport when explicitly allowed', function () {
    config([
        'ldap.ssl' => false,
        'ldap.tls' => false,
        'ldap.allow_insecure' => true,
        'ldap.host' => null,
        'ldap.base_dn' => null,
    ]);

    expect(fn () => app(LdapAuthenticator::class)->authenticate('user', 'password'))
        ->toThrow(LdapUnavailableException::class, 'LDAP host and base DN must be configured.');
});

test('LDAP login does not fall back to a local password when directory authentication fails', function () {
    config(['ldap.enabled' => true]);
    $user = User::factory()->create([
        'email' => 'leonardo@example.test',
        'password' => 'local-password',
    ]);
    $ldap = Mockery::mock(LdapAuthenticator::class);
    $ldap->shouldReceive('authenticate')->once()->andReturnNull();
    $this->instance(LdapAuthenticator::class, $ldap);

    $this->post('/login', [
        'username' => 'leonardo',
        'password' => 'local-password',
    ])->assertSessionHasErrors('username');

    $this->assertGuest();
});

test('LDAP login links a matching local account without changing its assigned profile', function () {
    config(['ldap.enabled' => true]);
    $manager = User::factory()->create([
        'email' => 'gestor@example.test',
        'role' => User::ROLE_MANAGER,
    ]);
    $ldap = Mockery::mock(LdapAuthenticator::class);
    $ldap->shouldReceive('authenticate')
        ->once()
        ->andReturn([
            'name' => 'Gestor da Prefeitura',
            'email' => 'gestor@example.test',
            'username' => 'gestor.rede',
        ]);
    $this->instance(LdapAuthenticator::class, $ldap);

    $this->post('/login', [
        'username' => 'gestor.rede',
        'password' => 'network-password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($manager->fresh());
    $this->assertDatabaseHas('users', [
        'id' => $manager->id,
        'username' => 'gestor.rede',
        'role' => User::ROLE_MANAGER,
    ]);
});

test('local administrator can sign in by email when LDAP is enabled', function () {
    config(['ldap.enabled' => true]);
    $admin = User::factory()->create([
        'email' => 'admin@contratos.com',
        'password' => 'local-admin-password',
        'role' => User::ROLE_ADMINISTRATOR,
    ]);

    $this->post('/login', [
        'username' => 'admin@contratos.com',
        'password' => 'local-admin-password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($admin);
});

test('regular local users cannot bypass LDAP authentication with a local password', function () {
    config(['ldap.enabled' => true]);
    $user = User::factory()->create([
        'email' => 'user@example.test',
        'password' => 'local-user-password',
    ]);
    $ldap = Mockery::mock(LdapAuthenticator::class);
    $ldap->shouldReceive('authenticate')->once()->andReturnNull();
    $this->instance(LdapAuthenticator::class, $ldap);

    $this->post('/login', [
        'username' => $user->email,
        'password' => 'local-user-password',
    ])->assertSessionHasErrors('username');

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
