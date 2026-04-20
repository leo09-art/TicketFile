<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (auth()->check() && auth()->user()->role === $role) {
            return $next($request);
        }

        // Rediriger vers le bon dashboard selon le rôle
        if (auth()->check()) {
            return match(auth()->user()->role) {
                'admin'  => redirect()->route('admin.dashboard'),
                'agent'  => redirect()->route('agent.dashboard'),
                'usager' => redirect()->route('usager.dashboard'),
                default  => redirect()->route('login'),
            };
        }

        return redirect()->route('login');
    }
}

