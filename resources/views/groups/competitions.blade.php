@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">{{ __('messages.groups_competitions') }}</h2>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('groups.competitions') }}" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label for="group_id" class="form-label fw-semibold">{{ __('messages.select_group_for_flowchart') }}</label>
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
                                class="btn btn-outline-danger w-100">
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
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h6 class="fw-bold text-secondary mb-3">
                        <i class="fas fa-palette me-2"></i>Color Key
                    </h6>
                    <div class="row g-2">
                        <div class="col-md-3 col-6">
                            <div class="border rounded-3 px-3 py-2 bg-success-subtle d-flex justify-content-between align-items-center">
                                <span class="small fw-semibold text-success"><i class="fas fa-circle me-1"></i>{{ __('messages.flow_finished') }}</span>
                                <span class="badge text-bg-success">{{ $finishedCount }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded-3 px-3 py-2 bg-warning-subtle d-flex justify-content-between align-items-center">
                                <span class="small fw-semibold text-warning"><i class="fas fa-circle me-1"></i>{{ __('messages.flow_active') }}</span>
                                <span class="badge text-bg-warning text-dark">{{ $activeCount }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded-3 px-3 py-2 bg-danger-subtle d-flex justify-content-between align-items-center">
                                <span class="small fw-semibold text-danger"><i class="fas fa-circle me-1"></i>{{ __('messages.flow_cancelled') }}</span>
                                <span class="badge text-bg-danger">{{ $cancelledCount }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded-3 px-3 py-2 bg-primary-subtle d-flex justify-content-between align-items-center">
                                <span class="small fw-semibold text-primary"><i class="fas fa-circle me-1"></i>{{ __('messages.flow_future') }}</span>
                                <span class="badge text-bg-primary">{{ $futureCount }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
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
                const lines = [
                    'flowchart LR',
                    'classDef finished fill:#dcfce7,stroke:#16a34a,stroke-width:2px,color:#111;',
                    'classDef active fill:#fef9c3,stroke:#ca8a04,stroke-width:2px,color:#111;',
                    'classDef cancelled fill:#fee2e2,stroke:#dc2626,stroke-width:2px,color:#111;',
                    'classDef future fill:#dbeafe,stroke:#2563eb,stroke-width:2px,color:#111;'
                ];

                groupedCompetitions.forEach((group) => {
                    group.forEach((competition) => {
                        const competitionNode = `GC${chartIndex}_${competition.id}`;
                        const label = `${competition.name} (${competition.start_at} → ${competition.end_at})`;

                        lines.push(`${competitionNode}["${safeLabel(label)}"]`);

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
                });

                for (let i = 1; i < groupedCompetitions.length; i++) {
                    const previousGroup = groupedCompetitions[i - 1];
                    const currentGroup = groupedCompetitions[i];

                    previousGroup.forEach((previousCompetition) => {
                        currentGroup.forEach((currentCompetition) => {
                            lines.push(`GC${chartIndex}_${previousCompetition.id} --> GC${chartIndex}_${currentCompetition.id}`);
                        });
                    });
                }

                chartEl.textContent = lines.join('\n');
            });

            mermaid.run({
                nodes: Array.from(charts),
            });
        });
    </script>
@endpush
