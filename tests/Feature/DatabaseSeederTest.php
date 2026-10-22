<?php

it('does not seed a developer account with a known password', function () {
    $this->seed();

    $this->assertDatabaseMissing('users', [
        'email' => 'leocfl@gmail.com',
    ]);
});
