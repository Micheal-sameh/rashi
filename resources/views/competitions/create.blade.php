@extends('layouts.sideBar')

@section('content')
<div class="container py-4" style="max-width: 900px;">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-gradient-primary text-white rounded-top-4">
            <h4 class="mb-0"><i class="fas fa-trophy me-2"></i>{{ __('messages.create_competitions') }}</h4>
        </div>

        <div class="card-body p-4">
            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li><i class="fas fa-exclamation-circle me-1"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('competitions.store') }}" method="POST" enctype="multipart/form-data" class="row g-4">
                @csrf

                <!-- Name -->
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">{{ __('messages.name') }}</label>
                    <input type="text" name="name" class="form-control rounded-3 shadow-sm"
                           value="{{ old('name') }}" required>
                </div>

                <!-- Start At -->
                <div class="col-md-6">
                    <label for="start_at" class="form-label fw-semibold">{{ __('messages.start_at') }}</label>
                    <input type="date" name="start_at" class="form-control rounded-3 shadow-sm"
                           value="{{ old('start_at') }}" required>
                </div>

                <!-- End At -->
                <div class="col-md-6">
                    <label for="end_at" class="form-label fw-semibold">{{ __('messages.end_at') }}</label>
                    <input type="date" name="end_at" class="form-control rounded-3 shadow-sm"
                           value="{{ old('end_at') }}" required>
                </div>

                <!-- Image -->
                <div class="col-md-6">
                    <label for="image" class="form-label fw-semibold">{{ __('messages.image') }}</label>
                    <input type="file" name="image" class="form-control rounded-3 shadow-sm" accept="image/*">
                </div>

                <!-- Groups -->
                <div class="col-12">
                    <label class="form-label fw-semibold">{{ __('messages.select_groups') }}</label>
                    <div class="border rounded-3 p-3 shadow-sm bg-light"
                         style="max-height: 250px; overflow-y: auto;">
                        <div class="row row-cols-1 row-cols-md-2 g-2">
                            @foreach ($groups as $group)
                                <div class="col">
                                    <div class="form-check d-flex align-items-center p-2 border rounded-3 bg-white shadow-sm hover-card">
                                        <input class="form-check-input me-2"
                                               type="checkbox"
                                               name="groups[]"
                                               value="{{ $group->id }}"
                                               id="group_{{ $group->id }}">
                                        <label class="form-check-label fw-semibold" for="group_{{ $group->id }}">
                                            <i class="fas fa-users me-1 text-primary"></i>{{ $group->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @error('groups')
                        <div class="text-danger mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">
                        <i class="fas fa-plus me-2"></i>{{ __('messages.create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card-header {
        background: linear-gradient(135deg, #0d6efd, #3b82f6);
    }
    .hover-card:hover {
        background-color: #f1f5ff;
        transition: 0.2s;
    }
</style>
@endsection
