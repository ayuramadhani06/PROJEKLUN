@extends('be.master')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4 pb-2 border-bottom" style="border-color: #f8d7da !important;">
        <h4 class="fw-bold" style="color: #8b0000;">Network Management NOC</h4>
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
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m-0 fw-bold">Live Network Activity</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr style="font-size: 12px; color: #666; border-bottom: 2px solid #eee;">
                                <th class="ps-3">Source IP</th>
                                <th>Application / Protocol</th>
                                <th class="text-end pe-3">Last Seen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $act)
                            <tr style="border-bottom: 1px solid #f9f9f9;">
                                <td class="ps-3 text-dark">{{ $act->client_ip }}</td>
                                <td class="text-dark">{{ $act-> protocol_l7 ?: 'Generic Traffic' }}</td>
                                <td class="text-end text-muted small pe-3">
                                    {{ \Carbon\Carbon::parse($act->last_seen)->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center p-5 text-muted">No activity detected.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
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
            
            <!-- <div class="card border-0 shadow-sm p-3" style="background-color: #fdf2f2; border: 1px solid #f8d7da;">
                <h6 class="fw-bold small mb-2" style="color: #8b0000;">System Health</h6>
                <p class="mb-0" style="font-size: 11px; color: #a52a2a;">
                    Monitor: <span class="fw-bold text-success">Online</span><br>
                    DB Status: <span class="fw-bold text-success">Connected</span>
                </p>
            </div> -->
        </div>
    </div>
</div>

<style>
    .card { border-radius: 12px; }
    .table-hover tbody tr:hover {
        background-color: #fafafa !important;
    }
</style>
@endsection