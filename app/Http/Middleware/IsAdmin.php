<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }
        
        // Rediriger vers le dashboard user si ce n'est pas un admin
        return redirect('/dashboard')->with('error', 'Accès réservé aux administrateurs');
    }
}
