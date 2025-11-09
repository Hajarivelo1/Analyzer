<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'user') {
            return $next($request);
        }
        
        // Rediriger vers le dashboard admin si c'est un admin
        return redirect('/admin/dashboard')->with('error', 'Accès réservé aux utilisateurs');
    }
}
