<?php

namespace App\Http\Controllers;

use App\Models\InternalCommunication;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class InternalCommunicationController extends Controller
{
    /**
     * Display a listing of internal communications.
     */
    public function index()
    {
        $communications = InternalCommunication::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('InternalCommunications/Index', [
            'communications' => $communications,
        ]);
    }

    /**
     * Show the form for creating a new internal communication.
     */
    public function create()
    {
        return Inertia::render('InternalCommunications/Create', [
            'nextCode' => InternalCommunication::generateNextCode(),
        ]);
    }

    /**
     * Store a newly created internal communication.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:internal_communications,code',
            'date' => 'required|date',
            'sender_name' => 'required|string|max:255',
            'sender_role' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'recipient_role' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $validated['year'] = date('Y');
        $validated['user_id'] = auth()->id();

        $communication = InternalCommunication::create($validated);

        return redirect()
            ->route('internal-communications.show', $communication->id)
            ->with('success', 'Comunicação interna criada com sucesso!');
    }

    /**
     * Display the specified internal communication.
     */
    public function show(int $id)
    {
        $communication = InternalCommunication::with('user')->findOrFail($id);

        return Inertia::render('InternalCommunications/Show', [
            'communication' => $communication,
        ]);
    }

    /**
     * Export the internal communication as PDF.
     */
    public function exportPdf(int $id)
    {
        $communication = InternalCommunication::findOrFail($id);

        $pdf = Pdf::loadView('pdf.internal-communication', [
            'communication' => $communication,
        ]);

        $pdf->setPaper('A4');

        $filename = 'CI_' . str_replace('/', '_', $communication->code) . '.pdf';

        return $pdf->download($filename);
    }
}
