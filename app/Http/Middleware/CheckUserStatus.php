<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user sudah login
        if (Auth::check()) {
            $user = Auth::user();
            
            // Jika dia BUKAN admin DAN statusnya BUKAN approved
            if (!$user->isAdmin() && !$user->isApproved()) {
                // Paksa Logout saat itu juga!
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Tendang ke halaman login dengan pesan error
                return redirect()->route('login')->withErrors([
                    'email' => 'Akses ditolak! Status akun Anda saat ini: ' . strtoupper($user->status)
                ]);
            }
        }

        return $next($request);
    }
}