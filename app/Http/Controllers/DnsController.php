<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpseclib3\Net\SSH2; // WAJIB ADA INI

class DnsController extends Controller
{
    public function index()
    {
        return view('be.dns');  
    }

    public function getDnsApi(Request $request)
    {
        // 1. Settingan Remote Ubuntu (Sesuaikan dengan data aslimu!)
        $host = '10.10.15.179'; 
        $user = 'ayu';
        $pass = '123'; // Isi password user ayu di Ubuntu

        try {
            $ssh = new SSH2($host);
            // Tambahkan timeout biar gak kelamaan nunggu
            $ssh->setTimeout(10); 
            
            if (!$ssh->login($user, $pass)) {
                // Biar kita tahu detail errornya
                return response()->json([
                    'error' => 'Login SSH Gagal!',
                    'detail' => 'User: '.$user.', Host: '.$host.', Password sudah diisi? '.(empty($pass) ? 'BELUM' : 'SUDAH')
                ], 401);
            }

            // 3. Command Tshark (Jalan di Ubuntu, diperintah dari Laptop)
            $command = "timeout 5s sudo /usr/bin/tshark -l -i any -f 'udp port 53 and src net 10.10.15.0/24' -T fields -e ip.src -e dns.qry.name -E separator=, 2>&1";
            
            $output = $ssh->exec($command);
            $logs = [];

            if ($output) {
                // Ambil data leases dari Database (Database di Ubuntu kan? Pastikan .env sudah benar)
                $leases = DB::table('dhcp_leases')->get()->keyBy('ip');
                
                $lines = explode("\n", trim($output));
                foreach ($lines as $line) {
                    if (empty($line) || str_contains($line, 'Capturing on')) continue;
                    
                    $data = explode(',', $line);
                    if (count($data) >= 2) {
                        $ip = trim($data[0]);
                        $domain = trim($data[1]);
                        if (strlen($domain) < 4) continue;

                        $device = $leases->get($ip);
                        $logs[] = [
                            'log_time'    => now()->format('H:i:s'),
                            'time_ago'    => 'Live',
                            'hostname'    => $device ? $device->hostname : 'Guest (' . $ip . ')',
                            'src_ip'      => $ip,
                            'mac'         => $device ? $device->mac : 'N/A',
                            'base_domain' => $domain,
                            'status'      => 'ONLINE',
                        ];
                    }
                }
            }

            // 4. Return Data ke Dashboard
            return response()->json([
                'logs' => array_reverse(array_values(collect($logs)->unique(fn($i)=>$i['src_ip'].$i['base_domain'])->toArray())),
                'stats' => [
                    'total_query' => count($logs),
                    'active_ip'   => count(array_unique(array_column($logs, 'src_ip'))),
                    'top_domain'  => isset($logs[0]) ? $logs[0]['base_domain'] : 'Waiting Traffic...'
                ],
                'debug_raw' => $output, // <--- TAMBAHKAN INI
                'current_page' => 1, 'total_page' => 1, 'total_filtered' => count($logs)
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal Remote SSH: ' . $e->getMessage()], 500);
        }
    }
}