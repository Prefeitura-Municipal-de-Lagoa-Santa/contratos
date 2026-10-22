<?php

test('public registration is disabled when LDAP is not enabled', function () {
    config(['ldap.enabled' => false]);

    $this->get('/register')->assertNotFound();

    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});

test('public registration remains disabled when LDAP is enabled', function () {
    config(['ldap.enabled' => true]);

    $this->get('/register')->assertNotFound();

    $this->post('/register', [
        'name' => 'LDAP Bypass User',
        'email' => 'ldap-bypass@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'ldap-bypass@example.com']);
});
