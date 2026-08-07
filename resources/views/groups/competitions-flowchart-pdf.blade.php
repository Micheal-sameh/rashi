<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.groups_competitions') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #3525cd;
            background: #f1f5f9;
            padding: 24px;
        }

        /* ── Header ─────────────────────────────────────── */
        .header-wrap {
            background: #3525cd;
            border-radius: 12px;
            padding: 0;
            margin-bottom: 18px;
            overflow: hidden;
        }
        .header-accent {
            height: 5px;
            background: linear-gradient(90deg, #3525cd 0%, #4f46e5 50%, #34d399 100%);
        }
        .header-body {
            padding: 16px 20px 18px 20px;
        }
        .header-top {
            width: 100%;
        }
        .header-top td {
            vertical-align: middle;
        }
        .header-icon {
            width: 44px;
            height: 44px;
            background: #3525cd;
            border-radius: 10px;
            border: 1px solid #3525cd;
            text-align: center;
            vertical-align: middle;
            padding-top: 4px;
        }
        .header-texts {
            padding-left: 14px;
        }
        .header-title {
            font-size: 20px;
            font-weight: bold;
            color: #f8fafc;
            letter-spacing: 0.3px;
        }
        .header-subtitle {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 3px;
        }
        .header-badge {
            background: #3525cd;
            border: 1px solid #3525cd;
            border-radius: 6px;
            padding: 5px 10px;
            text-align: right;
        }
        .header-badge-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-badge-date {
            font-size: 11px;
            color: #cbd5e1;
            margin-top: 2px;
        }

        /* ── Summary Cards ───────────────────────────────── */
        .section-label {
            font-size: 9px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 18px;
        }
        .summary-table td {
            width: 25%;
            border-radius: 10px;
            padding: 12px 14px;
            vertical-align: top;
        }
        .card-finished {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
        }
        .card-active {
            background: #fefce8;
            border-left: 4px solid #eab308;
        }
        .card-cancelled {
            background: #fff1f2;
            border-left: 4px solid #f43f5e;
        }
        .card-future {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
        }
        .card-icon {
            font-size: 16px;
            margin-bottom: 6px;
        }
        .card-value {
            font-size: 26px;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 4px;
        }
        .card-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.75;
        }
        .card-finished .card-value,
        .card-finished .card-label { color: #15803d; }
        .card-active .card-value,
        .card-active .card-label { color: #a16207; }
        .card-cancelled .card-value,
        .card-cancelled .card-label { color: #be123c; }
        .card-future .card-value,
        .card-future .card-label { color: #1d4ed8; }

        /* ── Legend ──────────────────────────────────────── */
        .legend-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .legend-table td {
            padding: 0 6px 0 0;
            vertical-align: middle;
        }
        .legend-pill {
            display: inline-block;
            padding: 4px 10px 4px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid;
        }
        .pill-finished { background: #dcfce7; border-color: #16a34a; color: #14532d; }
        .pill-active   { background: #fef9c3; border-color: #ca8a04; color: #713f12; }
        .pill-cancelled{ background: #ffe4e6; border-color: #f43f5e; color: #881337; }
        .pill-future   { background: #dbeafe; border-color: #2563eb; color: #1e3a8a; }
        .dot {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
            margin-right: 5px;
            vertical-align: middle;
        }
        .dot-finished  { background: #22c55e; }
        .dot-active    { background: #eab308; }
        .dot-cancelled { background: #f43f5e; }
        .dot-future    { background: #3b82f6; }

        /* ── Chart ───────────────────────────────────────── */
        .chart-section-label {
            font-size: 9px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .chart-wrap {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            background: #ffffff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .flowchart-svg { width: 100%; height: auto; }

        /* ── Footer ──────────────────────────────────────── */
        .footer {
            margin-top: 16px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    {{-- ── Header ──────────────────────────────────────────── --}}
    <div class="header-wrap">
        <div class="header-accent"></div>
        <div class="header-body">
            <table class="header-top" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width:44px;">
                        <div class="header-icon">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L2 7l10 5 10-5-10-5z" stroke="#3525cd" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M2 17l10 5 10-5M2 12l10 5 10-5" stroke="#4f46e5" stroke-width="1.8" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </td>
                    <td class="header-texts">
                        <div class="header-title">{{ __('messages.groups_competitions') }}</div>
                        <div class="header-subtitle">{{ $selectedGroup?->name }}</div>
                    </td>
                    <td style="text-align:right;">
                        <div class="header-badge">
                            <div class="header-badge-label">Generated</div>
                            <div class="header-badge-date">{{ $generatedAt }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ── Summary Cards ────────────────────────────────────── --}}
    <div class="section-label">Overview</div>
    <table class="summary-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="card-finished">
                <div class="card-value">{{ $counts['finished'] }}</div>
                <div class="card-label">{{ __('messages.flow_finished') }}</div>
            </td>
            <td class="card-active">
                <div class="card-value">{{ $counts['active'] }}</div>
                <div class="card-label">{{ __('messages.flow_active') }}</div>
            </td>
            <td class="card-cancelled">
                <div class="card-value">{{ $counts['cancelled'] }}</div>
                <div class="card-label">{{ __('messages.flow_cancelled') }}</div>
            </td>
            <td class="card-future">
                <div class="card-value">{{ $counts['future'] }}</div>
                <div class="card-label">{{ __('messages.flow_future') }}</div>
            </td>
        </tr>
    </table>

    {{-- ── Legend ───────────────────────────────────────────── --}}
    <table class="legend-table" cellpadding="0" cellspacing="0">
        <tr>
            <td><span class="legend-pill pill-finished"><span class="dot dot-finished"></span>{{ __('messages.flow_finished') }}</span></td>
            <td><span class="legend-pill pill-active"><span class="dot dot-active"></span>{{ __('messages.flow_active') }}</span></td>
            <td><span class="legend-pill pill-cancelled"><span class="dot dot-cancelled"></span>{{ __('messages.flow_cancelled') }}</span></td>
            <td><span class="legend-pill pill-future"><span class="dot dot-future"></span>{{ __('messages.flow_future') }}</span></td>
        </tr>
    </table>

    {{-- ── Flowchart ────────────────────────────────────────── --}}
    @if(empty($chartData['nodes']))
        <p style="color:#64748b; padding:20px; text-align:center;">{{ __('messages.no_competitions') }}</p>
    @else
        <div class="chart-section-label">Competition Flow</div>
        <div class="chart-wrap">
            <svg class="flowchart-svg" viewBox="0 0 {{ $chartData['chartWidth'] }} {{ $chartData['chartHeight'] }}" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <marker id="arrow" markerWidth="8" markerHeight="6" refX="7" refY="3" orient="auto" markerUnits="strokeWidth">
                        <path d="M0,0 L8,3 L0,6 Z" fill="#94a3b8"/>
                    </marker>
                    <filter id="shadow" x="-5%" y="-5%" width="115%" height="120%">
                        <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#00000018"/>
                    </filter>
                </defs>

                {{-- Background --}}
                <rect x="0" y="0" width="{{ $chartData['chartWidth'] }}" height="{{ $chartData['chartHeight'] }}" rx="10" ry="10" fill="#ffffff"/>

                {{-- Subtle dot grid --}}
                @for($gx = 0; $gx < $chartData['chartWidth']; $gx += 28)
                    @for($gy = 0; $gy < $chartData['chartHeight']; $gy += 28)
                        <circle cx="{{ $gx }}" cy="{{ $gy }}" r="1" fill="#e2e8f0"/>
                    @endfor
                @endfor

                {{-- Stage labels (optional) --}}
                {{-- @for ($si = 0; $si < $chartData['stagesCount']; $si++)
                    @php $sx = $chartData['paddingX'] + ($si * ($chartData['nodeWidth'] + $chartData['gapX'])); @endphp
                    <rect x="{{ $sx }}" y="14" width="{{ $chartData['nodeWidth'] }}" height="20" rx="5" ry="5" fill="#f1f5f9"/>
                    <text x="{{ $sx + $chartData['nodeWidth'] / 2 }}" y="28" font-size="10" font-weight="bold" fill="#64748b" text-anchor="middle" letter-spacing="0.5">STAGE {{ $si + 1 }}</text>
                @endfor --}}

                {{-- Connector lines --}}
                @foreach ($chartData['edges'] as $edge)
                    <path
                        d="{{ $edge['d'] }}"
                        stroke="#94a3b8"
                        stroke-width="1.5"
                        fill="none"
                        stroke-dasharray="4 3"
                        marker-end="url(#arrow)"
                    />
                @endforeach

                {{-- Nodes --}}
                @foreach ($chartData['nodes'] as $stageNodes)
                    @foreach ($stageNodes as $node)
                        {{-- Card shadow --}}
                        <rect
                            x="{{ $node['x'] + 2 }}"
                            y="{{ $node['y'] + 3 }}"
                            width="{{ $chartData['nodeWidth'] }}"
                            height="{{ $chartData['nodeHeight'] }}"
                            rx="9" ry="9"
                            fill="#00000012"
                        />
                        {{-- Card body --}}
                        <rect
                            x="{{ $node['x'] }}"
                            y="{{ $node['y'] }}"
                            width="{{ $chartData['nodeWidth'] }}"
                            height="{{ $chartData['nodeHeight'] }}"
                            rx="9" ry="9"
                            fill="{{ $node['fill'] }}"
                            stroke="{{ $node['stroke'] }}"
                            stroke-width="1.5"
                        />
                        {{-- Card header band --}}
                        <rect
                            x="{{ $node['x'] }}"
                            y="{{ $node['y'] }}"
                            width="{{ $chartData['nodeWidth'] }}"
                            height="32"
                            rx="9" ry="9"
                            fill="{{ $node['hdr'] }}"
                        />
                        <rect
                            x="{{ $node['x'] }}"
                            y="{{ $node['y'] + 22 }}"
                            width="{{ $chartData['nodeWidth'] }}"
                            height="10"
                            fill="{{ $node['hdr'] }}"
                        />
                        {{-- Divider --}}
                        <line
                            x1="{{ $node['x'] }}"
                            y1="{{ $node['y'] + 32 }}"
                            x2="{{ $node['x'] + $chartData['nodeWidth'] }}"
                            y2="{{ $node['y'] + 32 }}"
                            stroke="{{ $node['stroke'] }}"
                            stroke-width="1"
                            stroke-opacity="0.4"
                        />
                        {{-- Status dot + label in header --}}
                        <circle cx="{{ $node['x'] + 12 }}" cy="{{ $node['y'] + 16 }}" r="4" fill="{{ $node['stroke'] }}"/>
                        <text
                            x="{{ $node['x'] + 22 }}"
                            y="{{ $node['y'] + 20 }}"
                            font-size="10"
                            font-weight="bold"
                            fill="{{ $node['txt'] }}"
                        >{{ $node['statusLabel'] }}</text>

                        {{-- Name lines --}}
                        <text
                            x="{{ $node['x'] + 10 }}"
                            y="{{ $node['y'] + 50 }}"
                            font-size="11"
                            font-weight="bold"
                            fill="#111827"
                        >
                            <tspan x="{{ $node['x'] + 10 }}" dy="0">{{ $node['lineOne'] }}</tspan>
                            @if($node['lineTwo'])
                                <tspan x="{{ $node['x'] + 10 }}" dy="13">{{ $node['lineTwo'] }}</tspan>
                            @endif
                        </text>

                        {{-- Date --}}
                        <text
                            x="{{ $node['x'] + 10 }}"
                            y="{{ $node['y'] + $chartData['nodeHeight'] - 10 }}"
                            font-size="9"
                            fill="#64748b"
                        >📅 {{ $node['dateText'] }}</text>
                    @endforeach
                @endforeach
            </svg>
        </div>
    @endif

    <div class="footer">
        {{ __('messages.groups_competitions') }} &nbsp;·&nbsp; {{ $selectedGroup?->name }} &nbsp;·&nbsp; {{ $generatedAt }}
    </div>

</body>
</html>