<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;

it('removes routes and tables for oficios and internal communications', function () {
    $user = User::factory()->create();

    expect(Schema::hasTable('oficios'))->toBeFalse()
        ->and(Schema::hasTable('internal_communications'))->toBeFalse();

    $this->actingAs($user)
        ->get('/oficios')
        ->assertNotFound();

    $this->get('/oficios/novo')->assertNotFound();
    $this->get('/oficios/1')->assertNotFound();
    $this->get('/oficios/1/pdf')->assertNotFound();
    $this->post('/oficios')->assertNotFound();

    $this->get('/comunicacoes-internas')->assertNotFound();
    $this->get('/comunicacoes-internas/nova')->assertNotFound();
    $this->get('/comunicacoes-internas/1')->assertNotFound();
    $this->get('/comunicacoes-internas/1/pdf')->assertNotFound();
    $this->post('/comunicacoes-internas')->assertNotFound();
});
