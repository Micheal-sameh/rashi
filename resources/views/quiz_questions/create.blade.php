@extends('layouts.sideBar')

@section('content')
<div class="container" style="width: 90%;">

    <!-- Heading Section -->
    <h2 class="mb-4">{{ __('messages.create_questions') }}</h2>

    <!-- Error Handling Section -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Question Creation Form -->
    <form action="{{ route('questions.store') }}" method="POST">
        @csrf

        <!-- Hidden Quiz ID Field -->
        <input type="hidden" name="quiz_id" value="{{ $quiz_id }}">

        <!-- Question Input -->
        <div class="mb-3">
            <label for="question" class="form-label">{{ __('messages.question') }}</label>
            <input type="text" name="question" id="question" class="form-control" value="{{ old('question') }}" required>
        </div>

        <!-- Points Input -->
        <div class="mb-3">
            <label for="points" class="form-label">{{ __('messages.points') }}</label>
            <input type="number" name="points" id="points" class="form-control" value="{{ old('points') }}" min="1" required>
        </div>

        <!-- Answer Inputs (Dynamic Answers) -->
        @for ($i = 1; $i <= 4; $i++)
            <div class="mb-3 row align-items-center">
                <label for="answers[{{ $i }}]" class="col-sm-2 col-form-label">
                    {{ __('messages.answer') }} {{ $i }}
                </label>
                <div class="col-sm-8">
                    <input type="text" name="answers[{{ $i }}]" id="answers[{{ $i }}]" class="form-control"
                           value="{{ old("answers.$i") }}" required>
                </div>
                <div class="col-sm-2 text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="correct" id="correct_{{ $i }}"
                               value="{{ $i }}" {{ old('correct') == $i ? 'checked' : '' }} required>
                        <label class="form-check-label" for="correct_{{ $i }}">
                            {{ __('messages.correct') }}
                        </label>
                    </div>
                </div>
            </div>
        @endfor

        <!-- Form Submission Buttons -->
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            <a href="{{ route('questions.index') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
        </div>
    </form>

</div>
@endsection
