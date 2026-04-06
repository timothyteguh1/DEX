<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Rules\IndonesianPhone;

class SuperAdminController extends Controller
{
    // =========================================================
    // LOG HISTORY
    // =========================================================

    public function logs()
    {
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->get();
        return view('superadmin.logs', compact('logs'));
    }

    // =========================================================
    // HAPUS USER / ADMIN
    // =========================================================

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri!');
        }

        $namaUser = $user->name;
        $roleUser = $user->role;
        $user->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_ACCOUNT',
            'description' => auth()->user()->name . ' menghapus permanen akun ' . $roleUser . ' bernama ' . $namaUser,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Akun $roleUser bernama $namaUser berhasil dihapus permanen.");
    }

    // =========================================================
    // RESET PASSWORD MANUAL
    // =========================================================

    public function resetPassword(Request $request, User $user)
    {
        $request->validate(['new_password' => 'required|min:6']);

        $user->update(['password' => Hash::make($request->new_password)]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'RESET_PASSWORD',
            'description' => auth()->user()->name . ' me-reset password akun ' . $user->name . ' secara manual.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Password untuk {$user->name} berhasil direset!");
    }

    // =========================================================
    // KELOLA ADMIN — STEP 3
    // =========================================================

    public function manageAdmins()
    {
        $admins = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPERADMIN])
            ->orderByRaw("FIELD(role, 'superadmin', 'admin')")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('superadmin.admins', compact('admins'));
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'no_hp'    => ['required', 'string', 'max:15', new IndonesianPhone],
            'password' => 'required|min:6',
        ]);

        $admin = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'no_hp'             => $request->no_hp,
            'password'          => Hash::make($request->password),
            'role'              => User::ROLE_ADMIN,
            'status'            => User::STATUS_APPROVED,
            'email_verified_at' => now(),
        ]);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'CREATE_ADMIN',
            'description' => auth()->user()->name . ' membuat akun Admin baru: ' . $admin->name . ' (' . $admin->email . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Admin {$admin->name} berhasil dibuat!");
    }

    public function updateAdmin(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Gunakan halaman profil untuk edit akun Anda sendiri.');
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Tidak bisa mengedit akun Superadmin.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_hp'    => ['required', 'string', 'max:15', new IndonesianPhone],
            'password' => 'nullable|min:6',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->no_hp = $request->no_hp;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'UPDATE_ADMIN',
            'description' => auth()->user()->name . ' mengedit data Admin: ' . $user->name . ' (' . $user->email . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Data Admin {$user->name} berhasil diperbarui!");
    }
}