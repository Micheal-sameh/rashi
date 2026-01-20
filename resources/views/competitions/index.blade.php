@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">{{ __('messages.competitions') }}</h2>
            <a href="{{ route('competitions.create') }}" class="btn btn-success">
                <i class="fa fa-plus-circle me-1"></i> {{ __('messages.create_competitions') }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($competitions->count())
            <div class="card shadow-sm border-0 rounded-4 d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.start_at') }}</th>
                                <th>{{ __('messages.end_at') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.image') }}</th>
                                <th class="text-center">{{ __('messages.actions') }}</th>
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
                                    <td class="fw-semibold">{{ $competition->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($competition->start_at)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($competition->end_at)->format('d M Y') }}</td>
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
                                                alt="Image" width="60" class="rounded shadow-sm zoomable-image"
                                                onclick="openPopup(this.src)">
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('competitions.edit', $competition->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                title="Upload Quizzes"
                                                onclick="openUploadModal({{ $competition->id }}, '{{ $competition->name }}')">
                                                <i class="fa fa-file-excel"></i>
                                            </button>
                                            <a href="{{ route('competitions.userAnswers', $competition->id) }}"
                                                class="btn btn-sm btn-outline-info" title="User Answers">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <form action="{{ route('competitions.setActive', $competition->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('put')
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                    title="Set Active">
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
                                                    title="Delete">
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
                    <div class="card-footer d-flex justify-content-center">
                        {{ $competitions->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            {{-- Mobile cards --}}
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

                    <div class="card mb-3 shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                @if ($competition->hasMedia('competitions_images'))
                                    <img src="{{ $competition->getFirstMediaUrl('competitions_images') }}" alt="Image"
                                        width="70" class="rounded shadow-sm me-3 zoomable-image"
                                        onclick="openPopup(this.src)">
                                @endif
                                <h5 class="mb-0 fw-bold text-primary">{{ $competition->name }}</h5>
                            </div>
                            <p class="mb-1"><strong>{{ __('messages.start_at') }}:</strong>
                                {{ \Carbon\Carbon::parse($competition->start_at)->format('d M Y') }}</p>
                            <p class="mb-1"><strong>{{ __('messages.end_at') }}:</strong>
                                {{ \Carbon\Carbon::parse($competition->end_at)->format('d M Y') }}</p>
                            <p class="mb-3">
                            <form action="{{ route('competitions.changeStatus', $competition->id) }}" method="POST"
                                class="status-form d-inline" data-id="{{ $competition->id }}">
                                @csrf
                                @method('put')
                                <button type="submit" class="btn btn-sm {{ $btnClass }}"
                                    id="status-btn-{{ $competition->id }}">
                                    {{ App\Enums\CompetitionStatus::getStringValue($competition->status) }}
                                </button>
                            </form>
                            </p>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('competitions.edit', $competition->id) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-success"
                                    onclick="openUploadModal({{ $competition->id }}, '{{ $competition->name }}')">
                                    <i class="fa fa-file-excel"></i>
                                </button>
                                <a href="{{ route('competitions.userAnswers', $competition->id) }}"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <form action="{{ route('competitions.setActive', $competition->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('put')
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Set Active">
                                        <i class="fa fa-play"></i>
                                    </button>
                                </form>
                                <form action="{{ route('competitions.cancel', $competition->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('put')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="delete-modal"
                                        data-message="{{ __('messages.confirm_delete_competition', ['name' => $competition->name]) }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if ($competitions->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $competitions->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Image Popup Modal -->
    <div id="popupModal" class="popup-modal">
        <div class="popup-content">
            <span class="popup-close" onclick="closePopup()">&times;</span>
            <img id="popupImage" src="" alt="Popup Image" />
        </div>
    </div>

    <!-- Upload Quizzes Modal -->
    <div class="modal fade" id="uploadQuizzesModal" tabindex="-1" aria-labelledby="uploadQuizzesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="uploadQuizzesModalLabel">
                        <i class="fa fa-file-excel me-2"></i>Upload Quizzes Excel
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadQuizzesForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted mb-3">
                            Upload an Excel file with quiz questions for <strong id="competitionName"></strong>
                        </p>

                        <div class="mb-3">
                            <label for="quiz_file" class="form-label fw-semibold">Excel File</label>
                            <input type="file" name="file" id="quiz_file" class="form-control"
                                accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">
                                Accepted formats: .xlsx, .xls, .csv (Max: 10MB)
                            </small>
                        </div>

                        <!-- Excel Format Instructions -->
                        <div class="alert alert-info rounded-3">
                            <h6 class="fw-bold"><i class="fa fa-info-circle me-1"></i>Excel Format:</h6>
                            <ul class="mb-0 small">
                                <li><strong>Headers:</strong> quiz_name, date, question, points, answer_1, answer_2, answer_3, answer_4, correct</li>
                                <li>The <strong>date</strong> column should contain the quiz date (format: YYYY-MM-DD or any valid date format)</li>
                                <li>The <strong>correct</strong> column should contain the answer number (1, 2, 3, or 4)</li>
                                <li>Rows with the same quiz_name will be grouped together</li>
                                <li>If a quiz name already exists, questions will be added to it</li>
                            </ul>
                            <div class="mt-2">
                                <a href="{{ route('competitions.downloadExampleExcel') }}" class="btn btn-sm btn-outline-info">
                                    <i class="fa fa-download me-1"></i>Download Example Excel
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-upload me-1"></i>Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .popup-modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background-color: #fff;
            padding: 15px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.4);
        }

        .popup-content img {
            max-width: 100%;
            border-radius: 8px;
        }

        .popup-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            cursor: pointer;
            color: #333;
        }

        .zoomable-image {
            cursor: pointer;
            transition: transform 0.3s;
        }

        .zoomable-image:hover {
            transform: scale(1.1);
        }
    </style>

    <script>
        function openPopup(src) {
            $('#popupImage').attr('src', src);
            $('#popupModal').fadeIn();
        }

        function closePopup() {
            $('#popupModal').fadeOut();
        }

        // ESC key closes popup
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePopup();
            }
        });

        // AJAX Setup for CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // AJAX status change
        $(function() {
            $('.status-form').on('submit', function(e) {
                e.preventDefault();
                let form = $(this),
                    id = form.data('id'),
                    url = form.attr('action');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'PUT'
                    },
                    success: function(response) {
                        let statusBtn = $('#status-btn-' + id);
                        statusBtn.text(response.competition);
                        statusBtn.removeClass().addClass('btn btn-sm ' + response.status_class);
                    },
                    error: function() {
                        showToast('Failed to update status. Try again.', 'danger');
                    }
                });
            });
        });

        // Simple Toast for feedback
        function showToast(message, type = 'success') {
            let toast = $(`<div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                           <div class="d-flex">
                               <div class="toast-body">${message}</div>
                               <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                           </div>
                       </div>`);
            $('.container-fluid').prepend(toast);
            new bootstrap.Toast(toast[0]).show();
        }

        // Open upload modal
        let currentCompetitionId = null;
        function openUploadModal(competitionId, competitionName) {
            currentCompetitionId = competitionId;
            $('#competitionName').text(competitionName);
            $('#uploadQuizzesModal').modal('show');
        }

        // Handle quiz upload
        $('#uploadQuizzesForm').on('submit', function(e) {
            e.preventDefault();

            if (!currentCompetitionId) {
                showToast('Competition ID not found', 'danger');
                return;
            }

            let formData = new FormData(this);
            let submitBtn = $(this).find('button[type="submit"]');
            let originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>Uploading...');

            $.ajax({
                url: '/competitions/' + currentCompetitionId + '/upload-quizzes',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#uploadQuizzesModal').modal('hide');
                    $('#uploadQuizzesForm')[0].reset();
                    showToast('Quizzes uploaded successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    let message = 'Error uploading quizzes';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToast(message, 'danger');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Reset form when modal is closed
        $('#uploadQuizzesModal').on('hidden.bs.modal', function() {
            $('#uploadQuizzesForm')[0].reset();
        });
    </script>
@endsection
