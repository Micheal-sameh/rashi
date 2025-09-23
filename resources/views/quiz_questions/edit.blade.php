@extends('layouts.sideBar')

@section('content')
    <div class="container py-4" style="max-width: 900px;">
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
                <form action="{{ route('questions.update', $question->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Hidden Quiz ID -->
                    <input type="hidden" name="quiz_id" value="{{ $question->quiz_id }}">

                    <!-- Question -->
                    <div class="mb-3">
                        <label for="question" class="form-label fw-bold">
                            <i class="fa fa-question-circle text-info me-1"></i> {{ __('messages.question') }}
                        </label>
                        <input type="text" name="question" id="question" class="form-control form-control-lg"
                            value="{{ old('question', $question->question) }}" required>
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
                            </h5>

                            @foreach ($question?->answers as $index => $answer)
                                @php $i = $index + 1; @endphp
                                <div class="row align-items-center mb-3">
                                    <div class="col-8">
                                        <input type="text" name="answers[{{ $i }}]"
                                            id="answers[{{ $i }}]" class="form-control"
                                            placeholder="{{ __('messages.answer') }} {{ $i }}"
                                            value="{{ old("answers.$i", $answer->answer) }}" required>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="correct"
                                                id="correct_{{ $i }}" value="{{ $i }}"
                                                {{ old('correct', $answer->is_correct) == 1 ? 'checked' : '' }} required>
                                            <label class="form-check-label fw-bold text-success"
                                                for="correct_{{ $i }}">
                                                <i class="fa fa-check-circle me-1"></i> {{ __('messages.correct') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

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
@endsection
