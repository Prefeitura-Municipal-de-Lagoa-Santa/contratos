<?php

namespace App\Http\Controllers;

use App\Models\Oficio;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class OficioController extends Controller
{
    /**
     * Display a listing of oficios.
     */
    public function index()
    {
        $oficios = Oficio::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Oficios/Index', [
            'oficios' => $oficios,
        ]);
    }

    /**
     * Show the form for creating a new oficio.
     */
    public function create()
    {
        return Inertia::render('Oficios/Create', [
            'nextCode' => Oficio::generateNextCode(),
        ]);
    }

    /**
     * Store a newly created oficio.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:oficios,code',
            'date' => 'required|date',
            'sender_name' => 'required|string|max:255',
            'sender_role' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'recipient_role' => 'nullable|string|max:255',
            'recipient_institution' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $validated['year'] = date('Y');
        $validated['user_id'] = auth()->id();

        $oficio = Oficio::create($validated);

        return redirect()
            ->route('oficios.show', $oficio->id)
            ->with('success', 'Ofício criado com sucesso!');
    }

    /**
     * Display the specified oficio.
     */
    public function show(int $id)
    {
        $oficio = Oficio::with('user')->findOrFail($id);

        return Inertia::render('Oficios/Show', [
            'oficio' => $oficio,
        ]);
    }

    /**
     * Export the oficio as PDF.
     */
    public function exportPdf(int $id)
    {
        $oficio = Oficio::findOrFail($id);

        $pdf = Pdf::loadView('pdf.oficio', [
            'oficio' => $oficio,
        ]);

        $pdf->setPaper('A4');

        $filename = 'OF_' . str_replace('/', '_', $oficio->code) . '.pdf';

        return $pdf->download($filename);
    }
}
