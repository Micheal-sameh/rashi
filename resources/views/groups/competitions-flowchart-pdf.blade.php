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
            color: #5f7ec7;
            background: #f1f5f9;
            padding: 24px;
        }

        /* ── Header ─────────────────────────────────────── */
        .header-wrap {
            background: #5f7ec7;
            border-radius: 12px;
            padding: 0;
            margin-bottom: 18px;
            overflow: hidden;
        }
        .header-accent {
            height: 5px;
            background: linear-gradient(90deg, #6366f1 0%, #38bdf8 50%, #34d399 100%);
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
            background: #5f7ec7;
            border-radius: 10px;
            border: 1px solid #5f7ec7;
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
            background: #5f7ec7;
            border: 1px solid #5f7ec7;
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
                                <path d="M12 2L2 7l10 5 10-5-10-5z" stroke="#6366f1" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M2 17l10 5 10-5M2 12l10 5 10-5" stroke="#38bdf8" stroke-width="1.8" stroke-linejoin="round"/>
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
                            <div class="header-badge-date">{{ now()->format('Y-m-d  H:i') }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    @php
        $finishedCount  = $competitions->where('status', App\Enums\CompetitionStatus::FINISHED)->count();
        $activeCount    = $competitions->where('status', App\Enums\CompetitionStatus::ACTIVE)->count();
        $cancelledCount = $competitions->where('status', App\Enums\CompetitionStatus::CANCELLED)->count();
        $futureCount    = $competitions->where('status', App\Enums\CompetitionStatus::PENDING)->count();
    @endphp

    {{-- ── Summary Cards ────────────────────────────────────── --}}
    <div class="section-label">Overview</div>
    <table class="summary-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="card-finished">
                <div class="card-value">{{ $finishedCount }}</div>
                <div class="card-label">{{ __('messages.flow_finished') }}</div>
            </td>
            <td class="card-active">
                <div class="card-value">{{ $activeCount }}</div>
                <div class="card-label">{{ __('messages.flow_active') }}</div>
            </td>
            <td class="card-cancelled">
                <div class="card-value">{{ $cancelledCount }}</div>
                <div class="card-label">{{ __('messages.flow_cancelled') }}</div>
            </td>
            <td class="card-future">
                <div class="card-value">{{ $futureCount }}</div>
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
    @php
        $grouped = $competitions->groupBy(fn($c) => optional($c->start_at)->format('Y-m-d'));

        $stages     = $grouped->values();
        $nodeWidth  = 270;
        $nodeHeight = 88;
        $gapX       = 110;
        $gapY       = 20;
        $paddingX   = 30;
        $paddingY   = 50;

        $stageHeights   = $stages->map(fn($stage) => (count($stage) * $nodeHeight) + ((max(count($stage) - 1, 0)) * $gapY));
        $maxStageHeight = max((int) ($stageHeights->max() ?? $nodeHeight), $nodeHeight);

        $chartWidth  = max((int) ((count($stages) * $nodeWidth) + (max(count($stages) - 1, 0) * $gapX) + ($paddingX * 2)), 1000);
        $chartHeight = max((int) ($maxStageHeight + ($paddingY * 2)), 340);

        $statusStyle = function ($status) {
            return match ($status) {
                App\Enums\CompetitionStatus::FINISHED  => ['fill' => '#f0fdf4', 'stroke' => '#22c55e', 'hdr' => '#dcfce7', 'txt' => '#15803d'],
                App\Enums\CompetitionStatus::ACTIVE    => ['fill' => '#fefce8', 'stroke' => '#eab308', 'hdr' => '#fef9c3', 'txt' => '#a16207'],
                App\Enums\CompetitionStatus::CANCELLED => ['fill' => '#fff1f2', 'stroke' => '#f43f5e', 'hdr' => '#ffe4e6', 'txt' => '#be123c'],
                default                                => ['fill' => '#eff6ff', 'stroke' => '#3b82f6', 'hdr' => '#dbeafe', 'txt' => '#1d4ed8'],
            };
        };

        $nodes = [];
        foreach ($stages as $stageIndex => $stage) {
            $stageHeight = $stageHeights[$stageIndex];
            $startY = (int) ($paddingY + (($maxStageHeight - $stageHeight) / 2));
            $x      = (int) ($paddingX + ($stageIndex * ($nodeWidth + $gapX)));

            foreach ($stage->values() as $nodeIndex => $competition) {
                $y      = (int) ($startY + ($nodeIndex * ($nodeHeight + $gapY)));
                $styles = $statusStyle($competition->status);
                $nodes[$stageIndex][] = [
                    'competition' => $competition,
                    'x' => $x, 'y' => $y,
                    'fill'   => $styles['fill'],
                    'stroke' => $styles['stroke'],
                    'hdr'    => $styles['hdr'],
                    'txt'    => $styles['txt'],
                ];
            }
        }
    @endphp

    @if($stages->isEmpty())
        <p style="color:#64748b; padding:20px; text-align:center;">{{ __('messages.no_competitions') }}</p>
    @else
        <div class="chart-section-label">Competition Flow</div>
        <div class="chart-wrap">
            <svg class="flowchart-svg" viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <marker id="arrow" markerWidth="8" markerHeight="6" refX="7" refY="3" orient="auto" markerUnits="strokeWidth">
                        <path d="M0,0 L8,3 L0,6 Z" fill="#94a3b8"/>
                    </marker>
                    <filter id="shadow" x="-5%" y="-5%" width="115%" height="120%">
                        <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#00000018"/>
                    </filter>
                </defs>

                {{-- Background --}}
                <rect x="0" y="0" width="{{ $chartWidth }}" height="{{ $chartHeight }}" rx="10" ry="10" fill="#ffffff"/>

                {{-- Subtle dot grid --}}
                @for($gx = 0; $gx < $chartWidth; $gx += 28)
                    @for($gy = 0; $gy < $chartHeight; $gy += 28)
                        <circle cx="{{ $gx }}" cy="{{ $gy }}" r="1" fill="#e2e8f0"/>
                    @endfor
                @endfor

                {{-- Stage labels --}}
                @for ($si = 0; $si < count($nodes); $si++)
                    @php $sx = $paddingX + ($si * ($nodeWidth + $gapX)); @endphp
                    <rect x="{{ $sx }}" y="14" width="{{ $nodeWidth }}" height="20" rx="5" ry="5" fill="#f1f5f9"/>
                    <text x="{{ $sx + $nodeWidth / 2 }}" y="28" font-size="10" font-weight="bold" fill="#64748b" text-anchor="middle" letter-spacing="0.5">STAGE {{ $si + 1 }}</text>
                @endfor

                {{-- Connector lines --}}
                @for ($si = 1; $si < count($nodes); $si++)
                    @foreach (($nodes[$si - 1] ?? []) as $from)
                        @foreach (($nodes[$si] ?? []) as $to)
                            <path
                                d="M{{ $from['x'] + $nodeWidth }},{{ $from['y'] + ($nodeHeight / 2) }}
                                   C{{ $from['x'] + $nodeWidth + 40 }},{{ $from['y'] + ($nodeHeight / 2) }}
                                    {{ $to['x'] - 40 }},{{ $to['y'] + ($nodeHeight / 2) }}
                                    {{ $to['x'] }},{{ $to['y'] + ($nodeHeight / 2) }}"
                                stroke="#94a3b8"
                                stroke-width="1.5"
                                fill="none"
                                stroke-dasharray="4 3"
                                marker-end="url(#arrow)"
                            />
                        @endforeach
                    @endforeach
                @endfor

                {{-- Nodes --}}
                @foreach ($nodes as $stageNodes)
                    @foreach ($stageNodes as $node)
                        @php
                            $competition = $node['competition'];
                            $name        = $competition->name;
                            if (mb_strlen($name) > 28) { $name = mb_substr($name, 0, 28).'…'; }
                            $dateText    = optional($competition->start_at)->format('Y-m-d').' → '.optional($competition->end_at)->format('Y-m-d');

                            $nameWords = preg_split('/\s+/', trim((string) $name));
                            $lineOne = ''; $lineTwo = '';
                            foreach ($nameWords as $word) {
                                if (mb_strlen(trim($lineOne.' '.$word)) <= 28) { $lineOne = trim($lineOne.' '.$word); }
                                elseif (mb_strlen(trim($lineTwo.' '.$word)) <= 28) { $lineTwo = trim($lineTwo.' '.$word); }
                            }
                            if ($lineOne === '') { $lineOne = $name; }
                            if ($lineTwo === '' && mb_strlen($name) > 28) {
                                $lineOne = mb_substr($name, 0, 28);
                                $lineTwo = mb_substr($name, 28, 26);
                            }
                            if (mb_strlen($lineTwo) > 26) { $lineTwo = mb_substr($lineTwo, 0, 26).'…'; }

                            $statusLabel = match($competition->status) {
                                App\Enums\CompetitionStatus::FINISHED  => 'Finished',
                                App\Enums\CompetitionStatus::ACTIVE    => 'Active',
                                App\Enums\CompetitionStatus::CANCELLED => 'Cancelled',
                                default                                 => 'Upcoming',
                            };
                        @endphp

                        {{-- Card shadow --}}
                        <rect
                            x="{{ $node['x'] + 2 }}"
                            y="{{ $node['y'] + 3 }}"
                            width="{{ $nodeWidth }}"
                            height="{{ $nodeHeight }}"
                            rx="9" ry="9"
                            fill="#00000012"
                        />
                        {{-- Card body --}}
                        <rect
                            x="{{ $node['x'] }}"
                            y="{{ $node['y'] }}"
                            width="{{ $nodeWidth }}"
                            height="{{ $nodeHeight }}"
                            rx="9" ry="9"
                            fill="{{ $node['fill'] }}"
                            stroke="{{ $node['stroke'] }}"
                            stroke-width="1.5"
                        />
                        {{-- Card header band --}}
                        <rect
                            x="{{ $node['x'] }}"
                            y="{{ $node['y'] }}"
                            width="{{ $nodeWidth }}"
                            height="32"
                            rx="9" ry="9"
                            fill="{{ $node['hdr'] }}"
                        />
                        <rect
                            x="{{ $node['x'] }}"
                            y="{{ $node['y'] + 22 }}"
                            width="{{ $nodeWidth }}"
                            height="10"
                            fill="{{ $node['hdr'] }}"
                        />
                        {{-- Divider --}}
                        <line
                            x1="{{ $node['x'] }}"
                            y1="{{ $node['y'] + 32 }}"
                            x2="{{ $node['x'] + $nodeWidth }}"
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
                        >{{ $statusLabel }}</text>

                        {{-- Name lines --}}
                        <text
                            x="{{ $node['x'] + 10 }}"
                            y="{{ $node['y'] + 50 }}"
                            font-size="11"
                            font-weight="bold"
                            fill="#111827"
                        >
                            <tspan x="{{ $node['x'] + 10 }}" dy="0">{{ $lineOne }}</tspan>
                            @if($lineTwo)
                                <tspan x="{{ $node['x'] + 10 }}" dy="13">{{ $lineTwo }}</tspan>
                            @endif
                        </text>

                        {{-- Date --}}
                        <text
                            x="{{ $node['x'] + 10 }}"
                            y="{{ $node['y'] + $nodeHeight - 10 }}"
                            font-size="9"
                            fill="#64748b"
                        >📅 {{ $dateText }}</text>
                    @endforeach
                @endforeach
            </svg>
        </div>
    @endif

    <div class="footer">
        {{ __('messages.groups_competitions') }} &nbsp;·&nbsp; {{ $selectedGroup?->name }} &nbsp;·&nbsp; {{ now()->format('Y-m-d H:i') }}
    </div>

</body>
</html>