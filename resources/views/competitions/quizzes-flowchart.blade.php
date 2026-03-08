@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-1">{{ __('messages.quiz_flowchart_report') }}</h2>
                <p class="text-muted mb-0">{{ $competition->name }}</p>
            </div>
            <a href="{{ route('competitions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
            </a>
        </div>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3 small">
                    <span><i class="fas fa-circle text-success me-1"></i>{{ __('messages.flow_finished') }}</span>
                    <span><i class="fas fa-circle text-warning me-1"></i>{{ __('messages.flow_active') }}</span>
                    <span><i class="fas fa-circle text-primary me-1"></i>{{ __('messages.flow_future') }}</span>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
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
            const lines = [
                'flowchart LR',
                'classDef finished fill:#dcfce7,stroke:#16a34a,stroke-width:2px,color:#111;',
                'classDef active fill:#fef9c3,stroke:#ca8a04,stroke-width:2px,color:#111;',
                'classDef future fill:#dbeafe,stroke:#2563eb,stroke-width:2px,color:#111;'
            ];

            const safeLabel = (value) => String(value ?? '')
                .replace(/[\[\]{}()<>|#`]/g, ' ')
                .replace(/"/g, "'")
                .replace(/\n|\r/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();

            dates.forEach((date, index) => {
                const dateNode = `D${index}`;
                lines.push(`${dateNode}["${safeLabel(date)}"]`);

                if (index > 0) {
                    lines.push(`D${index - 1} --> ${dateNode}`);
                }

                byDate[date].forEach((quiz) => {
                    const quizNode = `Q${quiz.id}`;
                    lines.push(`${quizNode}["${safeLabel(quiz.name)}"]`);
                    lines.push(`${dateNode} --> ${quizNode}`);
                    lines.push(`class ${quizNode} ${statusClass[quiz.status] || 'future'}`);
                });
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
