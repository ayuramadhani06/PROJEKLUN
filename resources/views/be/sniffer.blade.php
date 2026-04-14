@extends('be.master')


@php
  $title      = 'Sniffer Network';
  $breadcrumb = 'Sniffer';
@endphp


@section('content')


<style>
  /* ── FONTS ── */
  @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap');


  /* ── RESET / BASE ── */
  body, .main-content { background: #f0f2f5 !important; }
  * { box-sizing: border-box; }


  /* ── VARIABLES ── */
  :root {
    --bg-page   : #f0f2f5;
    --bg-card   : #ffffff;
    --bg-muted  : #f7f8fa;
    --border    : #e4e7ec;
    --border-soft: #eef0f3;
    --text-primary  : #111827;
    --text-secondary: #4b5563;
    --text-muted    : #9ca3af;
    --text-faint    : #d1d5db;
    --accent    : #1d4ed8;
    --accent-bg : #eff6ff;
    --mono      : 'IBM Plex Mono', 'Courier New', monospace;
    --sans      : 'IBM Plex Sans', system-ui, sans-serif;
    --radius    : 12px;
    --radius-sm : 8px;
    --shadow-sm : 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
    --shadow-md : 0 4px 12px rgba(0,0,0,0.08);
  }


  /* ── TYPOGRAPHY BASE ── */
  body { font-family: var(--sans); }


  /* ══════════════════════════════════════════
     TOPBAR
  ══════════════════════════════════════════ */
  .sniffer-topbar {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 14px 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
  }
  .sniffer-topbar-left {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .sniffer-topbar-title {
    font-family: var(--mono);
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .sniffer-topbar-title .dot-live {
    display: inline-block;
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #22c55e;
    animation: live-pulse 2s infinite;
    flex-shrink: 0;
  }
  @keyframes live-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.5); }
    50%       { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
  }
  .sniffer-topbar-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }
  .meta-badge {
    font-family: var(--mono);
    font-size: 0.69rem;
    color: var(--text-muted);
    background: var(--bg-muted);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 4px 10px;
  }
  .meta-badge strong { color: var(--text-secondary); }

  /* Responsive for topbar - using relative units */
  @media (max-width: 48rem) { /* ~768px */
    .sniffer-topbar {
      padding: 0.625rem 0.9375rem; /* 10px 15px */
      gap: 0.5rem; /* 8px */
    }
    .sniffer-topbar-title {
      font-size: 0.75rem; /* 12px */
      gap: 0.375rem; /* 6px */
    }
    .sniffer-topbar-title .dot-live {
      width: 0.375rem; /* 6px */
      height: 0.375rem; /* 6px */
    }
    .meta-badge {
      font-size: 0.65rem; /* 10.4px */
      padding: 0.25rem 0.5rem; /* 4px 8px */
    }
    .interval-pill {
      font-size: 0.68rem; /* 10.88px */
      padding: 0.25rem 0.625rem; /* 4px 10px */
    }
    .btn-hard-refresh {
      font-size: 0.68rem; /* 10.88px */
      padding: 0.25rem 0.625rem; /* 4px 10px */
    }
  }

  @media (max-width: 30rem) { /* ~480px */
    .sniffer-topbar {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.75rem; /* 12px */
    }
    .sniffer-topbar-left {
      width: 100%;
    }
    .sniffer-topbar-right {
      width: 100%;
      justify-content: space-between;
    }
  }

  /* Interval Pill */
  .interval-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--bg-muted);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 5px 12px;
    font-family: var(--mono);
    font-size: 0.72rem;
    color: var(--text-secondary);
  }
  .interval-pill select {
    background: transparent;
    border: none;
    color: var(--text-secondary);
    font-family: var(--mono);
    font-size: 0.72rem;
    cursor: pointer;
    outline: none;
  }


  /* Refresh Btn */
  .btn-hard-refresh {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 5px 12px;
    font-family: var(--sans);
    font-size: 0.72rem;
    font-weight: 500;
    color: var(--text-secondary);
    text-decoration: none;
    transition: background 0.15s, border-color 0.15s;
  }
  .btn-hard-refresh:hover {
    background: var(--bg-muted);
    border-color: #c8cdd6;
    color: var(--text-primary);
  }


  /* ══════════════════════════════════════════
     STAT CARDS
  ══════════════════════════════════════════ */
  .stat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 20px;
  }
  @media (max-width: 767px) {
    .stat-grid { grid-template-columns: 1fr 1fr; }
  }


  .stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px 18px;
    box-shadow: var(--shadow-sm);
    transition: box-shadow 0.2s, border-color 0.2s;
    position: relative;
    overflow: hidden;
  }
  .stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, #e4e7ec 0%, var(--border-soft) 100%);
  }
  .stat-card:hover {
    box-shadow: var(--shadow-md);
    border-color: #c8cdd6;
  }
  .stat-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 10px;
  }
  .stat-card-icon {
    width: 36px; height: 36px;
    border-radius: 9px;
    background: var(--bg-muted);
    border: 1px solid var(--border-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .stat-card-icon i { color: var(--text-muted); font-size: 13px; }
  .stat-number {
    font-family: var(--mono);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.1;
    letter-spacing: -0.5px;
  }
  .stat-label {
    font-family: var(--sans);
    font-size: 0.66rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: var(--text-muted);
    margin-top: 3px;
  }


  /* ══════════════════════════════════════════
     MAIN GRID
  ══════════════════════════════════════════ */
  .main-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
    align-items: start;
  }


  /* ══════════════════════════════════════════
     CARD SHELL (reusable)
  ══════════════════════════════════════════ */
  .card-shell {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }
  .card-shell + .card-shell { margin-top: 14px; }


  .card-head {
    background: var(--bg-muted);
    border-bottom: 1px solid var(--border);
    padding: 11px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
  }
  .card-head-title {
    display: flex;
    align-items: center;
    gap: 7px;
    font-family: var(--sans);
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.3px;
    color: var(--text-secondary);
  }
  .card-head-title i { color: var(--text-muted); font-size: 11px; }


  /* ══════════════════════════════════════════
     FILTER BAR
  ══════════════════════════════════════════ */
  .filter-form-wrap {
    padding: 14px 18px;
  }
  .filter-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
  }
  .filter-search-group {
    flex: 1 1 200px;
    display: flex;
    align-items: center;
    background: var(--bg-muted);
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    transition: border-color 0.15s, box-shadow 0.15s;
  }
  .filter-search-group:focus-within {
    border-color: #93aad6;
    box-shadow: 0 0 0 3px rgba(29,78,216,0.08);
  }
  .filter-search-group .icon-prefix {
    padding: 0 10px;
    color: var(--text-faint);
    font-size: 11px;
    flex-shrink: 0;
  }
  .filter-search-group input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 7px 10px 7px 0;
    font-family: var(--mono);
    font-size: 0.76rem;
    color: var(--text-primary);
    outline: none;
  }
  .filter-search-group input::placeholder { color: var(--text-faint); }


  .filter-select {
    flex: 0 1 160px;
    padding: 7px 10px;
    background: var(--bg-muted);
    border: 1px solid var(--border);
    border-radius: 8px;
    font-family: var(--sans);
    font-size: 0.76rem;
    color: var(--text-secondary);
    outline: none;
    cursor: pointer;
    transition: border-color 0.15s;
  }
  .filter-select:focus {
    border-color: #93aad6;
    box-shadow: 0 0 0 3px rgba(29,78,216,0.08);
  }


  .btn-apply {
    padding: 7px 16px;
    background: var(--text-primary);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-family: var(--sans);
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s;
  }
  .btn-apply:hover { background: #374151; }


  .btn-clear {
    padding: 7px 10px;
    background: var(--bg-muted);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text-muted);
    text-decoration: none;
    font-size: 12px;
    transition: background 0.15s, color 0.15s;
    display: inline-flex;
    align-items: center;
  }
  .btn-clear:hover { background: #fef2f2; border-color: #fca5a5; color: #ef4444; }


  /* ══════════════════════════════════════════
     TABLE HEADER META
  ══════════════════════════════════════════ */
  .table-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }
  .entry-count-badge {
    font-family: var(--mono);
    font-size: 0.68rem;
    background: var(--accent-bg);
    color: var(--accent);
    border: 1px solid #bfdbfe;
    border-radius: 5px;
    padding: 2px 8px;
    font-weight: 600;
  }
  .perpage-row {
    display: flex;
    align-items: center;
    gap: 5px;
  }
  .perpage-label {
    font-size: 0.68rem;
    color: var(--text-muted);
    font-family: var(--sans);
  }
  .perpage-input {
    width: 54px;
    padding: 3px 7px;
    background: var(--bg-muted);
    border: 1px solid var(--border);
    border-radius: 6px;
    font-family: var(--mono);
    font-size: 0.73rem;
    color: var(--text-primary);
    text-align: center;
    outline: none;
  }
  .perpage-input:focus { border-color: #93aad6; }


  /* ══════════════════════════════════════════
     FLOW TABLE
  ══════════════════════════════════════════ */
  .packet-scroll {
    max-height: 480px;
    overflow-y: auto;
  }
  .packet-scroll::-webkit-scrollbar { width: 5px; }
  .packet-scroll::-webkit-scrollbar-track { background: var(--bg-muted); }
  .packet-scroll::-webkit-scrollbar-thumb {
    background: var(--border);
    border-radius: 4px;
  }
  


  #flow-table { width: 100%; border-collapse: collapse; }
  #flow-table thead th {
    position: sticky;
    top: 0;
    z-index: 2;
    background: var(--bg-muted);
    border-bottom: 1px solid var(--border);
    padding: 10px 16px;
    font-family: var(--sans);
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.4px;
    color: var(--text-muted);
    white-space: nowrap;
    text-align: left;
  }


  #flow-table tbody tr {
    border-bottom: 1px solid var(--border-soft);
    cursor: pointer;
    transition: background 0.1s;
  }
  #flow-table tbody tr:last-child { border-bottom: none; }
  #flow-table tbody tr:hover { background: #f5f7ff; }
  #flow-table tbody tr.row-active {
    background: var(--accent-bg);
    border-left: 2px solid var(--accent);
  }


  #flow-table tbody td {
    padding: 9px 16px;
    font-family: var(--mono);
    font-size: 0.75rem;
    color: var(--text-secondary);
    vertical-align: middle;
    border: none;
  }


  /* col widths */
  #flow-table th:nth-child(1),
  #flow-table td:nth-child(1) { min-width: 90px; }
  #flow-table th:nth-child(2),
  #flow-table td:nth-child(2) { min-width: 120px; }
  #flow-table th:nth-child(3),
  #flow-table td:nth-child(3) { min-width: 120px; }
  #flow-table th:nth-child(4),
  #flow-table td:nth-child(4) { min-width: 80px; }
  #flow-table th:nth-child(5),
  #flow-table td:nth-child(5) { min-width: 100px; }
  #flow-table th:nth-child(6),
  #flow-table td:nth-child(6) { min-width: 90px; }
  #flow-table th:nth-child(7),
  #flow-table td:nth-child(7) { max-width: 180px; }


  .td-time-main {
    font-size: 0.76rem;
    color: var(--text-primary);
    font-weight: 500;
  }
  .td-time-ago {
    font-size: 0.63rem;
    color: var(--text-muted);
    display: block;
    margin-top: 1px;
  }
  .td-ip-src { color: var(--text-primary) !important; font-weight: 600; }
  .td-ip-dst { color: var(--text-secondary) !important; }
  .td-bytes  { color: var(--text-primary) !important; font-weight: 600; }
  .td-info   {
    color: var(--text-muted) !important;
    font-size: 0.71rem !important;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }


  /* ── BADGES ── */
  .badge-proto {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 2px 7px;
    border-radius: 4px;
    font-family: var(--mono);
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.4px;
    min-width: 44px;
  }
  .badge-TCP  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
  .badge-UDP  { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
  .badge-ICMP { background: #fefce8; color: #854d0e; border: 1px solid #fde68a; }
  .badge-OTHER{ background: var(--bg-muted); color: var(--text-muted); border: 1px solid var(--border); }


  .badge-app {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-family: var(--mono);
    font-size: 0.62rem;
    font-weight: 600;
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
    max-width: 110px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
  }


  /* ── EMPTY STATE ── */
  .table-empty {
    text-align: center;
    padding: 48px 24px;
    color: var(--text-faint);
  }
  .table-empty i { font-size: 28px; display: block; margin-bottom: 10px; }
  .table-empty p {
    font-family: var(--mono);
    font-size: 0.78rem;
    color: var(--text-muted);
    margin: 0;
  }
  .table-empty small { color: var(--text-faint); font-size: 0.7rem; }


  /* ══════════════════════════════════════════
     TABLE FOOTER / PAGINATION
  ══════════════════════════════════════════ */
  .table-footer {
    background: var(--bg-muted);
    border-top: 1px solid var(--border);
    padding: 10px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
  }
  .footer-meta {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
  }
  .footer-item {
    font-family: var(--mono);
    font-size: 0.67rem;
    color: var(--text-muted);
  }
  .footer-item strong { color: var(--text-secondary); }


  /* Pagination overrides */
  .pagination { margin: 0; gap: 3px; }
  .pagination .page-link {
    background: var(--bg-card);
    border-color: var(--border);
    color: var(--text-secondary);
    font-family: var(--mono);
    font-size: 0.7rem;
    padding: 4px 10px;
    border-radius: 6px !important;
    transition: all 0.15s;
  }
  .pagination .page-link:hover {
    background: var(--bg-muted);
    border-color: #c8cdd6;
    color: var(--text-primary);
  }
  .pagination .page-item.active .page-link {
    background: var(--text-primary);
    border-color: var(--text-primary);
    color: #fff;
  }
  .pagination .page-item.disabled .page-link {
    background: var(--bg-muted);
    color: var(--text-faint);
    border-color: var(--border-soft);
  }


  /* ══════════════════════════════════════════
     DETAIL PANEL
  ══════════════════════════════════════════ */
  .detail-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 36px 24px;
    color: var(--text-faint);
    gap: 8px;
  }
  .detail-empty-state i { font-size: 20px; }
  .detail-empty-state span {
    font-family: var(--mono);
    font-size: 0.73rem;
    color: var(--text-muted);
  }


  .detail-content { padding: 16px 18px; }
  .detail-section-title {
    font-family: var(--sans);
    font-size: 0.59rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.3px;
    color: var(--text-muted);
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid var(--border-soft);
  }
  .detail-kv-table { width: 100%; border-collapse: collapse; }
  .detail-kv-table tr + tr td { padding-top: 5px; }
  .detail-kv-table td:first-child {
    font-family: var(--sans);
    font-size: 0.7rem;
    color: var(--text-muted);
    padding-right: 14px;
    white-space: nowrap;
    width: 1%;
  }
  .detail-kv-table td:last-child {
    font-family: var(--mono);
    font-size: 0.72rem;
    color: var(--text-secondary);
  }

  /* ── TABS ── */
  .sniffer-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
    border-bottom: 1px solid var(--border);
    padding-bottom: 0;
  }
  .tab-item {
    padding: 10px 20px;
    font-family: var(--sans);
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--text-muted);
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none !important;
  }
  .tab-item:hover {
    color: var(--text-primary);
    background: var(--bg-muted);
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }
  .tab-item.active {
    color: var(--accent);
    border-bottom-color: var(--accent);
  }
  .tab-item i { font-size: 12px; }

  /* Tab Content Toggle */
  .tab-content { display: none; }
  .tab-content.active { display: block; }


  /* ══════════════════════════════════════════
     SIDEBAR CARDS — (disabled)
  ══════════════════════════════════════════ */
