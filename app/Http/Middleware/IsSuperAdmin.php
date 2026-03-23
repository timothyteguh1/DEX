<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        return redirect()->route('admin.dashboard')->with('error', '⛔ Akses Ditolak! Fitur ini hanya untuk Superadmin.');
    }
}