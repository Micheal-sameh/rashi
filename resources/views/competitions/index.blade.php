@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-gradient mb-1">
                    <i class="fas fa-trophy me-2"></i>{{ __('messages.competitions') }}
                </h2>
                <p class="text-muted mb-0">{{ __('messages.manage_all_competitions') }}</p>
            </div>
            <a href="{{ route('competitions.create') }}" class="btn btn-success btn-lg hover-lift">
                <i class="fa fa-plus-circle me-2"></i> {{ __('messages.create_competitions') }}
            </a>
        </div>

        <!-- Competition Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 opacity-75">{{ __('messages.active_competitions') }}</h6>
                                <h2 class="mb-0 fw-bold">{{ $competitionCounts['active'] }}</h2>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fas fa-trophy"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 opacity-75">{{ __('messages.pending_competitions') }}</h6>
                                <h2 class="mb-0 fw-bold">{{ $competitionCounts['pending'] }}</h2>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 opacity-75">{{ __('messages.finished_competitions') }}</h6>
                                <h2 class="mb-0 fw-bold">{{ $competitionCounts['finished'] }}</h2>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fas fa-flag-checkered"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($competitions->count())
            <!-- Desktop Table View -->
            <div class="card shadow-soft rounded-4 border-0 d-none d-md-block mb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-flag me-2"></i>{{ __('messages.name') }}</th>
                                <th><i class="fas fa-calendar-start me-2"></i>{{ __('messages.start_at') }}</th>
                                <th><i class="fas fa-calendar-check me-2"></i>{{ __('messages.end_at') }}</th>
                                <th><i class="fas fa-info-circle me-2"></i>{{ __('messages.status') }}</th>
                                <th><i class="fas fa-image me-2"></i>{{ __('messages.image') }}</th>
                                <th class="text-center"><i class="fas fa-cog me-2"></i>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($competitions as $competition)
                                @php
                                    $btnClasses = [
                                        App\Enums\CompetitionStatus::PENDING => 'btn-primary',
                                        App\Enums\CompetitionStatus::ACTIVE => 'btn-warning',
                                        App\Enums\CompetitionStatus::FINISHED => 'btn-info',
                                        App\Enums\CompetitionStatus::CANCELLED => 'btn-danger',
                                    ];
                                    $btnClass = $btnClasses[$competition->status] ?? 'btn-secondary';
                                @endphp
                                <tr>
                                    <td class="fw-semibold">
                                        <i class="fas fa-trophy text-primary me-2"></i>{{ $competition->name }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ \Carbon\Carbon::parse($competition->start_at)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ \Carbon\Carbon::parse($competition->end_at)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('competitions.changeStatus', $competition->id) }}"
                                            method="POST" class="status-form d-inline" data-id="{{ $competition->id }}">
                                            @csrf
                                            @method('put')
                                            <button type="submit" class="btn btn-sm {{ $btnClass }}"
                                                id="status-btn-{{ $competition->id }}">
                                                {{ App\Enums\CompetitionStatus::getStringValue($competition->status) }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        @if ($competition->hasMedia('competitions_images'))
                                            <img src="{{ $competition->getFirstMediaUrl('competitions_images') }}"
                                                alt="Competition Image"
                                                class="rounded-3 shadow-sm zoomable-image"
                                                style="width: 70px; height: 70px; object-fit: cover;"
                                                onclick="openPopup(this.src)">
                                        @else
                                            <div class="text-center p-3 bg-light rounded-3">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('competitions.edit', $competition->id) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                title="{{ __('messages.edit') }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('competitions.export', $competition->id) }}"
                                                class="btn btn-sm btn-outline-info"
                                                title="Export Competition">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                title="{{ __('messages.upload_quizzes') }}"
                                                onclick="openUploadModal({{ $competition->id }}, '{{ $competition->name }}')">
                                                <i class="fa fa-file-excel"></i>
                                            </button>
                                            <a href="{{ route('competitions.userAnswers', $competition->id) }}"
                                                class="btn btn-sm btn-outline-info"
                                                title="{{ __('messages.user_answers') }}">
                                                <i class="fa fa-users"></i>
                                            </a>
                                            <form action="{{ route('competitions.setActive', $competition->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('put')
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                    title="{{ __('messages.set_active') }}">
                                                    <i class="fa fa-play"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('competitions.cancel', $competition->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('put')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="delete-modal"
                                                    data-message="{{ __('messages.confirm_delete_competition', ['name' => $competition->name]) }}"
                                                    title="{{ __('messages.delete') }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($competitions->hasPages())
                    <div class="card-footer bg-white border-0 d-flex justify-content-center py-3">
                        {{ $competitions->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            {{-- Mobile Cards --}}
            <div class="d-md-none">
                @foreach ($competitions as $competition)
                    @php
                        $btnClasses = [
                            App\Enums\CompetitionStatus::PENDING => 'btn-primary',
                            App\Enums\CompetitionStatus::ACTIVE => 'btn-warning',
                            App\Enums\CompetitionStatus::FINISHED => 'btn-info',
                            App\Enums\CompetitionStatus::CANCELLED => 'btn-danger',
                        ];
                        $btnClass = $btnClasses[$competition->status] ?? 'btn-secondary';
                    @endphp

                    <div class="card mb-3 shadow-soft border-0 rounded-4 hover-lift">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                @if ($competition->hasMedia('competitions_images'))
                                    <img src="{{ $competition->getFirstMediaUrl('competitions_images') }}"
                                        alt="Competition"
                                        class="rounded-3 shadow-sm me-3 zoomable-image"
                                        style="width: 80px; height: 80px; object-fit: cover;"
                                        onclick="openPopup(this.src)">
                                @endif
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fw-bold text-gradient">
                                        <i class="fas fa-trophy me-1"></i>{{ $competition->name }}
                                    </h5>
                                    <form action="{{ route('competitions.changeStatus', $competition->id) }}"
                                        method="POST"
                                        class="status-form d-inline" data-id="{{ $competition->id }}">
                                        @csrf
                                        @method('put')
                                        <button type="submit" class="btn btn-sm {{ $btnClass }}"
                                            id="status-btn-{{ $competition->id }}">
                                            {{ App\Enums\CompetitionStatus::getStringValue($competition->status) }}
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="mb-3">
                                <p class="mb-1">
                                    <i class="fas fa-calendar-start text-primary me-2"></i>
                                    <strong>{{ __('messages.start_at') }}:</strong>
                                    {{ \Carbon\Carbon::parse($competition->start_at)->format('d M Y') }}
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-calendar-check text-success me-2"></i>
                                    <strong>{{ __('messages.end_at') }}:</strong>
                                    {{ \Carbon\Carbon::parse($competition->end_at)->format('d M Y') }}
                                </p>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('competitions.edit', $competition->id) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-edit me-1"></i>{{ __('messages.edit') }}
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-success"
                                    onclick="openUploadModal({{ $competition->id }}, '{{ $competition->name }}')">
                                    <i class="fa fa-file-excel me-1"></i>{{ __('messages.upload') }}
                                </button>
                                <a href="{{ route('competitions.userAnswers', $competition->id) }}"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="fa fa-users me-1"></i>{{ __('messages.answers') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if ($competitions->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $competitions->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="card shadow-soft rounded-4 border-0">
                <div class="card-body text-center py-5">
                    <i class="fas fa-trophy fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">{{ __('messages.no_competitions') }}</h4>
                    <p class="text-muted">{{ __('messages.create_first_competition') }}</p>
                    <a href="{{ route('competitions.create') }}" class="btn btn-primary mt-3">
                        <i class="fa fa-plus-circle me-2"></i>{{ __('messages.create_competition') }}
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Image Popup Modal -->
    <div id="popupModal" class="popup-modal" onclick="closePopup()">
        <div class="popup-content" onclick="event.stopPropagation()">
            <span class="popup-close" onclick="closePopup()">&times;</span>
            <img id="popupImage" src="" alt="Competition Image" class="img-fluid" />
        </div>
    </div>

    <!-- Upload Quizzes Modal -->
    <div class="modal fade" id="uploadQuizzesModal" tabindex="-1" aria-labelledby="uploadQuizzesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header text-white" style="background: var(--success-gradient);">
                    <h5 class="modal-title" id="uploadQuizzesModalLabel">
                        <i class="fa fa-file-excel me-2"></i>{{ __('messages.upload_quizzes_excel') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="uploadQuizzesForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-info rounded-3 mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('messages.uploading_for') }} <strong id="competitionName"></strong>
                        </div>

                        <div class="mb-4">
                            <label for="quiz_file" class="form-label fw-semibold">
                                <i class="fas fa-file me-2"></i>{{ __('messages.select_excel_file') }}
                            </label>
                            <input type="file" name="file" id="quiz_file"
                                class="form-control form-control-lg"
                                accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">
                                <i class="fas fa-check-circle me-1"></i>
                                {{ __('messages.accepted_formats') }}: .xlsx, .xls, .csv (Max: 10MB)
                            </small>
                        </div>

                        <!-- Excel Format Guide -->
                        <div class="card bg-light border-0 rounded-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold text-primary mb-0">
                                        <i class="fa fa-table me-2"></i>{{ __('messages.excel_format_guide') }}
                                    </h6>
                                    <a href="{{ route('competitions.downloadExampleExcel') }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fa fa-download me-1"></i>{{ __('messages.download_template') }}
                                    </a>
                                </div>
                                <ul class="mb-0 small">
                                    <li class="mb-2">
                                        <strong>{{ __('messages.required_columns') }}:</strong>
                                        quiz_name, date, question, points, answer_1, answer_2, answer_3, answer_4, correct
                                    </li>
                                    <li class="mb-2">
                                        <strong>{{ __('messages.date_format') }}:</strong> YYYY-MM-DD or any valid date
                                    </li>
                                    <li class="mb-2">
                                        <strong>{{ __('messages.correct_answer') }}:</strong> Number 1-4 indicating correct answer
                                    </li>
                                    <li>
                                        <strong>{{ __('messages.grouping') }}:</strong> Same quiz_name groups questions together
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times me-2"></i>{{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-upload me-2"></i>{{ __('messages.upload') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Image Popup
        function openPopup(src) {
            document.getElementById('popupImage').src = src;
            document.getElementById('popupModal').style.display = 'flex';
        }

        function closePopup() {
            document.getElementById('popupModal').style.display = 'none';
        }

        // ESC key closes popup
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closePopup();
        });

        // AJAX Setup
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Status Change
        $('.status-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const id = form.data('id');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: { _method: 'PUT' },
                success: (response) => {
                    const btn = $(`#status-btn-${id}`);
                    btn.text(response.competition).removeClass().addClass(`btn btn-sm ${response.status_class}`);
                },
                error: () => alert('{{ __("messages.error_updating_status") }}')
            });
        });

        // Upload Modal
        let currentCompetitionId = null;
        function openUploadModal(id, name) {
            currentCompetitionId = id;
            $('#competitionName').text(name);
            $('#uploadQuizzesModal').modal('show');
        }

        function openCloneModal(competitionId, competitionName) {
            document.getElementById('cloneCompetitionName').textContent = competitionName;
            document.getElementById('cloneCompetitionForm').action = `/competitions/${competitionId}/clone`;
            const modal = new bootstrap.Modal(document.getElementById('cloneCompetitionModal'));
            modal.show();
        }

        // Handle Upload
        $('#uploadQuizzesForm').on('submit', function(e) {
            e.preventDefault();
            if (!currentCompetitionId) return;

            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');
            const originalHtml = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>{{ __("messages.uploading") }}...');

            $.ajax({
                url: `/competitions/${currentCompetitionId}/upload-quizzes`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: () => {
                    $('#uploadQuizzesModal').modal('hide');
                    location.reload();
                },
                error: (xhr) => {
                    alert(xhr.responseJSON?.message || '{{ __("messages.upload_error") }}');
                    submitBtn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    </script>

    <style>
        .popup-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.85);
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }

        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 16px;
            max-width: 800px;
            width: 90%;
            position: relative;
            animation: zoomIn 0.3s ease;
        }

        .popup-close {
            position: absolute;
            top: -40px;
            right: 0;
            font-size: 32px;
            color: white;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .popup-close:hover {
            transform: rotate(90deg);
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
    @endpush
@endsection
