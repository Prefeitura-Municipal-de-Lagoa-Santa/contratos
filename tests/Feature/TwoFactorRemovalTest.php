<?php

use Illuminate\Support\Facades\Schema;

it('does not expose two-factor routes or user columns', function () {
    expect(Schema::hasColumn('users', 'two_factor_secret'))->toBeFalse()
        ->and(Schema::hasColumn('users', 'two_factor_recovery_codes'))->toBeFalse()
        ->and(Schema::hasColumn('users', 'two_factor_confirmed_at'))->toBeFalse();

    $this->get('/settings/two-factor')->assertNotFound();
    $this->get('/two-factor-challenge')->assertNotFound();
    $this->post('/two-factor-challenge')->assertNotFound();
    $this->post('/user/two-factor-authentication')->assertNotFound();
    $this->delete('/user/two-factor-authentication')->assertNotFound();
    $this->get('/user/two-factor-qr-code')->assertNotFound();
    $this->get('/user/two-factor-recovery-codes')->assertNotFound();
    $this->post('/user/two-factor-recovery-codes')->assertNotFound();
});
