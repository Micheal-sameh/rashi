@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-4" style="max-width: 1000px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">{{ __('messages.create_quiz') }}</h2>
            <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-1"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <form action="{{ route('quizzes.store') }}" method="POST">
                    @csrf

                    {{-- Quiz Info --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold">{{ __('messages.name') }}</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="date" class="form-label fw-bold">{{ __('messages.date') }}</label>
                            <input type="date" name="date" id="date"
                                class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}"
                                required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="competition_id" class="form-label fw-bold">{{ __('messages.competition') }}</label>
                            <select name="competition_id" id="competition_id"
                                class="form-select @error('competition_id') is-invalid @enderror" required>
                                <option value="">{{ __('messages.choose_competition') }}</option>
                                @foreach ($competitions as $competition)
                                    <option value="{{ $competition->id }}"
                                        {{ old('competition_id') == $competition->id ? 'selected' : '' }}>
                                        {{ $competition->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('competition_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Questions Section --}}
                    <h4 class="fw-bold text-secondary mb-3">{{ __('messages.questions') }}</h4>
                    <div id="questions-section">
                        <div class="question card mb-4 border-0 shadow-sm rounded-3" id="question-0">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Question 1</h5>

                                <input type="text" name="questions[0][question]" class="form-control mb-3"
                                    placeholder="Enter question text" required>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Points</label>
                                    <input type="number" name="questions[0][points]" class="form-control" min="1"
                                        required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bold">
                                        {{ __('messages.answers') }}
                                        <span class="badge bg-info ms-2">{{ __('messages.min_2_max_4') }}</span>
                                    </label>
                                </div>

                                <div class="answers-container" data-question-index="0">
                                    @for ($i = 1; $i <= 2; $i++)
                                        <div class="answer-row input-group mb-2">
                                            <div class="input-group-text">
                                                <input type="radio" name="questions[0][correct]" value="{{ $i }}"
                                                    {{ $i == 1 ? 'required checked' : 'required' }}>
                                            </div>
                                            <input type="text" name="questions[0][answers][{{ $i }}]"
                                                class="form-control answer-input" placeholder="Answer {{ $i }}" required>
                                            @if ($i > 2)
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-answer-btn">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endfor
                                </div>

                                <button type="button" class="btn btn-sm btn-outline-primary add-answer-btn mb-2"
                                    data-question-index="0">
                                    <i class="fa fa-plus me-1"></i> {{ __('messages.add_answer') }}
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-danger mt-2"
                                    onclick="removeQuestion('question-0')">
                                    <i class="fa fa-trash me-1"></i> Remove Question
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Add Question --}}
                    <button type="button" class="btn btn-outline-primary mb-3" onclick="addQuestion()">
                        <i class="fa fa-plus-circle me-1"></i> {{ __('messages.add_question') }}
                    </button>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fa fa-save me-1"></i> {{ __('messages.create') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JS --}}
    <script>
        let questionIndex = 1;
        const maxAnswers = 4;
        const minAnswers = 2;

        function addQuestion() {
            const section = document.getElementById('questions-section');
            const questionId = `question-${questionIndex}`;

            let answersHTML = '';
            for (let i = 1; i <= 2; i++) {
                answersHTML += `
                <div class="answer-row input-group mb-2">
                    <div class="input-group-text">
                        <input type="radio" name="questions[${questionIndex}][correct]" value="${i}" ${i === 1 ? 'checked' : ''} required>
                    </div>
                    <input type="text" name="questions[${questionIndex}][answers][${i}]" class="form-control answer-input" placeholder="Answer ${i}" required>
                </div>
            `;
            }

            const questionHTML = `
            <div class="question card mb-4 border-0 shadow-sm rounded-3" id="${questionId}">
                <div class="card-body">
                    <h5 class="card-title mb-3">Question ${questionIndex + 1}</h5>

                    <input type="text" name="questions[${questionIndex}][question]"
                           class="form-control mb-3" placeholder="Enter question text" required>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Points</label>
                        <input type="number" name="questions[${questionIndex}][points]" class="form-control" min="1" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">
                            {{ __('messages.answers') }}
                            <span class="badge bg-info ms-2">{{ __('messages.min_2_max_4') }}</span>
                        </label>
                    </div>

                    <div class="answers-container" data-question-index="${questionIndex}">
                        ${answersHTML}
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary add-answer-btn mb-2" data-question-index="${questionIndex}">
                        <i class="fa fa-plus me-1"></i> {{ __('messages.add_answer') }}
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeQuestion('${questionId}')">
                        <i class="fa fa-trash me-1"></i> Remove Question
                    </button>
                </div>
            </div>
        `;

            section.insertAdjacentHTML('beforeend', questionHTML);
            questionIndex++;
        }

        function removeQuestion(id) {
            const questionDiv = document.getElementById(id);
            if (questionDiv) {
                questionDiv.remove();
            }
        }

        // Handle adding answers
        document.addEventListener('click', function(e) {
            if (e.target.closest('.add-answer-btn')) {
                const btn = e.target.closest('.add-answer-btn');
                const questionIndex = btn.getAttribute('data-question-index');
                const container = document.querySelector(`.answers-container[data-question-index="${questionIndex}"]`);
                const currentCount = container.querySelectorAll('.answer-row').length;

                if (currentCount < maxAnswers) {
                    const newIndex = currentCount + 1;
                    const answerHTML = `
                        <div class="answer-row input-group mb-2">
                            <div class="input-group-text">
                                <input type="radio" name="questions[${questionIndex}][correct]" value="${newIndex}" required>
                            </div>
                            <input type="text" name="questions[${questionIndex}][answers][${newIndex}]" class="form-control answer-input" placeholder="Answer ${newIndex}" required>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-answer-btn">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', answerHTML);
                    updateAddButtonState(questionIndex);
                }
            }

            // Handle removing answers
            if (e.target.closest('.remove-answer-btn')) {
                const row = e.target.closest('.answer-row');
                const container = row.closest('.answers-container');
                const questionIndex = container.getAttribute('data-question-index');
                const currentCount = container.querySelectorAll('.answer-row').length;

                if (currentCount > minAnswers) {
                    row.remove();
                    reindexAnswers(questionIndex);
                    updateAddButtonState(questionIndex);
                }
            }
        });

        function reindexAnswers(questionIndex) {
            const container = document.querySelector(`.answers-container[data-question-index="${questionIndex}"]`);
            const rows = container.querySelectorAll('.answer-row');

            rows.forEach((row, index) => {
                const newIndex = index + 1;
                const input = row.querySelector('.answer-input');
                const radio = row.querySelector('input[type="radio"]');

                input.name = `questions[${questionIndex}][answers][${newIndex}]`;
                input.placeholder = `Answer ${newIndex}`;
                radio.value = newIndex;

                // Remove existing remove buttons
                const existingBtn = row.querySelector('.remove-answer-btn');
                if (existingBtn) {
                    existingBtn.remove();
                }

                // Add remove button only if more than minAnswers and not first 2
                if (rows.length > minAnswers && newIndex > minAnswers) {
                    if (!row.querySelector('.remove-answer-btn')) {
                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'btn btn-outline-danger btn-sm remove-answer-btn';
                        removeBtn.innerHTML = '<i class="fa fa-trash"></i>';
                        row.appendChild(removeBtn);
                    }
                }
            });
        }

        function updateAddButtonState(questionIndex) {
            const container = document.querySelector(`.answers-container[data-question-index="${questionIndex}"]`);
            const btn = document.querySelector(`.add-answer-btn[data-question-index="${questionIndex}"]`);
            const currentCount = container.querySelectorAll('.answer-row').length;

            if (currentCount >= maxAnswers) {
                btn.disabled = true;
                btn.classList.add('disabled');
            } else {
                btn.disabled = false;
                btn.classList.remove('disabled');
            }
        }
    </script>
@endsection
