<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $type)
    {
        // kalau belum login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // kalau butuh manage tapi user bukan manage
        if ($type === 'manage' && !auth()->user()->canManage()) {
            abort(403, 'Tidak punya akses');
        }

        return $next($request);
    }
}