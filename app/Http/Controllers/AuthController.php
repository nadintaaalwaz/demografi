<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        
        return view('kasi.login');
    }

    /**
     * Process login
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba login dengan remember me
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Regenerate session untuk keamanan
            $request->session()->regenerate();

            // Redirect sesuai role
            return $this->redirectToDashboard();
        }

        // Jika login gagal
        return back()->with('error', 'Username atau password salah')->withInput($request->only('username'));
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout');
    }

    /**
     * Redirect ke dashboard berdasarkan role
     */
    protected function redirectToDashboard()
    {
        $user = Auth::user();

        if ($user->role === 'kasi') {
            return redirect()->route('kasi.dashboard');
        } elseif ($user->role === 'kasun') {
            return redirect()->route('kasun.dashboard');
        }

        // Default redirect jika role tidak dikenali
        Auth::logout();
        return redirect()->route('login')->with('error', 'Role tidak valid');
    }
}
