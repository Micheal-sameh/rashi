@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">{{ __('messages.edit_quiz') }}</h2>
            <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-1"></i> {{ __('messages.back') }}
            </a>
        </div>

        {{-- Error messages --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i> {{ __('messages.validation_error') }}
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <form action="{{ route('quizzes.update', $quiz->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold">{{ __('messages.name') }}</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $quiz->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="date" class="form-label fw-bold">{{ __('messages.date') }}</label>
                            <input type="date" name="date" id="date"
                                class="form-control @error('date') is-invalid @enderror"
                                value="{{ old('date', \Carbon\Carbon::parse($quiz->date)->format('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="competition" class="form-label fw-bold">{{ __('messages.competition') }}</label>
                            <input type="text" name="competition" id="competition" class="form-control"
                                value="{{ $quiz->competition->name ?? '-' }}" disabled>
                        </div>

                        <div class="col-md-12">
                            <label for="help" class="form-label fw-bold">Help URL <span class="text-muted">(Optional)</span></label>
                            <input type="url" name="help" id="help"
                                class="form-control @error('help') is-invalid @enderror"
                                value="{{ old('help', $quiz->help) }}"
                                placeholder="https://example.com/help">
                            <small class="text-muted">Enter a help URL for this quiz (optional)</small>
                            @error('help')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa fa-save me-1"></i> {{ __('messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const originalDate = "{{ \Carbon\Carbon::parse($quiz->date)->format('Y-m-d') }}";

        const form = document.querySelector('form[action="{{ route('quizzes.update', $quiz->id) }}"]');

        if (form) {
            form.addEventListener('submit', function(e) {
                const dateInput = document.querySelector('input[name="date"]');

                console.log('Original Date:', originalDate);
                console.log('Current Date:', dateInput ? dateInput.value : 'not found');

                // Remove date input if it hasn't changed
                if (dateInput && dateInput.value === originalDate) {
                    console.log('Removing date from submission');
                    dateInput.removeAttribute('name');
                }
            });
        }
    });
</script>
@endsection
