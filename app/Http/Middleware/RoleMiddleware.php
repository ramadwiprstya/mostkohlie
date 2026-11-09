<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;
        $matched = false;

        foreach ($roles as $role) {
            if ($userRole === $role) {
                $matched = true;
                break;
            }
            // Allow 'admin' to satisfy 'owner' requirement and vice versa
            if (in_array($userRole, ['owner', 'admin']) && in_array($role, ['owner', 'admin'])) {
                $matched = true;
                break;
            }
        }

        if (!$matched) {
            abort(403, 'Anda tidak memiliki hak akses untuk tindakan atau halaman ini.');
        }

        return $next($request);
    }
}
