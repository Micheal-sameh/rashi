@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">{{ __('messages.questions') }}</h2>
        </div>

        {{-- Dependent Dropdowns --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="competition" class="form-label fw-bold">
                            <i class="fa fa-trophy text-warning me-1"></i> {{ __('messages.select_competition') }}
                        </label>
                        <select id="competition" class="form-select">
                            <option value="">{{ __('messages.select_competition') }}</option>
                            @foreach ($competitions as $competition)
                                <option value="{{ $competition->id }}"
                                    {{ request()->get('competition_id') == $competition->id ? 'selected' : '' }}>
                                    {{ $competition->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="quiz" class="form-label fw-bold">
                            <i class="fa fa-question-circle text-info me-1"></i> {{ __('messages.select_quiz') }}
                        </label>
                        <select id="quiz" class="form-select">
                            <option value="">{{ __('messages.select_quiz') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create button --}}
        <div id="createButtonWrapper" class="mb-3" style="display: none;">
            <a id="createQuestionLink" href="#" class="btn btn-success">
                <i class="fa fa-plus-circle me-1"></i> {{ __('messages.create_questions') }}
            </a>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($questions->count())
            {{-- Desktop Table --}}
            <div class="card shadow-sm border-0 rounded-4 d-none d-md-block">
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.question') }}</th>
                                <th>{{ __('messages.points') }}</th>
                                @for ($i = 1; $i <= 4; $i++)
                                    <th>{{ __('messages.answer') }} {{ $i }}</th>
                                @endfor
                                <th class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questions as $question)
                                <tr>
                                    <td>{{ $question->question }}</td>
                                    <td><span class="badge bg-primary">{{ $question->points }}</span></td>
                                    @foreach ($question->answers as $answer)
                                        <td>
                                            <span class="{{ $answer->is_correct ? 'fw-bold text-success' : '' }}">
                                                {{ $answer->answer }}
                                            </span>
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        <a href="{{ route('questions.edit', $question->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('questions.delete', $question->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="delete-modal"
                                                data-message="{{ __('messages.confirm_delete_question') }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $questions->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

            {{-- Mobile Cards --}}
            <div class="d-block d-md-none">
                @foreach ($questions as $question)
                    <div class="card shadow-sm border-0 rounded-4 mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2">{{ $question->question }}</h6>
                            <p class="mb-2">
                                <span class="badge bg-primary">{{ __('messages.points') }}: {{ $question->points }}</span>
                            </p>
                            <ul class="list-group mb-3">
                                @foreach ($question->answers as $index => $answer)
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center
                                    {{ $answer->is_correct ? 'list-group-item-success fw-bold' : '' }}">
                                        {{ __('messages.answer') }} {{ $index + 1 }}: {{ $answer->answer }}
                                        @if ($answer->is_correct)
                                            <i class="fa fa-check text-success"></i>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('questions.edit', $question->id) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('questions.delete', $question->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="delete-modal"
                                        data-message="{{ __('messages.confirm_delete_question') }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-center mt-3">
                    {{ $questions->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle me-2"></i> {{ __('messages.no_questions_found') }}
            </div>
        @endif
    </div>

    <!-- Modal HTML -->
    <div id="imageModal" class="modal" onclick="closeModal()">
        <span class="close">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 70%;
            height: 70%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.9);
        }

        .modal-content {
            margin: auto;
            display: block;
            max-width: 80%;
            max-height: 80%;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
        }
    </style>

    <script>
        const selectQuizText = @json(__('messages.select_quiz'));

        const competitionSelect = document.getElementById('competition');
        const quizSelect = document.getElementById('quiz');
        const createButtonWrapper = document.getElementById('createButtonWrapper');
        const createQuestionLink = document.getElementById('createQuestionLink');

        // Redirect to questions index when competition changes
        competitionSelect.addEventListener('change', function() {
            const competitionId = this.value;
            if (competitionId) {
                const query = new URLSearchParams();
                query.set('competition_id', competitionId);
                window.location.href = `/questions?${query.toString()}`;
            } else {
                quizSelect.innerHTML = `<option value="">${selectQuizText}</option>`;
                createButtonWrapper.style.display = 'none';
            }
        });

        // Populate quizzes dropdown and restore quiz if selected
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            const competitionId = params.get('competition_id');
            const selectedQuizId = params.get('quiz_id');

            if (competitionId) {
                fetch(`/quizzes/dropdown/${competitionId}`)
                    .then(response => response.json())
                    .then(data => {
                        quizSelect.innerHTML = `<option value="">${selectQuizText}</option>`;
                        data.forEach(quiz => {
                            const option = document.createElement('option');
                            option.value = quiz.id;
                            option.text = quiz.name;
                            quizSelect.appendChild(option);
                        });

                        if (selectedQuizId) {
                            quizSelect.value = selectedQuizId;
                            createQuestionLink.href = `/questions/create?quiz_id=${selectedQuizId}`;
                            createButtonWrapper.style.display = 'block';
                        }
                    });
            }
        });

        quizSelect.addEventListener('change', function() {
            const quizId = this.value;
            const competitionId = competitionSelect.value;

            const query = new URLSearchParams();
            if (quizId) query.set('quiz_id', quizId);
            if (competitionId) query.set('competition_id', competitionId);

            window.location.href = `/questions?${query.toString()}`;
        });

        function openModal(src) {
            document.getElementById('imageModal').style.display = "block";
            document.getElementById('modalImage').src = src;
        }

        function closeModal() {
            document.getElementById('imageModal').style.display = "none";
        }
    </script>
@endsection
