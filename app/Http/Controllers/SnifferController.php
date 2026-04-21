<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SnifferController extends Controller
{
    public function active(Request $request)
    {
        $perPage  = (int) $request->get('per_page', 25);
        $perPage  = max(1, min($perPage, 100));
        $search   = $request->get('search');
        $protocol = $request->get('protocol');
        $app      = $request->get('application');

        try {
            DB::purge(); DB::reconnect();
            DB::statement('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');

            $protocolList    = DB::table('flows_history')->select('protocol_l4 as protocol')->distinct()->pluck('protocol');
            $applicationList = DB::table('flows_history')->select('protocol_l7 as application')->distinct()->whereNotNull('protocol_l7')->pluck('application');

            // ✅ Total data dari flows_history (akumulasi semua traffic)
            // ✅ Unique src/dst dari flows_active (yang sedang aktif sekarang)
            $totalBytes = DB::table('flows_history')->sum('bytes') ?: 0;
            $uniqueSrc  = DB::table('flows_active')->distinct('client_ip')->count('client_ip');
            $uniqueDst  = DB::table('flows_active')->distinct('server_ip')->count('server_ip');

        } catch (\Exception $e) {
            $totalBytes = $uniqueSrc = $uniqueDst = 0;
            $protocolList = $applicationList = collect();
        }

        return view('be.sniffer-active', compact(
            'perPage', 'search', 'protocol', 'app',
            'totalBytes', 'uniqueSrc', 'uniqueDst',
            'protocolList', 'applicationList'
        ));
    }

    public function history(Request $request)
    {
        $perPage    = (int) $request->get('per_page', 25);
        $perPage    = max(1, min($perPage, 100));
        $search     = $request->get('search');
        $protocol   = $request->get('protocol');
        $app        = $request->get('application');
        $start_date = $request->get('start_date');
        $end_date   = $request->get('end_date');
        $min_bytes  = $request->get('min_bytes');
        $max_bytes  = $request->get('max_bytes');

        try {
            DB::purge(); DB::reconnect();
            DB::statement('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');

            $query = DB::table('flows_history');

            $sortBytes = $request->get('sort_bytes');
            if ($sortBytes === 'asc')      $query->orderBy('bytes', 'asc');
            elseif ($sortBytes === 'desc') $query->orderBy('bytes', 'desc');
            else                           $query->orderByDesc('last_seen');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('client_ip', 'like', "%{$search}%")
                      ->orWhere('server_ip', 'like', "%{$search}%")
                      ->orWhere('server_name', 'like', "%{$search}%")
                      ->orWhere('protocol_l7', 'like', "%{$search}%");
                });
            }

            if (!empty($protocol))   $query->where('protocol_l4', $protocol);
            if (!empty($app))        $query->where('protocol_l7', 'like', "%{$app}%");
            if (!empty($min_bytes))  $query->where('bytes', '>=', (int)$min_bytes);
            if (!empty($max_bytes))  $query->where('bytes', '<=', (int)$max_bytes);
            if (!empty($start_date)) $query->where('last_seen', '>=', $start_date . ' 00:00:00');
            if (!empty($end_date))   $query->where('last_seen', '<=', $end_date . ' 23:59:59');

            // ✅ Clone SEBELUM paginate() agar filteredStats bisa pakai query yang sama
            $filteredStats = null;
            $hasFilter = !empty($start_date) || !empty($end_date) || !empty($search)
                      || !empty($protocol)   || !empty($app)      || !empty($min_bytes)
                      || !empty($max_bytes);

            if ($hasFilter) {
                $filteredQuery = clone $query;
                $filteredStats = [
                    'total_bytes' => $filteredQuery->sum('bytes') ?: 0,
                    'unique_src'  => (clone $filteredQuery)->distinct('client_ip')->count('client_ip'),
                    'unique_dst'  => (clone $filteredQuery)->distinct('server_ip')->count('server_ip'),
                ];
            }

            // Paginate setelah clone
            $flows = $query->paginate($perPage)->appends($request->query());
            $flows->getCollection()->transform(fn($f) => $this->mapFlowColumns($f));

            // ✅ Stats global (tanpa filter) — selalu dari keseluruhan flows_history
            $totalFlows = DB::table('flows_history')->count();
            $totalBytes = DB::table('flows_history')->sum('bytes') ?: 0;
            $uniqueSrc  = DB::table('flows_history')->distinct('client_ip')->count('client_ip');
            $uniqueDst  = DB::table('flows_history')->distinct('server_ip')->count('server_ip');

            $protocolList    = DB::table('flows_history')->select('protocol_l4 as protocol')->distinct()->pluck('protocol');
            $applicationList = DB::table('flows_history')->select('protocol_l7 as application')->distinct()->whereNotNull('protocol_l7')->pluck('application');

        } catch (\Exception $e) {
            $flows         = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
            $totalFlows    = $totalBytes = $uniqueSrc = $uniqueDst = 0;
            $filteredStats = null;
            $protocolList  = $applicationList = collect();
        }

        return view('be.sniffer-history', compact(
            'flows', 'perPage', 'search', 'protocol', 'app',
            'totalFlows', 'totalBytes', 'uniqueSrc', 'uniqueDst',
            'protocolList', 'applicationList', 'start_date', 'end_date', 'filteredStats'
        ));
    }

