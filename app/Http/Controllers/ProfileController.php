<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Tambahkan ini
use App\Services\RetentionPolicyService; // Tambahkan ini

class ProfileController extends Controller
{
    // Inject Service ke method index
    public function index(RetentionPolicyService $retentionService)
    {
        // Ambil data retention days dari tabel system_settings
        $retentionDays = DB::table('system_settings')
            ->where('key', 'retention_days')
            ->value('value') ?? 30;

        // Ambil status policy aktual dari service TimescaleDB
        $activePolicy = $retentionService->getActivePolicy();

        // Kirim data ke view
        return view('be.profile', compact('retentionDays', 'activePolicy'));
    }

    // Update Nama dan Email
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    // Update Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password'     => 'required|min:6|confirmed', // Harus ada input password_confirmation
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Password lama salah!']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Profile changed successfully!');
    }
}