<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        return Inertia::render('SuperAdmin/UserManagement', [
            'users' => $users,

        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nrp' => 'required|string|email|max:255|unique:users',
            'employee_id' => 'nullable|string|min:8|confirmed',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'nrp' => $request->nrp,
            'employee_id' => $request->employee_id,
            'role' => $request->role,
            'password' => bcrypt($request->password),
        ]);

        return back()->with('success', 'User baru berhasil dibuat!');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        // Set password sesuai NRP dan enkripsi menggunakan bcrypt
        $user->update([
            'password' => bcrypt($user->nrp)
        ]);

        return back()->with('success', 'Password berhasil di-reset menjadi default (NRP)!');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'User berhasil dihapus!');
    }

    // 1. Masuk sebagai User
    public function impersonate($id)
    {
        $adminId = Auth::id(); // Simpan ID Admin
        $userToImpersonate = User::findOrFail($id);

        // Simpan ID Admin di session agar bisa kembali nanti
        Session::put('impersonator_id', $adminId);

        // Login sebagai user target
        Auth::login($userToImpersonate);

        return redirect()->route('dashboard')->with('success', 'Sekarang Anda masuk sebagai ' . $userToImpersonate->name);
    }

    // 2. Kembali ke Admin
    public function stopImpersonate()
    {
        // dd([
        //     'auth_user' => Auth::user(),
        //     'session_all' => Session::all(),
        //     'impersonator_id' => Session::get('impersonator_id'),
        // ]);

        $adminId = Session::pull('impersonator_id');

        if ($adminId) {

            $admin = User::findOrFail($adminId);

            Auth::login($admin);

            return 'BERHASIL BALIK KE ADMIN';
        }

        return redirect('/');
    }
}
