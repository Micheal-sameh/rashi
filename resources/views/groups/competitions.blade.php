@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <x-page-header icon="fa-project-diagram" :title="__('messages.groups_competitions')" />

        <div class="card border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('groups.competitions') }}" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label for="group_id" class="form-label">{{ __('messages.select_group_for_flowchart') }}</label>
                        <select id="group_id" name="group_id" class="form-select" required>
                            <option value="">{{ __('messages.choose_a_group') }}</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" @selected(optional($selectedGroup)->id === $group->id)>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-project-diagram me-1"></i>{{ __('messages.show_flowchart') }}
                        </button>
                        @if($selectedGroup)
                            <a href="{{ route('groups.competitions.exportPdf', ['group_id' => $selectedGroup->id]) }}"
                                class="btn btn-danger w-100">
                                <i class="fas fa-file-pdf me-1"></i>{{ __('messages.export_pdf') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if($selectedGroup)
            @php
                $finishedCount = $competitions->where('status', App\Enums\CompetitionStatus::FINISHED)->count();
                $activeCount = $competitions->where('status', App\Enums\CompetitionStatus::ACTIVE)->count();
                $cancelledCount = $competitions->where('status', App\Enums\CompetitionStatus::CANCELLED)->count();
                $futureCount = $competitions->where('status', App\Enums\CompetitionStatus::PENDING)->count();
            @endphp
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="rs-stat-card tone-success">
                        <div class="rs-stat-top">
                            <span class="rs-label-md">{{ __('messages.flow_finished') }}</span>
                            <div class="rs-stat-icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                        <div class="rs-stat-value">{{ $finishedCount }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="rs-stat-card tone-warning">
                        <div class="rs-stat-top">
                            <span class="rs-label-md">{{ __('messages.flow_active') }}</span>
                            <div class="rs-stat-icon"><i class="fas fa-play-circle"></i></div>
                        </div>
                        <div class="rs-stat-value">{{ $activeCount }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="rs-stat-card tone-error">
                        <div class="rs-stat-top">
                            <span class="rs-label-md">{{ __('messages.flow_cancelled') }}</span>
                            <div class="rs-stat-icon"><i class="fas fa-times-circle"></i></div>
                        </div>
                        <div class="rs-stat-value">{{ $cancelledCount }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="rs-stat-card tone-primary">
                        <div class="rs-stat-top">
                            <span class="rs-label-md">{{ __('messages.flow_future') }}</span>
                            <div class="rs-stat-icon"><i class="fas fa-clock"></i></div>
                        </div>
                        <div class="rs-stat-value">{{ $futureCount }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 mb-4">
                <div class="card-header">
                    <h4 class="rs-title-lg mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        {{ $selectedGroup->name }}
                    </h4>
                </div>
                <div class="card-body">
                    @php
                        $competitionsForChart = $competitions->map(function ($competition) {
                            return [
                                'id' => $competition->id,
                                'name' => $competition->name,
                                'start_at' => $competition->start_at?->format('Y-m-d'),
                                'end_at' => $competition->end_at?->format('Y-m-d'),
                                'status' => $competition->status,
                            ];
                        })->values();
                    @endphp

                    @if($competitionsForChart->isEmpty())
                        <div class="alert alert-info mb-0">{{ __('messages.no_competitions') }}</div>
                    @else
                        <div class="mermaid group-flowchart" data-competitions='@json($competitionsForChart)'></div>
                    @endif
                </div>
            </div>
        @endif

        @if(!$selectedGroup)
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ __('messages.select_group_to_view_flowchart') }}
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const STATUS = {
                pending: {{ App\Enums\CompetitionStatus::PENDING }},
                active: {{ App\Enums\CompetitionStatus::ACTIVE }},
                finished: {{ App\Enums\CompetitionStatus::FINISHED }},
                cancelled: {{ App\Enums\CompetitionStatus::CANCELLED }},
            };

            mermaid.initialize({
                startOnLoad: false,
                securityLevel: 'loose',
            });

            const charts = document.querySelectorAll('.group-flowchart');
            const safeLabel = (value) => String(value ?? '')
                .replace(/[\[\]{}()<>|#`]/g, ' ')
                .replace(/"/g, "'")
                .replace(/\n|\r/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();

            const wrapText = (value, maxCharsPerLine = 26, maxLines = 4) => {
                const clean = safeLabel(value);
                if (!clean) return '';

                const words = clean.split(' ');
                const lines = [];
                let currentLine = '';

                words.forEach((word) => {
                    const candidate = currentLine ? `${currentLine} ${word}` : word;
                    if (candidate.length <= maxCharsPerLine) {
                        currentLine = candidate;
                    } else {
                        if (currentLine) {
                            lines.push(currentLine);
                        }
                        currentLine = word;
                    }
                });

                if (currentLine) {
                    lines.push(currentLine);
                }

                if (lines.length > maxLines) {
                    const visible = lines.slice(0, maxLines);
                    visible[maxLines - 1] = `${visible[maxLines - 1]}...`;
                    return visible.join('<br/>');
                }

                return lines.join('<br/>');
            };

            charts.forEach((chartEl, chartIndex) => {
                const competitions = JSON.parse(chartEl.dataset.competitions || '[]');

                if (!competitions.length) {
                    return;
                }

                const byDate = competitions.reduce((acc, item) => {
                    if (!acc[item.start_at]) {
                        acc[item.start_at] = [];
                    }
                    acc[item.start_at].push(item);
                    return acc;
                }, {});

                const dates = Object.keys(byDate);
                const groupedCompetitions = dates.map((date) => byDate[date]);
                const stagesPerRow = 4;
                const chunkedRows = [];
                for (let i = 0; i < groupedCompetitions.length; i += stagesPerRow) {
                    chunkedRows.push(groupedCompetitions.slice(i, i + stagesPerRow));
                }

                const lines = [
                    'flowchart TB',
                    'classDef finished fill:#dcfce7,stroke:#16a34a,stroke-width:2px,color:#111;',
                    'classDef active fill:#fef9c3,stroke:#ca8a04,stroke-width:2px,color:#111;',
                    'classDef cancelled fill:#fee2e2,stroke:#dc2626,stroke-width:2px,color:#111;',
                    'classDef future fill:#dbeafe,stroke:#2563eb,stroke-width:2px,color:#111;',
                    'classDef rowlink fill:transparent,stroke:transparent,color:transparent;'
                ];

                const rowBridgePoints = [];

                chunkedRows.forEach((rowStages, rowIndex) => {
                    lines.push(`subgraph ROW_${chartIndex}_${rowIndex}[" "]`);
                    lines.push('direction LR');

                    const rowStageNodeGroups = [];

                    rowStages.forEach((stage) => {
                        const stageNodes = [];

                        stage.forEach((competition) => {
                            const competitionNode = `GC${chartIndex}_${competition.id}`;
                            const wrappedName = wrapText(competition.name, 24, 3);
                            const dateText = safeLabel(`${competition.start_at} → ${competition.end_at}`);
                            const label = `${wrappedName}<br/>${dateText}`;

                            stageNodes.push(competitionNode);
                            lines.push(`${competitionNode}["${label}"]`);

                            if (competition.status === STATUS.finished) {
                                lines.push(`class ${competitionNode} finished`);
                            } else if (competition.status === STATUS.active) {
                                lines.push(`class ${competitionNode} active`);
                            } else if (competition.status === STATUS.cancelled) {
                                lines.push(`class ${competitionNode} cancelled`);
                            } else {
                                lines.push(`class ${competitionNode} future`);
                            }
                        });

                        rowStageNodeGroups.push(stageNodes);
                    });

                    for (let i = 1; i < rowStageNodeGroups.length; i++) {
                        const previousStage = rowStageNodeGroups[i - 1];
                        const currentStage = rowStageNodeGroups[i];
                        previousStage.forEach((fromNode) => {
                            currentStage.forEach((toNode) => {
                                lines.push(`${fromNode} --> ${toNode}`);
                            });
                        });
                    }

                    lines.push('end');

                    if (rowStageNodeGroups.length) {
                        rowBridgePoints.push({
                            rowEnd: rowStageNodeGroups[rowStageNodeGroups.length - 1][0],
                            rowStart: rowStageNodeGroups[0][0],
                        });
                    }
                });

                for (let i = 1; i < rowBridgePoints.length; i++) {
                    const turnNode = `TURN_${chartIndex}_${i}`;
                    lines.push(`${turnNode}(( ))`);
                    lines.push(`class ${turnNode} rowlink`);
                    lines.push(`${rowBridgePoints[i - 1].rowEnd} --> ${turnNode} --> ${rowBridgePoints[i].rowStart}`);
                }

                chartEl.textContent = lines.join('\n');
            });

            mermaid.run({
                nodes: Array.from(charts),
            });
        });
    </script>
@endpush
