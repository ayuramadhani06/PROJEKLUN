<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SnifferController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 25);
        $perPage = max(1, min($perPage, 100));
        $search   = $request->get('search');
        $protocol = $request->get('protocol');
        $app      = $request->get('application');
        $start_date = $request->get('start_date');
        $end_date   = $request->get('end_date');
        $tab      = $request->get('tab', 'active'); // 'active' atau 'history'
        $min_bytes = $request->get('min_bytes');
        $max_bytes = $request->get('max_bytes');

        try {
            DB::purge();
            DB::reconnect();
            DB::statement('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');

            if ($tab === 'history') {
                $query = DB::table('flows_history');
            } else {
                $query = DB::table('flows_active');
            }

            // 🔥 SORT BYTES (WAJIB TARUH DI SINI)
            $sortBytes = $request->get('sort_bytes');

            if ($sortBytes === 'asc') {
                $query->orderBy('bytes', 'asc');
            } elseif ($sortBytes === 'desc') {
                $query->orderBy('bytes', 'desc');
            } else {
                $query->orderByDesc('last_seen'); // default
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('client_ip', 'like', "%{$search}%")
                      ->orWhere('server_ip', 'like', "%{$search}%")
                      ->orWhere('server_name', 'like', "%{$search}%")
                      ->orWhere('protocol_l7', 'like', "%{$search}%");
                });
            }

            if (!empty($protocol)) $query->where('protocol_l4', $protocol);
            if (!empty($app))      $query->where('protocol_l7', 'like', "%{$app}%");
            // 🔥 FILTER BYTES
            if (!empty($min_bytes)) {
                $query->where('bytes', '>=', (int)$min_bytes);
            }
            if (!empty($max_bytes)) {
                $query->where('bytes', '<=', (int)$max_bytes);
            }

            // Date filters for history tab
            if ($tab === 'history') {
                if (!empty($start_date)) $query->where('last_seen', '>=', $start_date . ' 00:00:00');
                if (!empty($end_date))   $query->where('last_seen', '<=', $end_date . ' 23:59:59');
            }

            $flows = $query->paginate($perPage)->appends($request->query());

            // Mapping agar Blade tidak error (mengubah nama kolom DB ke nama yang diharapkan Blade)
            $flows->getCollection()->transform(function ($f) {
                return $this->mapFlowColumns($f);
            });
            
            // --- STATS ---
            if ($tab === 'history') {
                $baseQuery = DB::table('flows_history');
            } else {
                $baseQuery = DB::table('flows_active');
            }

            $totalFlows = $baseQuery->count();
            $totalBytes = DB::table('flows_history')->sum('bytes') ?: 0;
            $uniqueSrc  = $baseQuery->distinct('client_ip')->count('client_ip');
            $uniqueDst  = $baseQuery->distinct('server_ip')->count('server_ip');

            // Filtered stats for history tab
            $filteredStats = null;
            if ($tab === 'history' && (!empty($start_date) || !empty($end_date) || !empty($search) || !empty($protocol) || !empty($app))) {
                $filteredQuery = clone $query;
                $filteredStats = [
                    'total_bytes' => $filteredQuery->sum('bytes') ?: 0,
                    'unique_src'  => $filteredQuery->distinct('client_ip')->count('client_ip'),
                    'unique_dst'  => $filteredQuery->distinct('server_ip')->count('server_ip'),
                ];
            }

            // List untuk dropdown filter
            $protocolList    = DB::table('flows_history')->select('protocol_l4 as protocol')->distinct()->pluck('protocol');
            $applicationList = DB::table('flows_history')->select('protocol_l7 as application')->distinct()->whereNotNull('protocol_l7')->pluck('application');

        } catch (\Exception $e) {
            $flows = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
            $totalFlows = $totalBytes = $uniqueSrc = $uniqueDst = 0;
            $protocolList = $applicationList = collect();
        }

        return view('be.sniffer', compact(
            'flows', 'perPage', 'search', 'protocol', 'app', 'tab',
            'totalFlows', 'totalBytes', 'uniqueSrc', 'uniqueDst',
            'protocolList', 'applicationList', 'start_date', 'end_date', 'filteredStats'
        ));
    }

    public function api(Request $request)
    {
        try {
            DB::purge();
            DB::reconnect();
            DB::statement('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');

            $search   = $request->get('search');
            $protocol = $request->get('protocol');
            $app      = $request->get('application');
            $perPage  = (int) $request->get('per_page', 25);
            $min_bytes = $request->get('min_bytes');
            $max_bytes = $request->get('max_bytes');

            // Untuk API Live, kita ambil dari flows_active agar real-time!
            $sortBytes = $request->get('sort_bytes');

            $query = DB::table('flows_active')
                ->where('last_seen', '>=', now()->subSeconds(15));

            // 🔥 SORTING BYTES
            if ($sortBytes === 'asc') {
                $query->orderBy('bytes', 'asc');
            } elseif ($sortBytes === 'desc') {
                $query->orderBy('bytes', 'desc');
            } else {
                $query->orderByDesc('last_seen'); // default
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('client_ip', 'like', "%{$search}%")
                      ->orWhere('server_ip', 'like', "%{$search}%")
                      ->orWhere('server_name', 'like', "%{$search}%");
                });
            }
            if (!empty($protocol)) $query->where('protocol_l4', $protocol);
            if (!empty($app))      $query->where('protocol_l7', 'like', "%{$app}%");
            if (!empty($min_bytes)) {
                $query->where('bytes', '>=', (int)$min_bytes);
            }

            if (!empty($max_bytes)) {
                $query->where('bytes', '<=', (int)$max_bytes);
            }

            $total = $query->count();
            $flows = $query->limit($perPage)->get()->map(function ($f) {
                $mapped = $this->mapFlowColumns($f);
                $mapped->time_ago = Carbon::parse($f->last_seen)->diffForHumans();
                return $mapped;
            });

            // Stats terbaru
            $totalBytes = DB::table('flows_history')->sum('bytes') ?: 0;

            return response()->json([
                'success' => true,
                'flows'   => $flows,
                'total'   => $total,
                'stats'   => [
                    'total_bytes' => $this->formatBytes($totalBytes),
                    'unique_src'  => number_format(DB::table('flows_active')->distinct('client_ip')->count('client_ip')),
                    'unique_dst'  => number_format(DB::table('flows_active')->distinct('server_ip')->count('server_ip')),
                ],
                'server_time' => now()->format('H:i:s'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Helper untuk menyamakan nama kolom DB dengan property di Blade/JS
    private function mapFlowColumns($f) {
        $f->src_ip      = $f->client_ip;
        $f->dest_ip     = $f->server_ip;
        $f->protocol    = $f->protocol_l4;
        $f->application = $f->protocol_l7;
        $f->total_bytes = $f->bytes;
        $f->seen_last   = $f->last_seen;
        $f->info        = $f->server_name; // Menggunakan server_name sebagai info
        return $f;
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1000));
        $pow   = min($pow, count($units) - 1);
        $bytes /= pow(1000, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}