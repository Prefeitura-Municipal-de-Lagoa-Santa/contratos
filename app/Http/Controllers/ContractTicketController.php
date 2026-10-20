<?php

namespace App\Http\Controllers;

use App\Models\ContractTicket;
use App\Models\ContractTicketAttachment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractTicketController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $tickets = ContractTicket::query()
            ->with(['user:id,name', 'responsible:id,name'])
            ->when(! $user->canManageTickets(), fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Chamados/Index', [
            'tickets' => $tickets,
            'canManage' => $user->canManageTickets(),
            'isAdmin' => $user->isAdministrator(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Chamados/Create', [
            'categories' => ContractTicket::CATEGORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'in:'.implode(',', ContractTicket::CATEGORIES)],
            'description' => ['required', 'string', 'max:10000'],
            'specifications' => [
                Rule::requiredIf(in_array($request->input('category'), ['novo_contrato', 'aditivo', 'compra_direta'], true)),
                'nullable',
                'string',
                'max:10000',
            ],
            'justification' => [
                Rule::requiredIf(in_array($request->input('category'), ['novo_contrato', 'aditivo', 'compra_direta'], true)),
                'nullable',
                'string',
                'max:10000',
            ],
            'ficha' => [
                Rule::requiredIf(in_array($request->input('category'), ['novo_contrato', 'aditivo', 'compra_direta'], true)),
                'nullable',
                'string',
                'max:100',
            ],
            'attachments' => ['sometimes', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg'],
        ]);

        $ticket = ContractTicket::create([
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'specifications' => in_array($validated['category'], ['novo_contrato', 'aditivo', 'compra_direta'], true)
                ? $validated['specifications']
                : null,
            'justification' => in_array($validated['category'], ['novo_contrato', 'aditivo', 'compra_direta'], true)
                ? $validated['justification']
                : null,
            'ficha' => in_array($validated['category'], ['novo_contrato', 'aditivo', 'compra_direta'], true)
                ? $validated['ficha']
                : null,
            'user_id' => $request->user()->id,
        ]);

        foreach ($request->file('attachments', []) as $file) {
            $path = $file->store('contract-ticket-attachments/'.$ticket->id, 'local');

            if ($path === false) {
                throw new \RuntimeException('Não foi possível salvar um anexo do chamado.');
            }

            $ticket->attachments()->create([
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'file_size' => $file->getSize(),
            ]);
        }

        return redirect()
            ->route('chamados.show', $ticket)
            ->with('success', 'Chamado enviado para a Gestão de Contratos.');
    }

    public function show(Request $request, ContractTicket $ticket): Response
    {
        abort_unless($request->user()->canManageTickets() || $ticket->user_id === $request->user()->id, 403);

        return Inertia::render('Chamados/Show', [
            'ticket' => $ticket->load(['user:id,name', 'responsible:id,name', 'attachments']),
            'canManage' => $request->user()->canManageTickets(),
            'isAdmin' => $request->user()->isAdministrator(),
            'statuses' => ContractTicket::STATUSES,
            'responsibles' => User::query()
                ->whereIn('role', [User::ROLE_ADMINISTRATOR, User::ROLE_MANAGER])
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function downloadAttachment(Request $request, ContractTicketAttachment $attachment): StreamedResponse
    {
        abort_unless(
            $request->user()->canManageTickets() || $attachment->ticket()->where('user_id', $request->user()->id)->exists(),
            403
        );
        abort_unless(Storage::disk('local')->exists($attachment->file_path), 404, 'Anexo não encontrado.');

        return Storage::disk('local')->download($attachment->file_path, $attachment->original_name);
    }

    public function update(Request $request, ContractTicket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', ContractTicket::STATUSES)],
            'management_response' => ['nullable', 'string', 'max:10000'],
            'responsible_user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->whereIn('role', [User::ROLE_ADMINISTRATOR, User::ROLE_MANAGER]),
            ],
        ]);

        $ticket->update($validated);

        return back()->with('success', 'Chamado atualizado com sucesso.');
    }

    public function destroy(ContractTicket $ticket): RedirectResponse
    {
        foreach ($ticket->attachments as $attachment) {
            if (! Storage::disk('local')->delete($attachment->file_path)) {
                throw new \RuntimeException('Não foi possível remover os anexos do chamado.');
            }
        }

        $ticket->delete();

        return redirect()
            ->route('chamados.index')
            ->with('success', 'Chamado excluído com sucesso.');
    }
}
