<?php

use App\Models\User;
use App\Models\VacationBalance;
use App\Models\VacationRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

function createVacationRequest(
    User $user,
    string $startDate,
    string $endDate,
    string $status = VacationRequest::STATUS_PENDING,
    int $periodDays = 15,
): VacationRequest {
    return VacationRequest::create([
        'user_id' => $user->id,
        'supervisor_id' => $user->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'period_days' => $periodDays,
        'status' => $status,
        'year' => Carbon::parse($startDate)->format('Y'),
    ]);
}

function createVacationBalance(User $user, string $year, int $remainingDays = 30): VacationBalance
{
    return VacationBalance::create([
        'user_id' => $user->id,
        'year' => $year,
        'total_days' => 30,
        'used_days' => 30 - $remainingDays,
        'remaining_days' => $remainingDays,
    ]);
}

it('returns only user summaries with vacation approvals', function () {
    $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
    $employee = User::factory()->create();
    $request = createVacationRequest(
        $employee,
        now()->addDays(60)->toDateString(),
        now()->addDays(74)->toDateString(),
    );

    $this->actingAs($manager)
        ->get(route('ferias.approvals'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Ferias/Approvals')
            ->where('pendingRequests.data.0.user.id', $employee->id)
            ->where('pendingRequests.data.0.user.name', $employee->name)
            ->missing('pendingRequests.data.0.user.email')
            ->missing('pendingRequests.data.0.user.username')
            ->missing('pendingRequests.data.0.user.role')
            ->missing('pendingRequests.data.0.user.is_admin')
            ->missing('auth.user.email')
            ->missing('auth.user.username')
            ->missing('auth.user.role')
            ->missing('auth.user.is_admin'));

    $this->actingAs($employee)
        ->get(route('ferias.show', $request->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Ferias/Show')
            ->where('vacationRequest.user.id', $employee->id)
            ->where('vacationRequest.user.name', $employee->name)
            ->missing('vacationRequest.user.email')
            ->missing('vacationRequest.user.username')
            ->missing('vacationRequest.user.role')
            ->missing('vacationRequest.user.is_admin')
            ->missing('vacationRequest.supervisor.email')
            ->missing('vacationRequest.supervisor.username')
            ->missing('vacationRequest.supervisor.role')
            ->missing('vacationRequest.supervisor.is_admin'));
});

it('validates year filters before querying or creating balances', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('ferias.index', ['year' => ['2026']]))
        ->assertSessionHasErrors('year');

    $unsupportedYear = (string) ((int) now()->format('Y') + 10);
    $this->get(route('ferias.index', ['year' => $unsupportedYear]))
        ->assertSessionHasErrors('year');

    $this->assertDatabaseMissing('vacation_balances', [
        'user_id' => $user->id,
        'year' => '2026',
    ]);
    $this->assertDatabaseMissing('vacation_balances', [
        'user_id' => $user->id,
        'year' => $unsupportedYear,
    ]);
});

it('rechecks the simultaneous vacation limit when approving a request', function () {
    $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
    $firstEmployee = User::factory()->create();
    $secondEmployee = User::factory()->create();
    $pendingEmployee = User::factory()->create();
    $startDate = now()->addDays(60)->toDateString();
    $endDate = now()->addDays(74)->toDateString();

    createVacationRequest($firstEmployee, $startDate, $endDate, VacationRequest::STATUS_APPROVED);
    createVacationRequest($secondEmployee, $startDate, $endDate, VacationRequest::STATUS_APPROVED);
    $pendingRequest = createVacationRequest($pendingEmployee, $startDate, $endDate);
    $balance = createVacationBalance($pendingEmployee, Carbon::parse($startDate)->format('Y'));

    $this->actingAs($manager)
        ->patch(route('ferias.approve', $pendingRequest->id))
        ->assertSessionHasErrors('start_date');

    $this->assertDatabaseHas('vacation_requests', [
        'id' => $pendingRequest->id,
        'status' => VacationRequest::STATUS_PENDING,
    ]);
    $this->assertDatabaseHas('vacation_balances', [
        'id' => $balance->id,
        'remaining_days' => 30,
        'used_days' => 0,
    ]);
});

it('allows approval when overlapping requests are not simultaneous with each other', function () {
    $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
    $firstEmployee = User::factory()->create();
    $secondEmployee = User::factory()->create();
    $pendingEmployee = User::factory()->create();
    $firstStart = now()->addDays(60)->toDateString();
    $firstEnd = now()->addDays(64)->toDateString();
    $secondStart = now()->addDays(85)->toDateString();
    $secondEnd = now()->addDays(89)->toDateString();
    $startDate = $firstStart;
    $endDate = $secondEnd;

    createVacationRequest($firstEmployee, $firstStart, $firstEnd, VacationRequest::STATUS_APPROVED);
    createVacationRequest($secondEmployee, $secondStart, $secondEnd, VacationRequest::STATUS_APPROVED);
    $pendingRequest = createVacationRequest(
        $pendingEmployee,
        $startDate,
        $endDate,
        periodDays: 30,
    );
    createVacationBalance($pendingEmployee, Carbon::parse($startDate)->format('Y'));

    $this->actingAs($manager)
        ->patch(route('ferias.approve', $pendingRequest->id))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('vacation_requests', [
        'id' => $pendingRequest->id,
        'status' => VacationRequest::STATUS_APPROVED,
    ]);
});

it('does not approve a vacation without sufficient balance', function () {
    $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
    $employee = User::factory()->create();
    $startDate = now()->addDays(60)->toDateString();
    $endDate = now()->addDays(74)->toDateString();
    $pendingRequest = createVacationRequest($employee, $startDate, $endDate);
    $balance = createVacationBalance($employee, Carbon::parse($startDate)->format('Y'), 10);

    $this->actingAs($manager)
        ->patch(route('ferias.approve', $pendingRequest->id))
        ->assertSessionHasErrors('balance');

    $this->assertDatabaseHas('vacation_requests', [
        'id' => $pendingRequest->id,
        'status' => VacationRequest::STATUS_PENDING,
    ]);
    $this->assertDatabaseHas('vacation_balances', [
        'id' => $balance->id,
        'remaining_days' => 10,
        'used_days' => 20,
    ]);
});

it('approves a request and deducts its balance once', function () {
    $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
    $employee = User::factory()->create();
    $startDate = now()->addDays(60)->toDateString();
    $endDate = now()->addDays(74)->toDateString();
    $pendingRequest = createVacationRequest($employee, $startDate, $endDate);
    $balance = createVacationBalance($employee, Carbon::parse($startDate)->format('Y'));

    $this->actingAs($manager)
        ->patch(route('ferias.approve', $pendingRequest->id))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('vacation_requests', [
        'id' => $pendingRequest->id,
        'status' => VacationRequest::STATUS_APPROVED,
        'approved_by' => $manager->id,
    ]);
    $this->assertDatabaseHas('vacation_balances', [
        'id' => $balance->id,
        'remaining_days' => 15,
        'used_days' => 15,
    ]);

    $this->patch(route('ferias.approve', $pendingRequest->id))
        ->assertSessionHasErrors('status');

    expect(DB::table('vacation_balances')->where('id', $balance->id)->value('remaining_days'))->toBe(15);
});
