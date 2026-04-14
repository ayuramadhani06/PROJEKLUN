<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie; // Tambahkan ini

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Ambil username dari cookie jika ada
        $rememberedUsername = Cookie::get('remembered_username');

        return view('be.login', compact('rememberedUsername'));
    }

    public function process(Request $request)
    {
        $credentials = $request->validate([
            'username'    => ['required'],
            'password' => ['required'],
        ]);

        // Logika Remember Me (Laravel Built-in)
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // LOGIKA COOKIE UNTUK EMAIL:
            if ($remember) {
                // Simpan email selama 30 hari (43200 menit)
                Cookie::queue('remembered_username', $request->username, 43200);
            } else {
                // Hapus cookie jika tidak dicentang
                Cookie::queue(Cookie::forget('remembered_username'));
            }

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}