</style>


{{-- ══════════════════════════════════════════
     TOPBAR
══════════════════════════════════════════ --}}
<div class="sniffer-topbar">


  {{-- Left: title --}}
  <div class="sniffer-topbar-left">
    <div class="sniffer-topbar-title">
      <span class="dot-live" id="live-dot"></span>
      <span>Sniffer Network</span>
    </div>
  </div>


  {{-- Right: controls --}}
  <div class="sniffer-topbar-right">
    <span class="meta-badge">
      Updated: <strong id="last-update">{{ now()->format('H:i:s') }}</strong>
    </span>


    <div class="interval-pill">
      <i class="fas fa-sync-alt" style="font-size:10px;color:#9ca3af;"></i>
      <span>Refresh</span>
      <select id="refresh-select">
        <option value="10000">10 s</option>
        <option value="30000">30 s</option>
        <option value="60000">1 m</option>
        <option value="0">Off</option>
      </select>
    </div>


    <a href="{{ route('sniffer.index') }}" class="btn-hard-refresh">
      <i class="fas fa-redo-alt" style="font-size:10px;"></i>
      Hard Refresh
    </a>
  </div>
</div>


{{-- ══════════════════════════════════════════
     STAT CARDS
══════════════════════════════════════════ --}}
<div class="stat-grid">


  {{-- Total Data --}}
  <div class="stat-card">
    <div class="stat-card-top">
      <div>
        @php
          $tb = $totalBytes;
          if     ($tb >= 1000000000) $tbHuman = round($tb/1000000000, 2) . ' GB';
          elseif ($tb >= 1000000)    $tbHuman = round($tb/1000000, 2)    . ' MB';
          elseif ($tb >= 1000)       $tbHuman = round($tb/1000, 2)       . ' KB';
          else                       $tbHuman = $tb . ' B';
        @endphp
        <div class="stat-number" id="stat-total-bytes">{{ $tbHuman }}</div>
        <div class="stat-label">Total Data</div>
        @if($tab === 'history' && $filteredStats)
          @php $fb = $filteredStats['total_bytes']; @endphp
          <small style="color: var(--accent); font-size: 0.65rem;">
            Filtered:
            @if($fb >= 1000000000) {{ round($fb/1000000000, 2) }} GB
            @elseif($fb >= 1000000) {{ round($fb/1000000, 2) }} MB
            @elseif($fb >= 1000) {{ round($fb/1000, 2) }} KB
            @else {{ $fb }} B
            @endif
          </small>
        @endif
      </div>
      <div class="stat-card-icon"><i class="fas fa-database"></i></div>
    </div>
  </div>


  {{-- Unique Sources --}}
  <div class="stat-card">
    <div class="stat-card-top">
      <div>
        <div class="stat-number" id="stat-unique-src">{{ number_format($uniqueSrc) }}</div>
        <div class="stat-label">Unique Sources</div>
        @if($tab === 'history' && $filteredStats)
          <small style="color: var(--accent); font-size: 0.65rem;">
            Filtered: {{ number_format($filteredStats['unique_src']) }}
          </small>
        @endif
      </div>
      <div class="stat-card-icon"><i class="fas fa-desktop"></i></div>
    </div>
  </div>


  {{-- Unique Destinations --}}
  <div class="stat-card">
    <div class="stat-card-top">
      <div>
        <div class="stat-number" id="stat-unique-dst">{{ number_format($uniqueDst) }}</div>
        <div class="stat-label">Unique Destinations</div>
        @if($tab === 'history' && $filteredStats)
          <small style="color: var(--accent); font-size: 0.65rem;">
            Filtered: {{ number_format($filteredStats['unique_dst']) }}
          </small>
        @endif
      </div>
      <div class="stat-card-icon"><i class="fas fa-server"></i></div>
    </div>
  </div>


