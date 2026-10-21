<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsManager
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->canManageTickets()) {
            abort(403, 'Acesso não autorizado. Apenas gestores e administradores podem acessar esta página.');
        }

        return $next($request);
    }
}
