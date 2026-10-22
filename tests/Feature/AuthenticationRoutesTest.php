<?php

test('authentication and account settings use one route set', function () {
    expect(route('login'))->toBe(url('/login'))
        ->and(route('password.confirm'))->toBe(url('/confirm-password'))
        ->and(route('settings.profile.edit'))->toBe(url('/settings/profile'))
        ->and(route('settings.password.edit'))->toBe(url('/settings/password'));

    $this->get('/register')->assertNotFound();
    $this->get('/user/confirm-password')->assertNotFound();
    $this->get('/profile')->assertNotFound();
    $this->put('/password')->assertNotFound();
});
