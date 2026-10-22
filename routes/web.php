<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Contract support tickets
    Route::get('/chamados', [App\Http\Controllers\ContractTicketController::class, 'index'])->name('chamados.index');
    Route::get('/chamados/novo', [App\Http\Controllers\ContractTicketController::class, 'create'])->name('chamados.create');
    Route::post('/chamados', [App\Http\Controllers\ContractTicketController::class, 'store'])->name('chamados.store');
    Route::get('/chamados/{ticket}/pdf', [App\Http\Controllers\ContractTicketController::class, 'exportPdf'])
        ->name('chamados.pdf')
        ->whereNumber('ticket');
    Route::get('/chamados/anexos/{attachment}/download', [App\Http\Controllers\ContractTicketController::class, 'downloadAttachment'])
        ->name('chamados.attachments.download')
        ->whereNumber('attachment');
    Route::get('/chamados/{ticket}', [App\Http\Controllers\ContractTicketController::class, 'show'])->name('chamados.show')->whereNumber('ticket');
    Route::patch('/chamados/{ticket}', [App\Http\Controllers\ContractTicketController::class, 'update'])
        ->middleware('manager')
        ->name('chamados.update')
        ->whereNumber('ticket');
    Route::patch('/chamados/{ticket}/decisao', [App\Http\Controllers\ContractTicketController::class, 'decide'])
        ->middleware('manager')
        ->name('chamados.decide')
        ->whereNumber('ticket');
    Route::delete('/chamados/{ticket}', [App\Http\Controllers\ContractTicketController::class, 'destroy'])
        ->middleware('admin')
        ->name('chamados.destroy')
        ->whereNumber('ticket');

    // Settings routes
    Route::prefix('settings')->group(function () {
        Route::get('/profile', [App\Http\Controllers\Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
        Route::patch('/profile', [App\Http\Controllers\Settings\ProfileController::class, 'update'])->name('settings.profile.update');
        Route::delete('/profile', [App\Http\Controllers\Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');

        Route::get('/password', [App\Http\Controllers\Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
        Route::patch('/password', [App\Http\Controllers\Settings\PasswordController::class, 'update'])->name('settings.password.update');

        Route::get('/appearance', [App\Http\Controllers\Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');

        Route::get('/two-factor', [App\Http\Controllers\Settings\TwoFactorAuthenticationController::class, 'show'])->name('settings.two-factor.show');
    });

    // Profile routes (legacy compatibility)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Férias routes
    Route::get('/ferias', [App\Http\Controllers\VacationRequestController::class, 'index'])->name('ferias.index');
    Route::get('/ferias/nova', [App\Http\Controllers\VacationRequestController::class, 'create'])->name('ferias.create');
    Route::post('/ferias', [App\Http\Controllers\VacationRequestController::class, 'store'])->name('ferias.store');

    // Aprovações de férias ficam disponíveis para gestores e administradores.
    Route::middleware('manager')->group(function () {
        Route::get('/ferias/aprovacoes', [App\Http\Controllers\VacationRequestController::class, 'pendingApprovals'])->name('ferias.approvals');
        Route::patch('/ferias/{id}/aprovar', [App\Http\Controllers\VacationRequestController::class, 'approve'])->name('ferias.approve')->whereNumber('id');
        Route::patch('/ferias/{id}/rejeitar', [App\Http\Controllers\VacationRequestController::class, 'reject'])->name('ferias.reject')->whereNumber('id');
    });

    Route::middleware('admin')->group(function () {
        Route::get('/usuarios', [App\Http\Controllers\UserRoleController::class, 'index'])->name('users.index');
        Route::patch('/usuarios/{user}/perfil', [App\Http\Controllers\UserRoleController::class, 'update'])->name('users.role.update');
    });

    Route::get('/ferias/{id}', [App\Http\Controllers\VacationRequestController::class, 'show'])->name('ferias.show')->whereNumber('id');
    Route::patch('/ferias/{id}/cancelar', [App\Http\Controllers\VacationRequestController::class, 'cancel'])->name('ferias.cancel')->whereNumber('id');

    Route::get('/patrimonios', [App\Http\Controllers\PatrimonioController::class, 'index'])
        ->name('patrimonios.index');

    Route::middleware('manager')->group(function () {
        Route::resource('patrimonios', App\Http\Controllers\PatrimonioController::class)
            ->except(['index', 'show']);
    });
});

require __DIR__.'/auth.php';
