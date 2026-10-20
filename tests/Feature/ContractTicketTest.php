<?php

use App\Models\ContractTicket;
use App\Models\ContractTicketAttachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

it('allows an employee to create and view their own contract ticket', function () {
    $employee = User::factory()->create();

    $this->actingAs($employee)
        ->post(route('chamados.store'), [
            'subject' => 'Dúvida sobre contrato',
            'category' => 'contrato',
            'description' => 'Preciso de orientação sobre o contrato.',
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
        ->assertSessionHasErrors(['specifications', 'justification', 'ficha']);

    $this->post(route('chamados.store'), [
        ...$baseTicket,
        'specifications' => 'Especificações do pedido.',
        'justification' => 'Justificativa do pedido.',
        'ficha' => 'Ficha 123',
    ])->assertRedirect();

    $this->assertDatabaseHas('contract_tickets', [
        'user_id' => $employee->id,
        'category' => $category,
        'specifications' => 'Especificações do pedido.',
        'justification' => 'Justificativa do pedido.',
        'ficha' => 'Ficha 123',
    ]);
})->with(['novo_contrato', 'aditivo', 'compra_direta']);

it('does not require contract specifications and justification for other ticket categories', function () {
    $employee = User::factory()->create();

    $this->actingAs($employee)
        ->post(route('chamados.store'), [
            'subject' => 'Dúvida de documentação',
            'category' => 'documentacao',
            'description' => 'Preciso de orientação.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('contract_tickets', [
        'user_id' => $employee->id,
        'category' => 'documentacao',
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
            'category' => 'documentacao',
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

it('validates ticket attachment count, size, and file type', function () {
    $employee = User::factory()->create();

    $this->actingAs($employee)
        ->post(route('chamados.store'), [
            'subject' => 'Chamado com formato inválido',
            'category' => 'outro',
            'description' => 'Descrição.',
            'attachments' => [UploadedFile::fake()->create('programa.exe', 50, 'application/x-msdownload')],
        ])
        ->assertSessionHasErrors('attachments.0');
});
