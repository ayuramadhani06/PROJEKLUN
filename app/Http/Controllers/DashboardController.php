<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            DB::purge();
            DB::reconnect();

            $status = [
                'active_sessions' => DB::table('flows_active')->distinct('client_ip')->count('client_ip'),
                'total_endpoints' => DB::table('flows_history')->distinct('server_ip')->count('server_ip'),
                'active_flows'    => DB::table('flows_active')->count(),
                'last_update'     => DB::table('flows_active')->max('last_seen') ?? now(),
            ];

            $recentActivities = DB::table('flows_active')
                ->select('client_ip', 'protocol_l7', 'last_seen')
                ->orderByDesc('last_seen')
                ->limit(10)
                ->get();

        } catch (\Exception $e) {
            $status = ['active_sessions' => 0, 'total_endpoints' => 0, 'active_flows' => 0, 'last_update' => now()];
            $recentActivities = collect();
        }

        return view('be.dashboard', compact('status', 'recentActivities'));
    }
}