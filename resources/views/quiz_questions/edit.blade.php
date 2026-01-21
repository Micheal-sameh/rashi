@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <!-- Heading -->
                <h3 class="fw-bold text-primary mb-4">
                    <i class="fa fa-edit me-2"></i> {{ __('messages.edit_question') }}
                </h3>

                <!-- Error Handling -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-circle me-2"></i>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Edit Form -->
                <form action="{{ route('questions.update', $question->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Hidden Quiz ID -->
                    <input type="hidden" name="quiz_id" value="{{ $question->quiz_id }}">

                    <!-- Question Type Toggle -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fa fa-image text-primary me-1"></i> {{ __('messages.question_type') }}
                        </label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="question_type" id="text_type" value="text"
                                {{ !$question->hasMedia('question_image') ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="text_type">
                                <i class="fa fa-font me-1"></i> Text
                            </label>
                            <input type="radio" class="btn-check" name="question_type" id="image_type" value="image"
                                {{ $question->hasMedia('question_image') ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="image_type">
                                <i class="fa fa-image me-1"></i> Image
                            </label>
                        </div>
                    </div>

                    <!-- Question Text -->
                    <div class="mb-3 {{ $question->hasMedia('question_image') ? 'd-none' : '' }}" id="text-question-container">
                        <label for="question" class="form-label fw-bold">
                            <i class="fa fa-question-circle text-info me-1"></i> {{ __('messages.question') }}
                        </label>
                        <input type="text" name="question" id="question" class="form-control form-control-lg"
                            value="{{ old('question', $question->question) }}">
                    </div>

                    <!-- Question Image -->
                    <div class="mb-3 {{ !$question->hasMedia('question_image') ? 'd-none' : '' }}" id="image-question-container">
                        <label for="question_image" class="form-label fw-bold">
                            <i class="fa fa-image text-info me-1"></i> {{ __('messages.question_image') }}
                        </label>
                        @if ($question->hasMedia('question_image'))
                            <div class="mb-2">
                                <img src="{{ $question->getFirstMediaUrl('question_image') }}" class="img-thumbnail" style="max-width: 300px;">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image">
                                    <label class="form-check-label text-danger" for="remove_image">
                                        {{ __('messages.remove_image') }}
                                    </label>
                                </div>
                            </div>
                        @endif
                        <input type="file" name="question_image" id="question_image" class="form-control"
                            accept="image/*">
                        <small class="text-muted">{{ __('messages.supported_formats') }}: JPG, PNG, GIF</small>
                        <div id="image-preview" class="mt-2"></div>
                    </div>

                    <!-- Points -->
                    <div class="mb-4">
                        <label for="points" class="form-label fw-bold">
                            <i class="fa fa-star text-warning me-1"></i> {{ __('messages.points') }}
                        </label>
                        <input type="number" name="points" id="points" class="form-control"
                            value="{{ old('points', $question->points) }}" min="1" required>
                    </div>

                    <!-- Answers -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3 text-secondary">
                                <i class="fa fa-list-ol me-2"></i> {{ __('messages.answers') }}
                                <span class="badge bg-info ms-2">{{ __('messages.min_2_max_4') }}</span>
                            </h5>

                            <div id="answersContainer">
                                @foreach ($question?->answers as $index => $answer)
                                    @php $i = $index + 1; @endphp
                                    <div class="answer-row row align-items-center mb-3">
                                        <div class="col-7">
                                            <input type="text" name="answers[{{ $i }}]"
                                                class="form-control answer-input"
                                                placeholder="{{ __('messages.answer') }} {{ $i }}"
                                                value="{{ old("answers.$i", $answer->answer) }}" required>
                                        </div>
                                        <div class="col-3 text-end">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="correct"
                                                    value="{{ $i }}"
                                                    {{ old('correct', $answer->is_correct) == 1 ? 'checked' : '' }} required>
                                                <label class="form-check-label fw-bold text-success">
                                                    <i class="fa fa-check-circle me-1"></i> {{ __('messages.correct') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-2 text-end">
                                            @if ($i > 2)
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-answer">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" id="addAnswerBtn" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fa fa-plus me-1"></i> {{ __('messages.add_answer') }}
                            </button>
                        </div>
                    </div>

                    <script>
                        let answerCount = {{ count($question?->answers) }};
                        const maxAnswers = 4;
                        const minAnswers = 2;

                        // Question type toggle
                        document.querySelectorAll('input[name="question_type"]').forEach(radio => {
                            radio.addEventListener('change', function() {
                                const textContainer = document.getElementById('text-question-container');
                                const imageContainer = document.getElementById('image-question-container');
                                const textInput = document.getElementById('question');
                                const imageInput = document.getElementById('question_image');

                                if (this.value === 'text') {
                                    textContainer.classList.remove('d-none');
                                    imageContainer.classList.add('d-none');
                                    textInput.required = true;
                                    imageInput.required = false;
                                } else {
                                    textContainer.classList.add('d-none');
                                    imageContainer.classList.remove('d-none');
                                    textInput.required = false;
                                    imageInput.required = false;
                                }
                            });
                        });

                        // Image preview
                        document.getElementById('question_image')?.addEventListener('change', function(e) {
                            const preview = document.getElementById('image-preview');
                            const file = e.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-width: 300px;">`;
                                }
                                reader.readAsDataURL(file);
                            } else {
                                preview.innerHTML = '';
                            }
                        });

                        document.getElementById('addAnswerBtn').addEventListener('click', function() {
                            if (answerCount < maxAnswers) {
                                answerCount++;
                                const container = document.getElementById('answersContainer');
                                const newRow = document.createElement('div');
                                newRow.className = 'answer-row row align-items-center mb-3';
                                newRow.innerHTML = `
                                    <div class="col-7">
                                        <input type="text" name="answers[${answerCount}]"
                                            class="form-control answer-input"
                                            placeholder="{{ __('messages.answer') }} ${answerCount}"
                                            required>
                                    </div>
                                    <div class="col-3 text-end">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="correct"
                                                value="${answerCount}" required>
                                            <label class="form-check-label fw-bold text-success">
                                                <i class="fa fa-check-circle me-1"></i> {{ __('messages.correct') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-2 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-answer">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                `;
                                container.appendChild(newRow);
                                updateAddButtonState();
                            }
                        });

                        document.addEventListener('click', function(e) {
                            if (e.target.closest('.remove-answer')) {
                                const row = e.target.closest('.answer-row');
                                if (answerCount > minAnswers) {
                                    row.remove();
                                    answerCount--;
                                    reindexAnswers();
                                    updateAddButtonState();
                                }
                            }
                        });

                        function reindexAnswers() {
                            const rows = document.querySelectorAll('.answer-row');
                            rows.forEach((row, index) => {
                                const newIndex = index + 1;
                                const input = row.querySelector('.answer-input');
                                const radio = row.querySelector('input[type=\"radio\"]');
                                const placeholder = row.querySelector('.answer-input');

                                input.name = `answers[${newIndex}]`;
                                radio.value = newIndex;
                                placeholder.placeholder = `{{ __('messages.answer') }} ${newIndex}`;
                            });
                        }

                        function updateAddButtonState() {
                            const btn = document.getElementById('addAnswerBtn');
                            if (answerCount >= maxAnswers) {
                                btn.disabled = true;
                                btn.classList.add('disabled');
                            } else {
                                btn.disabled = false;
                                btn.classList.remove('disabled');
                            }
                        }

                        // Initialize button state on load
                        updateAddButtonState();
                    </script>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> {{ __('messages.back') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i> {{ __('messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
            </div>
        </div>
    </div>
@endsection
