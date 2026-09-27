<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // If the user's role is in the permitted roles, allow request
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // If customer tries to access admin/staff routes
        if ($user->isCustomer()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak akses untuk membuka halaman ini.');
        }

        // If admin/staff tries to access customer routes
        return redirect()->route('dashboard');
    }
}
