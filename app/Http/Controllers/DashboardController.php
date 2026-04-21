<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    const ACTIVE_THRESHOLD_SECONDS = 60;

    public function index()
    {
        try {
            DB::purge();
            DB::reconnect();

            $since = now()->subSeconds(self::ACTIVE_THRESHOLD_SECONDS);

            $status = [
                'active_sessions' => DB::table('flows_active')
                                        ->where('last_seen', '>=', $since)
                                        ->distinct('client_ip')->count('client_ip'),
                'total_endpoints' => DB::table('flows_history')
                                        ->distinct('server_ip')->count('server_ip'),
                'active_flows'    => DB::table('flows_active')
                                        ->where('last_seen', '>=', $since)
                                        ->count(),
                'last_update'     => DB::table('flows_active')->max('last_seen') ?? now(),
            ];

            $recentActivities = DB::table('flows_active')
                ->select('client_ip', 'client_name', 'protocol_l7', 'last_seen')
                ->where('last_seen', '>=', $since)
                ->orderByDesc('last_seen')
                ->limit(10)
                ->get();

            $topApps = DB::table('flows_history')
                ->selectRaw("
                    CASE
                        WHEN protocol_l7 IS NULL OR protocol_l7 = ''
                        THEN 'Unknown'
                        ELSE protocol_l7
                    END as app,
                    COUNT(*) as total
                ")
                ->groupBy('app')
                ->orderByDesc('total')
                ->limit(6)
                ->get();

        } catch (\Exception $e) {
            $status = ['active_sessions' => 0, 'total_endpoints' => 0, 'active_flows' => 0, 'last_update' => now()];
            $recentActivities = collect();
            $topApps = collect();
        }

        return view('be.dashboard', compact('status', 'recentActivities', 'topApps'));
    }
}