<?php

use App\Models\ContractTicket;
use App\Models\ContractTicketAttachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

it('only offers the three current demand categories in the create form', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('chamados.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Chamados/Create')
            ->where('categories', ['novo_processo', 'compra_direta', 'renovacao']));
});

it('allows an employee to create and view their own contract ticket', function () {
    $employee = User::factory()->create();

    $this->actingAs($employee)
        ->post(route('chamados.store'), [
            'subject' => 'Dúvida sobre contrato',
            'category' => 'renovacao',
            'description' => 'Preciso de orientação sobre o contrato.',
            'attachments' => [UploadedFile::fake()->create('dfd.pdf', 100, 'application/pdf')],
        ])
        ->assertRedirect();

    $ticket = ContractTicket::firstOrFail();

    expect($ticket->user_id)->toBe($employee->id)
        ->and($ticket->status)->toBe('aberto');

    $this->actingAs($employee)
        ->get(route('chamados.show', $ticket))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Chamados/Show')
            ->where('ticket.id', $ticket->id)
            ->where('isAdmin', false));

    $this->get(route('chamados.pdf', $ticket))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->assertHeader('content-disposition', 'attachment; filename=demanda-'.$ticket->id.'.pdf');
});

it('requires and stores specifications, justification, and ficha for contract requests', function (string $category) {
    $employee = User::factory()->create();
    $baseTicket = [
        'subject' => 'Solicitação contratual',
        'category' => $category,
        'description' => 'Descrição da solicitação.',
    ];

    $this->actingAs($employee)
        ->post(route('chamados.store'), $baseTicket)
        ->assertSessionHasErrors([
            'specifications',
            'justification',
            'ficha',
            'in_pca',
            'involves_other_secretariats',
            'attachments',
        ]);

    $this->post(route('chamados.store'), [
        ...$baseTicket,
        'specifications' => 'Especificações do pedido.',
        'justification' => 'Justificativa do pedido.',
        'ficha' => 'Ficha 123',
        'in_pca' => true,
        'involves_other_secretariats' => false,
        'attachments' => [UploadedFile::fake()->create('dfd.pdf', 100, 'application/pdf')],
    ])->assertRedirect();

    $this->assertDatabaseHas('contract_tickets', [
        'user_id' => $employee->id,
        'category' => $category,
        'specifications' => 'Especificações do pedido.',
        'justification' => 'Justificativa do pedido.',
        'ficha' => 'Ficha 123',
        'in_pca' => true,
        'involves_other_secretariats' => false,
    ]);
})->with(['novo_processo', 'compra_direta']);

it('requires and stores additional fichas when a request involves other secretariats', function () {
    $employee = User::factory()->create();
    $requestData = [
        'subject' => 'Solicitação para várias secretarias',
        'category' => 'novo_processo',
        'description' => 'Descrição da solicitação.',
        'specifications' => 'Especificações do pedido.',
        'justification' => 'Justificativa do pedido.',
        'ficha' => 'Ficha principal',
        'in_pca' => true,
        'involves_other_secretariats' => true,
    ];

    $this->actingAs($employee)
        ->post(route('chamados.store'), [
            ...$requestData,
            'attachments' => [UploadedFile::fake()->create('dfd.pdf', 100, 'application/pdf')],
        ])
        ->assertSessionHasErrors('other_fichas');

    $this->post(route('chamados.store'), [
        ...$requestData,
        'other_fichas' => ['Ficha Secretaria A', 'Ficha Secretaria B'],
        'attachments' => [UploadedFile::fake()->create('dfd.pdf', 100, 'application/pdf')],
    ])->assertRedirect();

    $ticket = ContractTicket::latest('id')->firstOrFail();

    $this->assertDatabaseHas('contract_tickets', [
        'id' => $ticket->id,
        'in_pca' => true,
        'involves_other_secretariats' => true,
    ]);

    expect($ticket->other_fichas)->toBe(['Ficha Secretaria A', 'Ficha Secretaria B']);

    $this->get(route('chamados.show', $ticket))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('ticket.in_pca', true)
            ->where('ticket.involves_other_secretariats', true)
            ->where('ticket.other_fichas.0', 'Ficha Secretaria A')
            ->where('ticket.other_fichas.1', 'Ficha Secretaria B'));
});

