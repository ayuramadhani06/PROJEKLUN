<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function downloadPDF()
    {
        // Mengambil data interface beserta total pemakaiannya
        $stats = DB::table('agg_interface_stats')
                    ->select(
                        'if_name', 
                        'router_ip', 
                        DB::raw('SUM(rx_bytes) as rx_bytes'), 
                        DB::raw('SUM(tx_bytes) as tx_bytes')
                    )
                    ->groupBy('if_name', 'router_ip')
                    ->get();

        // Data harian untuk melihat trend pengeluaran data
        $dailyHistory = DB::table('agg_interface_daily')
                            ->orderBy('time_bucket', 'desc')
                            ->limit(14) // Ambil 2 minggu terakhir
                            ->get();

        $pdf = Pdf::loadView('be.reports', [
            'stats' => $stats,
            'history' => $dailyHistory,
            'date' => date('d F Y H:i')
        ]);

        return $pdf->download('rekap_pengeluaran_data_'.date('Ymd').'.pdf');
    }
}