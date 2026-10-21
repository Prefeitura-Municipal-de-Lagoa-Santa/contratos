<?php

namespace App\Http\Controllers;

use App\Models\VacationRequest;
use App\Models\VacationBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class VacationRequestController extends Controller
{
    /**
     * Display a listing of the user's vacation requests.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $year = $request->get('year', date('Y'));

        $vacationRequests = VacationRequest::with('approver')
            ->where('user_id', $user->id)
            ->byYear($year)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $balance = VacationBalance::getOrCreateForUser($user->id, $year);

        return Inertia::render('Ferias/Index', [
            'vacationRequests' => $vacationRequests,
            'balance' => $balance,
            'currentYear' => $year,
            'years' => $this->getAvailableYears(),
        ]);
    }

    /**
     * Show the form for creating a new vacation request.
     */
    public function create()
    {
        $user = auth()->user();
        $year = date('Y');
        $balance = VacationBalance::getOrCreateForUser($user->id, $year);

        return Inertia::render('Ferias/Create', [
            'balance' => $balance,
            // TODO: Filtrar apenas usuários elegíveis a serem chefia, se necessário
            'users' => User::orderBy('name')->get(['id', 'name']),
            'allowedPeriods' => VacationRequest::ALLOWED_PERIODS,
            'minimumAdvanceDays' => VacationRequest::MINIMUM_ADVANCE_DAYS,
            'minDate' => Carbon::today()->addDays(VacationRequest::MINIMUM_ADVANCE_DAYS)->format('Y-m-d'),
        ]);
    }

    /**
     * Store a newly created vacation request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:' . Carbon::today()->addDays(VacationRequest::MINIMUM_ADVANCE_DAYS)->format('Y-m-d'),
            'period_days' => 'required|integer|in:15,30',
            'supervisor_id' => 'required|exists:users,id',
            'observations' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = $startDate->copy()->addDays($validated['period_days'] - 1);
        $year = $startDate->format('Y');

        // Verificar saldo
        $balance = VacationBalance::getOrCreateForUser($user->id, $year);
        if (!$balance->hasEnoughBalance($validated['period_days'])) {
            return back()->withErrors([
                'period_days' => 'Você não possui saldo suficiente de férias. Saldo disponível: ' . $balance->remaining_days . ' dias.',
            ]);
        }

        // Verificar limite de funcionários simultâneos
        $concurrentCount = VacationRequest::hasOverlap($startDate, $endDate);
        if ($concurrentCount >= VacationRequest::MAX_CONCURRENT_VACATIONS) {
            return back()->withErrors([
                'start_date' => 'Já existem ' . VacationRequest::MAX_CONCURRENT_VACATIONS . ' funcionários de férias neste período. Por favor, escolha outra data.',
            ]);
        }

        // Criar solicitação
        $vacationRequest = VacationRequest::create([
            'user_id' => $user->id,
            'supervisor_id' => $validated['supervisor_id'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'period_days' => $validated['period_days'],
            'observations' => $validated['observations'] ?? null,
            'year' => $year,
            'status' => VacationRequest::STATUS_PENDING,
        ]);

        return redirect()
            ->route('ferias.show', $vacationRequest->id)
            ->with('success', 'Solicitação de férias enviada com sucesso!');
    }

    /**
     * Display the specified vacation request.
     */
    public function show(int $id)
    {
        $vacationRequest = VacationRequest::with(['user', 'approver', 'supervisor'])
            ->findOrFail($id);

        // Verificar se o usuário pode ver esta solicitação
        $user = auth()->user();
        if ($vacationRequest->user_id !== $user->id) {
            abort(403);
        }

        return Inertia::render('Ferias/Show', [
            'vacationRequest' => $vacationRequest,
        ]);
    }

    /**
     * Cancel a pending vacation request.
     */
    public function cancel(int $id)
    {
        $vacationRequest = VacationRequest::findOrFail($id);
        $user = auth()->user();

        // Verificar permissão
        if ($vacationRequest->user_id !== $user->id) {
            abort(403);
        }

        // Verificar se pode ser cancelada
        if (!$vacationRequest->canBeCancelled()) {
            return back()->withErrors([
                'status' => 'Esta solicitação não pode ser cancelada.',
            ]);
        }

        $vacationRequest->update([
            'status' => VacationRequest::STATUS_CANCELLED,
        ]);

        return redirect()
            ->route('ferias.index')
            ->with('success', 'Solicitação cancelada com sucesso!');
    }

    /**
     * Display pending approvals for managers.
     */
    public function pendingApprovals()
    {
        $pendingRequests = VacationRequest::with('user')
            ->pending()
            ->orderBy('start_date', 'asc')
            ->paginate(10);

        return Inertia::render('Ferias/Approvals', [
            'pendingRequests' => $pendingRequests,
        ]);
    }

    /**
     * Approve a vacation request.
     */
    public function approve(int $id)
    {
        $vacationRequest = VacationRequest::findOrFail($id);
        $user = auth()->user();

        if ($vacationRequest->status !== VacationRequest::STATUS_PENDING) {
            return back()->withErrors([
                'status' => 'Esta solicitação já foi processada.',
            ]);
        }

        // Verificar saldo antes de aprovar
        $balance = VacationBalance::getOrCreateForUser($vacationRequest->user_id, $vacationRequest->year);
        if (!$balance->hasEnoughBalance($vacationRequest->period_days)) {
            return back()->withErrors([
                'balance' => 'O funcionário não possui saldo suficiente de férias.',
            ]);
        }

        // Aprovar e deduzir saldo
        $vacationRequest->update([
            'status' => VacationRequest::STATUS_APPROVED,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $balance->deductDays($vacationRequest->period_days);

        return back()->with('success', 'Solicitação aprovada com sucesso!');
    }

    /**
     * Reject a vacation request.
     */
    public function reject(Request $request, int $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $vacationRequest = VacationRequest::findOrFail($id);
        $user = auth()->user();

        if ($vacationRequest->status !== VacationRequest::STATUS_PENDING) {
            return back()->withErrors([
                'status' => 'Esta solicitação já foi processada.',
            ]);
        }

        $vacationRequest->update([
            'status' => VacationRequest::STATUS_REJECTED,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Solicitação rejeitada.');
    }

    /**
     * Get available years for filtering.
     */
    private function getAvailableYears(): array
    {
        $currentYear = (int) date('Y');
        return range($currentYear - 2, $currentYear + 1);
    }
}