it('does not allow submitting a contract demand that is not planned in the PCA', function () {
    $employee = User::factory()->create();

    $this->actingAs($employee)
        ->post(route('chamados.store'), [
            'subject' => 'Demanda fora do PCA',
            'category' => 'novo_processo',
            'description' => 'Descrição da demanda.',
            'specifications' => 'Especificações.',
            'justification' => 'Justificativa.',
            'ficha' => 'Ficha 123',
            'in_pca' => false,
            'involves_other_secretariats' => false,
            'attachments' => [UploadedFile::fake()->create('dfd.pdf', 100, 'application/pdf')],
        ])
        ->assertSessionHasErrors([
            'in_pca' => 'Não é possível prosseguir com a demanda, pois ela não está prevista no PCA.',
        ]);

    $this->assertDatabaseMissing('contract_tickets', [
        'subject' => 'Demanda fora do PCA',
    ]);
});

it('does not require contract specifications and justification for renewal demands', function () {
    $employee = User::factory()->create();

    $this->actingAs($employee)
        ->post(route('chamados.store'), [
            'subject' => 'Renovação de contrato',
            'category' => 'renovacao',
            'description' => 'Preciso de orientação.',
            'attachments' => [UploadedFile::fake()->create('dfd.pdf', 100, 'application/pdf')],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('contract_tickets', [
        'user_id' => $employee->id,
        'category' => 'renovacao',
        'specifications' => null,
        'justification' => null,
        'ficha' => null,
    ]);
});

it('allows opening a renewal ticket without contract specifications and justification', function () {
    $employee = User::factory()->create();

    $this->actingAs($employee)
        ->post(route('chamados.store'), [
            'subject' => 'Renovação de contrato',
            'category' => 'renovacao',
            'description' => 'Solicito avaliar a renovação.',
            'attachments' => [UploadedFile::fake()->create('dfd.pdf', 100, 'application/pdf')],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('contract_tickets', [
        'user_id' => $employee->id,
        'category' => 'renovacao',
        'specifications' => null,
        'justification' => null,
        'ficha' => null,
    ]);
});

it('does not allow employees to view another employee ticket', function () {
    $owner = User::factory()->create();
    $otherEmployee = User::factory()->create();
    $ticket = ContractTicket::create([
        'user_id' => $owner->id,
        'subject' => 'Solicitação privada',
        'category' => 'outro',
        'description' => 'Descrição.',
    ]);

    $this->actingAs($otherEmployee)
        ->get(route('chamados.show', $ticket))
        ->assertForbidden();

    $this->get(route('chamados.pdf', $ticket))
        ->assertForbidden();
});

it('allows managers and administrators to reply, assign, and update ticket status', function () {
    $employee = User::factory()->create();
    $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
    $ticket = ContractTicket::create([
        'user_id' => $employee->id,
        'subject' => 'Solicitação de atendimento',
        'category' => 'documentacao',
        'description' => 'Descrição.',
    ]);

    $this->actingAs($employee)
        ->patch(route('chamados.update', $ticket), [
            'status' => 'resolvido',
            'management_response' => 'Orientação.',
        ])
        ->assertForbidden();

    $this->actingAs($manager)
        ->patch(route('chamados.update', $ticket), [
            'status' => 'em_atendimento',
            'management_response' => 'Estamos verificando sua solicitação.',
            'responsible_user_id' => $manager->id,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('contract_tickets', [
        'id' => $ticket->id,
        'status' => 'em_atendimento',
        'management_response' => 'Estamos verificando sua solicitação.',
        'responsible_user_id' => $manager->id,
    ]);
});

it('allows managers and administrators to approve or refuse a ticket once', function () {
    $employee = User::factory()->create();
    $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $approvalTicket = ContractTicket::create([
        'user_id' => $employee->id,
        'subject' => 'Solicitação para aprovar',
        'category' => 'documentacao',
        'description' => 'Descrição.',
    ]);
    $refusalTicket = ContractTicket::create([
        'user_id' => $employee->id,
        'subject' => 'Solicitação para recusar',
        'category' => 'documentacao',
        'description' => 'Descrição.',
    ]);

    $this->actingAs($employee)
        ->patch(route('chamados.decide', $approvalTicket), ['status' => 'aprovado'])
        ->assertForbidden();

    $this->actingAs($manager)
        ->patch(route('chamados.decide', $approvalTicket), ['status' => 'aprovado'])
        ->assertRedirect();

    $this->actingAs($admin)
        ->patch(route('chamados.decide', $refusalTicket), ['status' => 'recusado'])
        ->assertRedirect();

    $this->patch(route('chamados.decide', $approvalTicket), ['status' => 'recusado'])
        ->assertStatus(409);

    $this->assertDatabaseHas('contract_tickets', [
        'id' => $approvalTicket->id,
        'status' => 'aprovado',
    ]);
    $this->assertDatabaseHas('contract_tickets', [
        'id' => $refusalTicket->id,
        'status' => 'recusado',
    ]);
});

it('allows managers to view all tickets and download their attachments', function () {
    Storage::fake('local');
    $employee = User::factory()->create();
    $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
    $ticket = ContractTicket::create([
        'user_id' => $employee->id,
        'subject' => 'Chamado visível para o gestor',
        'category' => 'documentacao',
        'description' => 'Descrição.',
    ]);
    $attachment = $ticket->attachments()->create([
        'original_name' => 'documento.pdf',
        'file_path' => 'contract-ticket-attachments/'.$ticket->id.'/documento.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 10,
    ]);
    Storage::disk('local')->put($attachment->file_path, 'documento');

    $this->actingAs($manager)
        ->get(route('chamados.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('canManage', true)
            ->where('isAdmin', false)
            ->has('tickets.data', 1));

    $this->get(route('chamados.show', $ticket))->assertOk();
    $this->get(route('chamados.attachments.download', $attachment))->assertOk();
});

it('only allows administrators to delete tickets', function () {
    $employee = User::factory()->create();
    $ticket = ContractTicket::create([
        'user_id' => $employee->id,
        'subject' => 'Chamado para excluir',
        'category' => 'outro',
        'description' => 'Descrição.',
    ]);

    $this->actingAs($employee)
        ->delete(route('chamados.destroy', $ticket))
        ->assertForbidden();

    $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
    $this->actingAs($manager)
        ->delete(route('chamados.destroy', $ticket))
        ->assertForbidden();

    $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
    $this->actingAs($admin)
        ->delete(route('chamados.destroy', $ticket))
        ->assertRedirect(route('chamados.index'));

    $this->assertDatabaseMissing('contract_tickets', ['id' => $ticket->id]);
});

it('does not allow assigning a regular user as responsible', function () {
    $employee = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    $ticket = ContractTicket::create([
        'user_id' => $employee->id,
        'subject' => 'Chamado com responsável inválido',
        'category' => 'outro',
        'description' => 'Descrição.',
    ]);

    $this->actingAs($admin)
        ->patch(route('chamados.update', $ticket), [
            'status' => 'aberto',
            'management_response' => null,
            'responsible_user_id' => $employee->id,
        ])
        ->assertSessionHasErrors('responsible_user_id');
});

it('allows employees to attach files to their tickets and download them', function () {
    Storage::fake('local');
    $employee = User::factory()->create();
    $file = UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf');

    $this->actingAs($employee)
        ->post(route('chamados.store'), [
            'subject' => 'Chamado com anexo',
            'category' => 'renovacao',
            'description' => 'Segue o documento para análise.',
            'attachments' => [$file],
        ])
        ->assertRedirect();

    $attachment = ContractTicketAttachment::firstOrFail();
    Storage::disk('local')->assertExists($attachment->file_path);

    $this->actingAs($employee)
        ->get(route('chamados.attachments.download', $attachment))
        ->assertOk()
        ->assertHeader('content-disposition', 'attachment; filename=documento.pdf');
});

it('prevents users from downloading attachments belonging to another user', function () {
    Storage::fake('local');
    $owner = User::factory()->create();
    $otherEmployee = User::factory()->create();
    $ticket = ContractTicket::create([
        'user_id' => $owner->id,
        'subject' => 'Chamado privado',
        'category' => 'outro',
        'description' => 'Descrição.',
    ]);
    Storage::disk('local')->put('contract-ticket-attachments/1/teste.pdf', 'conteúdo');
    $attachment = $ticket->attachments()->create([
        'original_name' => 'teste.pdf',
        'file_path' => 'contract-ticket-attachments/1/teste.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 9,
    ]);

    $this->actingAs($otherEmployee)
        ->get(route('chamados.attachments.download', $attachment))
        ->assertForbidden();
});

it('requires at least one PDF attachment and rejects other file types', function () {
    $employee = User::factory()->create();

    $this->actingAs($employee)
        ->post(route('chamados.store'), [
            'subject' => 'Chamado sem anexo',
            'category' => 'outro',
            'description' => 'Descrição.',
        ])
        ->assertSessionHasErrors('attachments');

    $this->actingAs($employee)
        ->post(route('chamados.store'), [
            'subject' => 'Chamado com formato inválido',
            'category' => 'outro',
            'description' => 'Descrição.',
            'attachments' => [UploadedFile::fake()->create('programa.exe', 50, 'application/x-msdownload')],
        ])
        ->assertSessionHasErrors('attachments.0');
});
