<?php

namespace App\Http\Controllers;

use App\Models\Patrimonio;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatrimonioController extends Controller
{
    public function index(Request $request)
    {
        $query = Patrimonio::with('user');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('numero_patrimonio', 'like', "%{$request->search}%")
                    ->orWhere('nome', 'like', "%{$request->search}%")
                    ->orWhereHas('user', function ($qu) use ($request) {
                        $qu->where('name', 'like', "%{$request->search}%");
                    });
            });
        }

        return Inertia::render('Patrimonio/Index', [
            'patrimonios' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Patrimonio/Create', [
            'users' => User::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_patrimonio' => 'required|string|unique:patrimonios,numero_patrimonio',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'data_atribuicao' => 'nullable|date',
        ]);

        Patrimonio::create($validated);

        return redirect()->route('patrimonios.index')
            ->with('success', 'Patrimônio criado com sucesso.');
    }

    public function edit(Patrimonio $patrimonio)
    {
        return Inertia::render('Patrimonio/Edit', [
            'patrimonio' => $patrimonio->load('user'),
            'users' => User::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Patrimonio $patrimonio)
    {
        $validated = $request->validate([
            'numero_patrimonio' => 'required|string|unique:patrimonios,numero_patrimonio,' . $patrimonio->id,
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'data_atribuicao' => 'nullable|date',
        ]);

        $patrimonio->update($validated);

        return redirect()->route('patrimonios.index')
            ->with('success', 'Patrimônio atualizado com sucesso.');
    }

    public function destroy(Patrimonio $patrimonio)
    {
        $patrimonio->delete();

        return redirect()->route('patrimonios.index')
            ->with('success', 'Patrimônio excluído com sucesso.');
    }
}