public function api(Request $request)
{
    try {
        DB::purge();
        DB::reconnect();

        // ── PARAMS ──
        $page    = max(1, (int) $request->get('page', 1));
        $perPage = max(1, min((int)$request->get('per_page', 25), 100));

        $search   = $request->get('search') ?? '';
        $protocol = $request->get('protocol') ?? '';
        $app      = $request->get('application') ?? '';
        $sort     = $request->get('sort_bytes') ?? '';

        // ── BASE QUERY (NO TIME FILTER) ──
        $query = DB::table('flows_active');

        // ── FILTER ──
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('client_ip', 'like', "%{$search}%")
                  ->orWhere('server_ip', 'like', "%{$search}%")
                  ->orWhere('server_name', 'like', "%{$search}%");
            });
        }

        if ($protocol !== '') {
            $query->where('protocol_l4', $protocol);
        }

        if ($app !== '') {
            $query->where('protocol_l7', 'like', "%{$app}%");
        }

        // ── SORT (IMPORTANT: stable sort) ──
        if ($sort === 'asc') {
            $query->orderBy('bytes', 'asc');
        } elseif ($sort === 'desc') {
            $query->orderBy('bytes', 'desc');
        } else {
            $query->orderByDesc('bytes')
                  ->orderByDesc('last_seen');
        }

        // ── PAGINATION ──
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // ── MAP SAFE ──
        $flows = collect($paginator->items())->map(function ($f) {
            try {
                $mapped = $this->mapFlowColumns($f);

                $mapped->time_ago = $f->last_seen
                    ? Carbon::parse($f->last_seen)->diffForHumans()
                    : '-';

                return $mapped;

            } catch (\Exception $e) {
                return null; // skip bad row
            }
        })->filter()->values();

        // ── STATS ──
        $totalBytes = DB::table('flows_history')->sum('bytes') ?? 0;

        $uniqueSrc = DB::table('flows_active')
            ->distinct()->count('client_ip');

        $uniqueDst = DB::table('flows_active')
            ->distinct()->count('server_ip');

        // ── RESPONSE ──
        return response()->json([
            'success' => true,
            'flows'   => $flows,

            'total'         => $paginator->total(),
            'current_page'  => $paginator->currentPage(),
            'last_page'     => $paginator->lastPage(),
            'per_page'      => $paginator->perPage(),

            'stats' => [
                'total_bytes' => $this->formatBytes($totalBytes),
                'unique_src'  => number_format($uniqueSrc),
                'unique_dst'  => number_format($uniqueDst),
            ],

            'server_time' => now()->format('H:i:s'),
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'error'   => $e->getMessage(),
            'line'    => $e->getLine(),
            'file'    => $e->getFile(),
        ], 500);
    }
}

    private function mapFlowColumns($f)
    {
        $f->src_ip      = $f->client_ip;
        $f->dest_ip     = $f->server_ip;
        $f->protocol    = $f->protocol_l4;
        $f->application = $f->protocol_l7;
        $f->total_bytes = $f->bytes;
        $f->seen_last   = $f->last_seen;
        $f->info        = $f->server_name;
        $f->hostname    = $f->client_name ?? null; // ✅ kolom baru
        $f->column_info = $f->column_info; // ✅ kolom baru

        $f->unique_flow_key = $f->id ?? md5(
            $f->client_ip . '-' . 
            $f->server_ip . '-' . 
            $f->protocol_l4 . '-' . 
            ($f->client_port ?? '') . '-' . 
            ($f->server_port ?? '') . '-' . 
            $f->last_seen
        );
        return $f;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1000));
        $pow   = min($pow, count($units) - 1);
        $bytes /= pow(1000, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}