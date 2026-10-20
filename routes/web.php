<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Contract routes
    Route::get('/contratos/novo', [App\Http\Controllers\ContractController::class, 'create'])->name('contracts.create');
    Route::post('/contratos', [App\Http\Controllers\ContractController::class, 'store'])->name('contracts.store');
    Route::get('/contratos', [App\Http\Controllers\ContractController::class, 'index'])->name('contracts.index');
    Route::get('/contratos/{contract}', [App\Http\Controllers\ContractController::class, 'show'])->name('contracts.show');
    Route::get('/contratos/{contract}/editar', [App\Http\Controllers\ContractController::class, 'edit'])->name('contracts.edit');
    Route::patch('/contratos/{contract}', [App\Http\Controllers\ContractController::class, 'update'])->name('contracts.update');
    Route::delete('/contratos/{contract}', [App\Http\Controllers\ContractController::class, 'destroy'])->name('contracts.destroy');

    // Contract Files routes
    Route::post('/contratos/{contract}/arquivos', [App\Http\Controllers\ContractFileController::class, 'store'])->name('contract-files.store');
    Route::get('/contratos/arquivos/{file}/download', [App\Http\Controllers\ContractFileController::class, 'download'])->name('contract-files.download');
    Route::get('/contratos/arquivos/{file}/preview', [App\Http\Controllers\ContractFileController::class, 'preview'])->name('contract-files.preview');
    Route::delete('/contratos/arquivos/{file}', [App\Http\Controllers\ContractFileController::class, 'destroy'])->name('contract-files.destroy');

    // Contract support tickets
    Route::get('/chamados', [App\Http\Controllers\ContractTicketController::class, 'index'])->name('chamados.index');
    Route::get('/chamados/novo', [App\Http\Controllers\ContractTicketController::class, 'create'])->name('chamados.create');
    Route::post('/chamados', [App\Http\Controllers\ContractTicketController::class, 'store'])->name('chamados.store');
    Route::get('/chamados/anexos/{attachment}/download', [App\Http\Controllers\ContractTicketController::class, 'downloadAttachment'])
        ->name('chamados.attachments.download')
        ->whereNumber('attachment');
    Route::get('/chamados/{ticket}', [App\Http\Controllers\ContractTicketController::class, 'show'])->name('chamados.show')->whereNumber('ticket');
    Route::patch('/chamados/{ticket}', [App\Http\Controllers\ContractTicketController::class, 'update'])
        ->middleware('manager')
        ->name('chamados.update')
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

    // Internal Communications routes
    Route::get('/comunicacoes-internas', [App\Http\Controllers\InternalCommunicationController::class, 'index'])->name('internal-communications.index');
    Route::get('/comunicacoes-internas/nova', [App\Http\Controllers\InternalCommunicationController::class, 'create'])->name('internal-communications.create');
    Route::post('/comunicacoes-internas', [App\Http\Controllers\InternalCommunicationController::class, 'store'])->name('internal-communications.store');
    Route::get('/comunicacoes-internas/{id}', [App\Http\Controllers\InternalCommunicationController::class, 'show'])->name('internal-communications.show');
    Route::get('/comunicacoes-internas/{id}/pdf', [App\Http\Controllers\InternalCommunicationController::class, 'exportPdf'])->name('internal-communications.pdf');

    // Oficios routes
    Route::get('/oficios', [App\Http\Controllers\OficioController::class, 'index'])->name('oficios.index');
    Route::get('/oficios/novo', [App\Http\Controllers\OficioController::class, 'create'])->name('oficios.create');
    Route::post('/oficios', [App\Http\Controllers\OficioController::class, 'store'])->name('oficios.store');
    Route::get('/oficios/{id}', [App\Http\Controllers\OficioController::class, 'show'])->name('oficios.show');
    Route::get('/oficios/{id}/pdf', [App\Http\Controllers\OficioController::class, 'exportPdf'])->name('oficios.pdf');

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

    // Patrimônios routes
    Route::resource('patrimonios', App\Http\Controllers\PatrimonioController::class);
});

require __DIR__.'/auth.php';
