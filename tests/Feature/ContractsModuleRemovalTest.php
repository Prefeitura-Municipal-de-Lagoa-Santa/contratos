<?php

use Illuminate\Support\Facades\Schema;

it('removes the contracts module routes and tables', function () {
    expect(Schema::hasTable('contracts'))->toBeFalse()
        ->and(Schema::hasTable('contract_files'))->toBeFalse()
        ->and(Schema::hasTable('suppliers'))->toBeFalse()
        ->and(Schema::hasTable('contract_types'))->toBeFalse();

    $this->get('/contratos')->assertNotFound();
    $this->get('/contratos/novo')->assertNotFound();
    $this->get('/contratos/1')->assertNotFound();
    $this->get('/contratos/1/editar')->assertNotFound();
    $this->post('/contratos')->assertNotFound();
    $this->put('/contratos/1', [])->assertNotFound();
    $this->patch('/contratos/1', [])->assertNotFound();
    $this->delete('/contratos/1')->assertNotFound();
    $this->post('/contratos/1/arquivos', [])->assertNotFound();
    $this->get('/contratos/arquivos/1/download')->assertNotFound();
    $this->get('/contratos/arquivos/1/preview')->assertNotFound();
    $this->delete('/contratos/arquivos/1')->assertNotFound();
});
