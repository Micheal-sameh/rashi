@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <x-page-header icon="fa-project-diagram" :title="__('messages.quiz_flowchart_report')" :subtitle="$competition->name">
            <x-slot:actions>
                <a href="{{ route('competitions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
                </a>
            </x-slot>
        </x-page-header>

        <div class="card rounded-4 shadow-soft mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3 rs-body-lg mb-0">
                    <span><i class="fas fa-circle text-success me-1"></i>{{ __('messages.flow_finished') }}</span>
                    <span><i class="fas fa-circle text-warning me-1"></i>{{ __('messages.flow_active') }}</span>
                    <span><i class="fas fa-circle text-primary me-1"></i>{{ __('messages.flow_future') }}</span>
                </div>
            </div>
        </div>

        <div class="card rounded-4 shadow-soft">
            <div class="card-body">
                @if($quizTimeline->isEmpty())
                    <div class="alert alert-info mb-0">{{ __('messages.no_quizzes') }}</div>
                @else
                    <div class="mermaid" id="competitionQuizFlowchart"></div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const entries = @json($quizTimeline);
            if (!entries.length) {
                return;
            }

            const statusClass = {
                finished: 'finished',
                active: 'active',
                pending: 'future',
            };

            const byDate = entries.reduce((acc, entry) => {
                if (!acc[entry.date]) {
                    acc[entry.date] = [];
                }
                acc[entry.date].push(entry);
                return acc;
            }, {});

            const dates = Object.keys(byDate);
            const groupedQuizzes = dates.map((date) => byDate[date]);
            const stagesPerRow = 4;
            const chunkedRows = [];
            for (let i = 0; i < groupedQuizzes.length; i += stagesPerRow) {
                chunkedRows.push(groupedQuizzes.slice(i, i + stagesPerRow));
            }

            const lines = [
                'flowchart TB',
                'classDef finished fill:#dcfce7,stroke:#16a34a,stroke-width:2px,color:#111;',
                'classDef active fill:#fef9c3,stroke:#ca8a04,stroke-width:2px,color:#111;',
                'classDef future fill:#e6e3fb,stroke:#3525cd,stroke-width:2px,color:#111;',
                'classDef rowlink fill:transparent,stroke:transparent,color:transparent;'
            ];

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

            const rowBridgePoints = [];

            chunkedRows.forEach((rowStages, rowIndex) => {
                lines.push(`subgraph QROW_${rowIndex}[" "]`);
                lines.push('direction LR');

                const rowStageNodeGroups = [];

                rowStages.forEach((stage) => {
                    const stageNodes = [];

                    stage.forEach((quiz) => {
                        const quizNode = `Q${quiz.id}`;
                        const wrappedName = wrapText(quiz.name, 24, 3);
                        const dateText = safeLabel(quiz.date);
                        const label = `${wrappedName}<br/>${dateText}`;

                        stageNodes.push(quizNode);
                        lines.push(`${quizNode}["${label}"]`);
                        lines.push(`class ${quizNode} ${statusClass[quiz.status] || 'future'}`);
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
                const turnNode = `QTURN_${i}`;
                lines.push(`${turnNode}(( ))`);
                lines.push(`class ${turnNode} rowlink`);
                lines.push(`${rowBridgePoints[i - 1].rowEnd} --> ${turnNode} --> ${rowBridgePoints[i].rowStart}`);
            });

            mermaid.initialize({
                startOnLoad: false,
                securityLevel: 'loose',
            });

            const chart = document.getElementById('competitionQuizFlowchart');
            chart.textContent = lines.join('\n');
            mermaid.run({ nodes: [chart] });
        });
    </script>
@endpush
