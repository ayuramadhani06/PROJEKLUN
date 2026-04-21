@extends('be.master')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4 pb-2 border-bottom" style="border-color: #f8d7da !important;">
        <h4 class="fw-bold" style="color: #8b0000;">Live Activity</h4>
        <p class="text-muted small">Ringkasan operasional sistem monitoring.</p>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm p-3 border-start border-danger border-4" style="background-color: #fff5f5;">
                <small class="text-uppercase fw-bold" style="font-size: 10px; color: #a52a2a; letter-spacing: 1px;">Active Sessions</small>
                <h3 class="fw-bold m-0" style="color: #8b0000;">{{ number_format($status['active_sessions']) }}</h3>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm p-3 border-start border-danger border-4" style="background-color: #fff5f5;">
                <small class="text-uppercase fw-bold" style="font-size: 10px; color: #a52a2a; letter-spacing: 1px;">Total Endpoints</small>
                <h3 class="fw-bold m-0" style="color: #8b0000;">{{ number_format($status['total_endpoints']) }}</h3>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm p-3 border-start border-danger border-4" style="background-color: #fff5f5;">
                <small class="text-uppercase fw-bold" style="font-size: 10px; color: #a52a2a; letter-spacing: 1px;">Live Flows</small>
                <h3 class="fw-bold m-0 text-danger">{{ number_format($status['active_flows']) }}</h3>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm p-3 border-start border-danger border-4" style="background-color: #fff5f5;">
                <small class="text-uppercase fw-bold" style="font-size: 10px; color: #a52a2a; letter-spacing: 1px;">Last Sync</small>
                <h3 class="fw-bold m-0" style="font-size: 18px; color: #8b0000;">{{ \Carbon\Carbon::parse($status['last_update'])->format('H:i:s') }}</h3>
            </div>
        </div>
    </div>

    <div class="row mt-4">

        <div class="col-12 col-lg-9">

            {{-- Live Network Activity --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m-0 fw-bold">Live Network Activity</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr style="font-size: 12px; color: #666; border-bottom: 2px solid #eee;">
                                <th class="ps-3">Source IP</th>
                                <th>Hostname</th>
                                <th>Application / Protocol</th>
                                <th class="text-end pe-3">Last Seen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $act)
                            <tr style="border-bottom: 1px solid #f9f9f9;">
                                <td class="ps-3 text-dark fw-semibold" style="font-size:13px;">
                                    {{ $act->client_ip }}
                                </td>
                                <td>
                                    @if(!empty($act->client_name))
                                        <span style="font-size:12px; color:#4b5563;">{{ $act->client_name }}</span>
                                    @else
                                        <span style="font-size:12px; color:#d1d5db;">—</span>
                                    @endif
                                </td>
                                <td class="text-dark" style="font-size:13px;">
                                    {{ $act->protocol_l7 ?: 'Generic Traffic' }}
                                </td>
                                <td class="text-end text-muted small pe-3">
                                    {{ \Carbon\Carbon::parse($act->last_seen)->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center p-5 text-muted">No activity detected.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Top Applications Donut --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m-0 fw-bold">Top Applications</h6>
                </div>
                <div class="card-body py-3">
                    {{-- Hapus div pembungkus yang ada width 380px tadi --}}
                    <div class="chart-container" style="position: relative; height:300px; width:100%">
                        <canvas id="appChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm p-3 mb-3 text-white sticky-lg-top" style="background-color: #4a0e0e; top: 20px;">
                <h6 class="fw-bold mb-3 d-flex align-items-center text-white">
                    <i class="fas fa-tools me-2 text-white"></i> NOC Tools
                </h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('sniffer.active') }}" class="btn btn-danger btn-sm text-start" style="background-color: #8b0000; border: none;">
                        <i class="fas fa-search me-2"></i> Open Sniffer
                    </a>
                    <a href="{{ route('profile.index') }}" class="btn btn-outline-light btn-sm text-start border-0 mt-1 shadow-none" style="background-color: rgba(255,255,255,0.1);">
                        <i class="fas fa-user-cog me-2"></i> Profile Settings
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .card { border-radius: 12px; }
    .table-hover tbody tr:hover { background-color: #fafafa !important; }
</style>

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const canvas = document.getElementById('appChart');

    // ✅ CEGAH ERROR NULL
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    const labels = {!! json_encode($topApps->pluck('app')) !!};
    const data   = {!! json_encode($topApps->pluck('total')) !!};

    // ✅ GANTI NAMA (hindari bentrok)
    const totalValue = data.reduce((a, b) => a + b, 0);

    const palette = [
        '#8b0000', '#c0392b', '#e74c3c',
        '#e67e22', '#f39c12', '#d35400'
    ];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: palette,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#374151',
                        usePointStyle: true,
                        padding: 16,
                        font: { size: 12 }
                    }
                },
                datalabels: {
                    color: '#fff',
                    font: { weight: 'bold', size: 11 },
                    formatter: (value) => {
                        const pct = (value / totalValue * 100).toFixed(1);
                        return pct > 3 ? pct + '%' : '';
                    }
                },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const pct = (ctx.parsed / totalValue * 100).toFixed(1);
                            return ` ${ctx.label}: ${ctx.formattedValue} flows (${pct}%)`;
                        }
                    }
                }
            }
        }
    });

});
</script>
@endsection
@endsection