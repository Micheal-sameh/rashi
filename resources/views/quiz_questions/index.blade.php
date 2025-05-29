@extends('layouts.sideBar')

@section('content')
    <div class="container" style="width: 95%;">
        <h2>{{ __('messages.questions') }}</h2>

        <!-- Dependent Dropdowns -->
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="competition">{{ __('messages.select_competition') }}</label>
                <select id="competition" class="form-control">
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
                <label for="quiz">{{ __('messages.select_quiz') }}</label>
                <select id="quiz" class="form-control">
                    <option value="">{{ __('messages.select_quiz') }}</option>
                </select>
            </div>
        </div>

        <!-- Create Button (Shown Only If quiz_id Is Selected) -->
        <div id="createButtonWrapper" style="display: none;">
            <a id="createQuestionLink" href="#" class="btn btn-success mb-3">
                {{ __('messages.create_questions') }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($questions->count())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('messages.question') }}</th>
                        <th>{{ __('messages.points') }}</th>
                        @for ($i = 1; $i <= 4; $i++)
                            <th>{{ __('messages.answer') }} {{ $i }}</th>
                        @endfor
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($questions as $question)
                        <tr>
                            <td>{{ $question->question }}</td>
                            <td>{{ $question->points }}</td>
                            @foreach ($question->answers as $answer)
                                <td style="color: {{ $answer->is_correct ? 'blue' : 'inherit' }};">
                                    {{ $answer->answer }}
                                </td>
                            @endforeach
                            <td>
                                <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-edit"></i>
                                </a>

                                <form action="{{ route('questions.delete', $question->id) }}" method="POST"
                                    style="display: inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this competition?')"><i
                                            class="fa fa-trash"></i></button>

                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @php
                $query = request()->query(); // Get current filters (e.g., quiz_id, competition_id)
            @endphp

            <div class="pagination">
                @foreach ($questions->getUrlRange(1, $questions->lastPage()) as $page => $url)
                    @php
                        $query['page'] = $page; // Add page number to the query
                    @endphp
                    <a href="{{ url()->current() . '?' . http_build_query($query) }}"
                        class="page-link {{ $questions->currentPage() == $page ? 'active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach
            </div>
        @else
            <p>{{ __('messages.no_questions_found') }}</p>
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
