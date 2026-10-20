<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserRoleController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Users/Index', [
            'users' => User::query()
                ->select(['id', 'name', 'email', 'role'])
                ->orderBy('name')
                ->paginate(15),
            'roles' => [
                User::ROLE_ADMINISTRATOR => 'Administrador',
                User::ROLE_MANAGER => 'Gestor',
                User::ROLE_USER => 'Usuário',
            ],
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(User::ROLES)],
        ]);

        $isCurrentUser = $request->user()->is($user);

        if (
            $isCurrentUser
            && $validated['role'] !== User::ROLE_ADMINISTRATOR
            && ! User::query()
                ->where('role', User::ROLE_ADMINISTRATOR)
                ->where('id', '!=', $user->id)
                ->exists()
        ) {
            return back()->withErrors([
                'role' => 'Não é possível remover o perfil do único administrador.',
            ]);
        }

        $user->update(['role' => $validated['role']]);

        $destination = $isCurrentUser && $validated['role'] !== User::ROLE_ADMINISTRATOR
            ? 'dashboard'
            : 'users.index';

        return redirect()
            ->route($destination)
            ->with('success', 'Perfil atualizado com sucesso.');
    }
}