</div>

{{-- Hidden inputs for stats per tab --}}
<input type="hidden" id="history-total-bytes" value="{{ $totalBytes }}">
<input type="hidden" id="history-unique-src" value="{{ $uniqueSrc }}">
<input type="hidden" id="history-unique-dst" value="{{ $uniqueDst }}">
<input type="hidden" id="active-total-bytes" value="{{ $totalBytes }}"> {{-- Active uses same totalBytes --}}
<input type="hidden" id="active-unique-src" value="{{ DB::table('flows_active')->distinct('client_ip')->count('client_ip') }}">
<input type="hidden" id="active-unique-dst" value="{{ DB::table('flows_active')->distinct('server_ip')->count('server_ip') }}">

{{-- ── Navigasi Tab ── --}}
<div class="sniffer-tabs">
  <div class="tab-item {{ $tab === 'active' ? 'active' : '' }}" onclick="switchTab('active', this)">
    <i class="fas fa-bolt"></i> Active Flows
  </div>
  <div class="tab-item {{ $tab === 'history' ? 'active' : '' }}" onclick="switchTab('history', this)">
    <i class="fas fa-history"></i> History Log
  </div>
</div>

<div class="main-grid">
  {{-- ── Tab Active ── --}}
  <div id="tab-active" class="tab-content {{ $tab === 'active' ? 'active' : '' }}">

    {{-- Filter --}}
    <div class="card-shell" style="margin-bottom:14px;">
      <div class="card-head">
        <div class="card-head-title">
          <i class="fas fa-filter"></i> Filter
        </div>
      </div>
      <div class="filter-form-wrap">
        <div class="filter-row">
          {{-- Search --}}
          <div class="filter-search-group">
            <span class="icon-prefix"><i class="fas fa-search"></i></span>
            <input type="text" id="input-search"
                   placeholder="IP, App, Info..."
                   value="{{ $search ?? '' }}">
          </div>

          {{-- Protocol --}}
          <select id="input-protocol" class="filter-select">
            <option value="">All Protocols</option>
            @foreach($protocolList as $p)
              <option value="{{ $p }}" {{ ($protocol ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
            @endforeach
          </select>

          {{-- Application --}}
          <select id="input-application" class="filter-select">
            <option value="">All Applications</option>
            @foreach($applicationList as $a)
              <option value="{{ $a }}" {{ ($app ?? '') === $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
          </select>

          {{-- Actions --}}
          <button type="button" class="btn-apply" onclick="resetAndFetch()">Apply</button>
          <a href="{{ route('sniffer.index') }}" class="btn-clear">
            <i class="fas fa-times"></i>
          </a>
        </div>
      </div>
    </div>

    {{-- Flow Table --}}
    <div class="card-shell">
      {{-- Table Head --}}
      <div class="card-head">
        <div class="card-head-title">
          <i class="fas fa-list-ul"></i> Active Flow Log
          <span class="entry-count-badge" id="entry-count">0 entries</span>
        </div>
        <div class="table-meta">
          <div class="perpage-row">
            <span class="perpage-label">Rows</span>
            <input type="number" id="input-perpage" value="{{ $perPage }}"
                   min="5" max="200" class="perpage-input"
                   onchange="saveActivePerpage(); fetchLive();">
          </div>
        </div>
      </div>

      {{-- Scrollable Table --}}
      <div>
        <table id="flow-table">
          <thead>
            <tr>
              <th>Time</th>
              <th>Src IP</th>
              <th>Dst IP</th>
              <th>Protocol</th>
              <th>Application</th>
              <th>Bytes</th>
              <th>Info</th>
            </tr>
          </thead>
          <tbody id="flow-tbody-active">
            <tr class="table-empty">
              <td colspan="7">
                <i class="fas fa-satellite-dish"></i>
                <p>Loading active flows...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      {{-- Footer: stats --}}
      <div class="table-footer">
        <div class="footer-meta">
          <span class="footer-item">
            Total: <strong id="active-total">0 flows</strong>
          </span>

          <span class="footer-item">
            Page: <strong>1 / 1</strong>
          </span>

          <span class="footer-item">
            Showing:
            <strong id="active-showing">0 - 0</strong>
          </span>
        </div>
      </div>
    </div>


  </div>

  {{-- ── Tab History ── --}}
  <div id="tab-history" class="tab-content {{ $tab === 'history' ? 'active' : '' }}">

    {{-- Filter History --}}
    <div class="card-shell" style="margin-bottom:14px;">
      <div class="card-head">
        <div class="card-head-title">
          <i class="fas fa-filter"></i> Filter
        </div>
      </div>
      <div class="filter-form-wrap">
        <form method="GET" action="{{ route('sniffer.index') }}" class="filter-row">
          <input type="hidden" name="tab" value="history">
          <input type="hidden" name="start_date" value="{{ $start_date ?? '' }}">
          <input type="hidden" name="end_date" value="{{ $end_date ?? '' }}">
          
          {{-- Search --}}
          <div class="filter-search-group">
            <span class="icon-prefix"><i class="fas fa-search"></i></span>
            <input type="text" name="search" id="input-history-search"
                   placeholder="IP, App, Info..."
                   value="{{ $search ?? '' }}">
          </div>

          {{-- Protocol --}}
          <select name="protocol" id="input-history-protocol" class="filter-select">
            <option value="">All Protocols</option>
            @foreach($protocolList as $p)
              <option value="{{ $p }}" {{ ($protocol ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
            @endforeach
          </select>

          {{-- Application --}}
          <select name="application" id="input-history-application" class="filter-select">
            <option value="">All Applications</option>
            @foreach($applicationList as $a)
              <option value="{{ $a }}" {{ ($app ?? '') === $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
          </select>

          {{-- Start Date --}}
          <input type="date" name="start_date" id="start_date" class="filter-select" value="{{ $start_date ?? '' }}">

          {{-- End Date --}}
          <input type="date" name="end_date" id="end_date" class="filter-select" value="{{ $end_date ?? '' }}">

          {{-- Actions --}}
          <button type="submit" class="btn-apply">Apply</button>
          <a href="{{ route('sniffer.index') }}" class="btn-clear">
            <i class="fas fa-times"></i>
          </a>
        </form>
      </div>
    </div>

    {{-- History Table --}}
    <div class="card-shell">
      {{-- Table Head --}}
      <div class="card-head">
        <div class="card-head-title">
          <i class="fas fa-archive"></i> Historical Flows (flows_history)
          <span class="entry-count-badge" id="history-entry-count">{{ $flows->total() }} entries</span>
        </div>
        <div class="table-meta">
          <form method="GET" action="{{ route('sniffer.index') }}" class="perpage-row">
            <input type="hidden" name="search"      value="{{ $search ?? '' }}">
            <input type="hidden" name="protocol"    value="{{ $protocol ?? '' }}">
            <input type="hidden" name="application" value="{{ $app ?? '' }}">
            <input type="hidden" name="start_date"  value="{{ $start_date ?? '' }}">
            <input type="hidden" name="end_date"    value="{{ $end_date ?? '' }}">
            <input type="hidden" name="tab"         value="history">
            <span class="perpage-label">Rows</span>
            <input type="number" name="per_page" value="{{ $perPage }}"
                   min="5" max="100" id="input-history-perpage"
                   onchange="saveHistoryPerpage(); this.form.submit();" class="perpage-input">
          </form>
        </div>
      </div>

      {{-- Scrollable Table --}}
      <div>
        <table id="flow-table">
          <thead>
            <tr>
              <th>Last Seen</th>
              <th>Src IP</th>
              <th>Dst IP</th>
              <th>Protocol</th>
              <th>Application</th>
              <th>Bytes</th>
              <th>Info</th>
            </tr>
          </thead>
          <tbody id="flow-tbody-history">
            @forelse($flows as $flow)
            <tr onclick="showHistoryDetail({{ json_encode($flow) }}, this)">
              <td>
                <span class="td-time-main">{{ \Carbon\Carbon::parse($flow->seen_last)->format('H:i:s') }}</span>
                <span class="td-time-ago">{{ \Carbon\Carbon::parse($flow->seen_last)->diffForHumans() }}</span>
              </td>
              <td class="td-ip-src">{{ $flow->src_ip }}</td>
              <td class="td-ip-dst">{{ $flow->dest_ip }}</td>
              <td>
                @php $pUp = strtoupper($flow->protocol ?? ''); @endphp
                <span class="badge-proto badge-{{ in_array($pUp,['TCP','UDP','ICMP']) ? $pUp : 'OTHER' }}">
                  {{ $flow->protocol ?: '-' }}
                </span>
              </td>
              <td>
                @if(!empty($flow->application) && $flow->application !== 'Unknown')
                  <span class="badge-app" title="{{ $flow->application }}">{{ $flow->application }}</span>
                @else
                  <span style="color:var(--text-faint);">—</span>
                @endif
              </td>
              <td class="td-bytes">
                @php $b = $flow->total_bytes ?? 0; @endphp
                @if($b >= 1000000000) {{ round($b/1000000000, 2) }} GB
                @elseif($b >= 1000000) {{ round($b/1000000, 2) }} MB
                @elseif($b >= 1000) {{ round($b/1000, 2) }} KB
                @else {{ $b }} B
                @endif
              </td>
              <td class="td-info">{{ $flow->info ?: '—' }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="7">
                <div class="table-empty">
                  <i class="fas fa-satellite-dish" style="opacity:0.3;"></i>
                  <p>No historical data found.</p>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Footer: stats + pagination --}}
      <div class="table-footer">
        <div class="footer-meta">
          <span class="footer-item">
            Total: <strong>{{ number_format($flows->total()) }} flows</strong>
          </span>
          <span class="footer-item">
            Page: <strong>{{ $flows->currentPage() }} / {{ $flows->lastPage() }}</strong>
          </span>
        </div>
        {{ $flows->appends(request()->query())->links('pagination::bootstrap-4') }}
      </div>
    </div>

  </div>

</div>

<!-- Modal Flow Detail -->
<div class="modal fade" id="flowDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      {{-- HEADER --}}
      <div class="modal-header">
        <h6 class="modal-title" id="modal-title">Flow Detail</h6>

        {{-- ❗ FIX CLOSE BUTTON --}}
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      {{-- BODY --}}
      <div class="modal-body" id="modal-body">
        <div class="text-center text-muted">
          Click a row to see detail
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
          Close
        </button>
      </div>

    </div>
  </div>
</div>
{{-- ── End Main Grid ── --}}


{{-- ══════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════ --}}
<script>
let refreshTimer  = null;
let activeRow     = null;
let existingKeys = new Set();

// Load saved perpage values
const savedActivePerpage = localStorage.getItem('sniffer_active_perpage');
const savedHistoryPerpage = localStorage.getItem('sniffer_history_perpage');
if (savedActivePerpage) document.getElementById('input-perpage').value = savedActivePerpage;
if (savedHistoryPerpage) document.getElementById('input-history-perpage').value = savedHistoryPerpage;

// Function to update stats based on tab
function updateStats(tab) {
  const totalBytesEl = document.getElementById('stat-total-bytes');
  const uniqueSrcEl = document.getElementById('stat-unique-src');
  const uniqueDstEl = document.getElementById('stat-unique-dst');

  if (tab === 'history') {
    totalBytesEl.textContent = renderBytes(document.getElementById('history-total-bytes').value);
    uniqueSrcEl.textContent = document.getElementById('history-unique-src').value;
    uniqueDstEl.textContent = document.getElementById('history-unique-dst').value;
  } else {
    // For active, use the values from hidden inputs (initially set)
    totalBytesEl.textContent = renderBytes(document.getElementById('active-total-bytes').value);
    uniqueSrcEl.textContent = document.getElementById('active-unique-src').value;
    uniqueDstEl.textContent = document.getElementById('active-unique-dst').value;
  }
}


/* ── Formatters ── */
function renderBytes(b) {
  b = b || 0;
  if (b >= 1e9) return (b/1e9).toFixed(2)  + ' GB';
  if (b >= 1e6) return (b/1e6).toFixed(2)  + ' MB';
  if (b >= 1e3) return (b/1e3).toFixed(2)  + ' KB';
  return b + ' B';
}


function renderProtoBadge(proto) {
  const p = (proto||'').toUpperCase();
  const cls = ['TCP','UDP','ICMP'].includes(p) ? 'badge-'+p : 'badge-OTHER';
  return `<span class="badge-proto ${cls}">${proto||'-'}</span>`;
}


function renderAppBadge(app) {
  if (!app || app === 'Unknown') return '<span style="color:var(--text-faint);">—</span>';
  return `<span class="badge-app" title="${app}">${app}</span>`;
}

// Save perpage functions
function saveActivePerpage() {
  localStorage.setItem('sniffer_active_perpage', document.getElementById('input-perpage').value);
}

function saveHistoryPerpage() {
  localStorage.setItem('sniffer_history_perpage', document.getElementById('input-history-perpage').value);
}


/* ── Live Fetch (Active Tab) ── */
function fetchLive() {
  const params = new URLSearchParams({
    search:      document.getElementById('input-search')?.value      || '',
    protocol:    document.getElementById('input-protocol')?.value    || '',
    application: document.getElementById('input-application')?.value || '',
    per_page:    document.getElementById('input-perpage')?.value     || 25,
  });

  fetch(`{{ route('sniffer.api') }}?${params}`)
    .then(r => r.json())
    .then(data => {
      if (!data.success) return;

      // Update meta
      document.getElementById('last-update').textContent      = data.server_time;
      document.getElementById('stat-total-bytes').textContent = data.stats.total_bytes;
      document.getElementById('stat-unique-src').textContent  = data.stats.unique_src;
      document.getElementById('stat-unique-dst').textContent  = data.stats.unique_dst;

      const tbody = document.getElementById('flow-tbody-active');

      if (!data.flows || data.flows.length === 0) {
        tbody.innerHTML = `
          <tr class="table-empty">
            <td colspan="7">
              <i class="fas fa-satellite-dish"></i>
              <p>No active flows</p>
            </td>
          </tr>
        `;

        document.getElementById('entry-count').textContent = '0 entries';
        document.getElementById('active-total').textContent = '0 flows';
        document.getElementById('active-showing').textContent = '0 - 0';

        existingKeys.clear();
        return;
      }

      let newRows = [];

      data.flows.forEach(f => {
        // Unique key (biar ga double)
        const key = f.src_ip + '-' + f.dest_ip + '-' + f.seen_last;

        if (existingKeys.has(key)) return;
        existingKeys.add(key);

        const row = document.createElement('tr');
        row.innerHTML = `
          <td>
            <span class="td-time-main">${(f.seen_last||'').substring(11,19)}</span>
            <span class="td-time-ago">${f.time_ago||''}</span>
          </td>
          <td class="td-ip-src">${f.src_ip}</td>
          <td class="td-ip-dst">${f.dest_ip}</td>
          <td>${renderProtoBadge(f.protocol)}</td>
          <td>${renderAppBadge(f.application)}</td>
          <td class="td-bytes">${renderBytes(f.total_bytes)}</td>
          <td class="td-info">${f.info||'—'}</td>
        `;

        row.onclick = () => showDetail(f, row);

        newRows.push(row);
      });

      // 🔥 prepend (biar newest di atas)
      newRows.reverse().forEach(r => tbody.prepend(r));

      // 🔥 limit rows (rolling window)
      const maxRows = parseInt(document.getElementById('input-perpage').value) || 25;
      while (tbody.rows.length > maxRows) {
        tbody.deleteRow(-1);
      }

      // Update counter
      const countEl = document.getElementById('entry-count');
      if (countEl) countEl.textContent = tbody.rows.length + ' entries';

      const activeTotalEl = document.getElementById('active-total');
      if (activeTotalEl) {
        activeTotalEl.textContent = (data.total ?? 0) + ' flows';
      }
    })
    .catch(err => console.warn('Live fetch error:', err));
}



/* ── Refresh control ── */
function startRefresh(interval) {
  if (refreshTimer) clearInterval(refreshTimer);
  const dot = document.getElementById('live-dot');
  const statusEl = document.getElementById('refresh-status');
  
  if (interval == 0) {
    dot.style.animation = 'none';
    dot.style.background = '#d1d5db';
    if (statusEl) statusEl.textContent = 'Paused';
    return;
  }
  
  dot.style.animation  = 'live-pulse 2s infinite';
  dot.style.background = '#22c55e';
  if (statusEl) statusEl.textContent = 'Active (' + (interval/1000) + 's)';
  refreshTimer = setInterval(fetchLive, interval);
  
  // Initial fetch
  fetchLive();
}

document.getElementById('refresh-select').addEventListener('change', function () {
  const interval = parseInt(this.value);
  // Only allow refresh control in Active tab
  if (document.getElementById('tab-active').classList.contains('active')) {
    startRefresh(interval);
  }
});

// Initial load
updateStats('{{ $tab }}');
startRefresh(10000);


/* ── Detail panel (Active Tab) ── */
function showDetail(flow, rowEl) {
  if (activeRow) activeRow.classList.remove('row-active');
  if (rowEl) { rowEl.classList.add('row-active'); activeRow = rowEl; }

  const bh   = renderBytes(flow.total_bytes);
  const pUp  = (flow.protocol||'').toUpperCase();
  const pCls = ['TCP','UDP','ICMP'].includes(pUp) ? 'badge-'+pUp : 'badge-OTHER';
  const appH = (flow.application && flow.application !== 'Unknown')
    ? `<span class="badge-app">${flow.application}</span>` : '—';

  function kv(label, val) {
    return `<tr><td>${label}</td><td>${val||'—'}</td></tr>`;
  }

  document.getElementById('modal-title').textContent =
    (flow.src_ip || '') + ' → ' + (flow.dest_ip || '');
//ini modal yang active 
  document.getElementById('modal-body').innerHTML = `
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">

      <div>
        <h6>Network</h6>
        <table class="table table-sm">
          ${kv('Source IP', flow.src_ip)}
          ${kv('Source Port', flow.client_port)}
          ${kv('Destination IP', flow.dest_ip)}
          ${kv('Dest Port', flow.server_port)}
          ${kv('Protocol', `<span class="badge-proto ${pCls}">${flow.protocol||'-'}</span>`)}
          ${kv('Application', appH)}
          ${kv('VLAN', flow.vlan)}
        </table>
      </div>

      <div>
        <h6>Statistics</h6>
        <table class="table table-sm">
          ${kv('Total Bytes', bh)}
          ${kv('Packets', flow.packets)}
          ${kv('Last Seen', flow.seen_last)}
          ${kv('Info', flow.info)}
        </table>
      </div>

    </div>
  `;

  openModal();
}

/* ── Detail panel (History Tab) ── */
function showHistoryDetail(flow, rowEl) {
  const rows = document.querySelectorAll('#tab-history tbody tr');
  rows.forEach(r => r.classList.remove('row-active'));
  if (rowEl) rowEl.classList.add('row-active');

  const bh   = renderBytes(flow.total_bytes);
  const pUp  = (flow.protocol||'').toUpperCase();
  const pCls = ['TCP','UDP','ICMP'].includes(pUp) ? 'badge-'+pUp : 'badge-OTHER';
  const appH = (flow.application && flow.application !== 'Unknown')
    ? `<span class="badge-app">${flow.application}</span>` : '—';

  function kv(label, val) {
    return `<tr><td>${label}</td><td>${val||'—'}</td></tr>`;
  }

  document.getElementById('modal-title').textContent =
    (flow.src_ip || '') + ' → ' + (flow.dest_ip || '');
//ini modal yang history
  document.getElementById('modal-body').innerHTML = `
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">

      <div>
        <h6>Network</h6>
        <table class="table table-sm">
          ${kv('Source IP', flow.src_ip)}
          ${kv('Source Port', flow.client_port)}
          ${kv('Destination IP', flow.dest_ip)}
          ${kv('Dest Port', flow.server_port)}
          ${kv('Protocol', `<span class="badge-proto ${pCls}">${flow.protocol||'-'}</span>`)}
          ${kv('Application', appH)}
          ${kv('VLAN', flow.vlan)}
        </table>
      </div>

      <div>
        <h6>Statistics</h6>
        <table class="table table-sm">
          ${kv('Total Bytes', bh)}
          ${kv('Packets', flow.packets)}
          ${kv('Duration', (flow.duration || 0) + ' sec')}
          ${kv('First Seen', flow.first_seen)}
          ${kv('Last Seen', flow.seen_last)}
          ${kv('Info', flow.info)}
        </table>
      </div>

    </div>
  `;

  openModal();
}

function switchTab(tabName, element) {
    // 1. aktifin tombol tab
    document.querySelectorAll('.tab-item').forEach(el => el.classList.remove('active'));
    element.classList.add('active');

    // 2. tampilkan isi tab
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tabName).classList.add('active');

    // 3. kontrol auto refresh
    if (tabName === 'active') {
        const interval = parseInt(document.getElementById('refresh-select').value);
        if (interval > 0) startRefresh(interval);
    } else {
        if (refreshTimer) clearInterval(refreshTimer);
    }

    // 4. update stats sesuai tab
    updateStats(tabName);

    // 5. update URL TANPA reload (optional tapi bagus)
    history.pushState(null, '', '?tab=' + tabName);
}

function openModal() {
  const modal = new bootstrap.Modal(document.getElementById('flowDetailModal'));
  modal.show();
}

function resetAndFetch() {
  existingKeys.clear();
  document.getElementById('flow-tbody-active').innerHTML = '';
  fetchLive();
}
</script>


@endsection