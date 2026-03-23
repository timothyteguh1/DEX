<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    // Menampilkan halaman Log History
    public function logs()
    {
        // Ambil semua log beserta data pelakunya
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->get();
        return view('superadmin.logs', compact('logs'));
    }

    // Menghapus permanen User / Admin
    public function destroyUser(User $user)
    {
        // Superadmin tidak bisa menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri!');
        }

        $namaUser = $user->name;
        $roleUser = $user->role;
        $user->delete();

        // Catat di Log Aktivitas
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_ACCOUNT',
            'description' => auth()->user()->name . ' menghapus permanen akun ' . $roleUser . ' bernama ' . $namaUser,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Akun $roleUser bernama $namaUser berhasil dihapus permanen.");
    }

    // Mereset Password User / Admin secara MANUAL (Dari Modal Pop-up)
    public function resetPassword(Request $request, User $user)
    {
        // Validasi inputan pop-up
        $request->validate([
            'new_password' => 'required|min:6'
        ]);

        $newPassword = $request->new_password;
        
        // Update password baru
        $user->update(['password' => Hash::make($newPassword)]);

        // Catat di Log Aktivitas
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'RESET_PASSWORD',
            'description' => auth()->user()->name . ' me-reset password akun ' . $user->name . ' secara manual.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Password untuk {$user->name} berhasil direset!");
    }
}