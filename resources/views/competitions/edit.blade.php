@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card border-0 shadow-sm rounded-4 ">
            <div class="card-body p-4">
                <!-- Title -->
                <h3 class="fw-bold mb-4 text-primary">
                    <i class="fas fa-trophy me-2"></i>{{ __('messages.edit_competition') }}
                </h3>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Edit Form -->
                <form action="{{ route('competitions.update', $competition->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">{{ __('messages.name') }}</label>
                            <input type="text" name="name" class="form-control shadow-sm"
                                value="{{ old('name', $competition->name) }}" required>
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-3">
                            <label for="start_at" class="form-label fw-semibold">{{ __('messages.start_at') }}</label>
                            <input type="date" name="start_at" class="form-control shadow-sm"
                                value="{{ old('start_at', \Carbon\Carbon::parse($competition->start_at)->format('Y-m-d')) }}"
                                required>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-3">
                            <label for="end_at" class="form-label fw-semibold">{{ __('messages.end_at') }}</label>
                            <input type="date" name="end_at" class="form-control shadow-sm"
                                value="{{ old('end_at', \Carbon\Carbon::parse($competition->end_at)->format('Y-m-d')) }}"
                                required>
                        </div>

                        <!-- Image Upload -->
                        <div class="col-md-6">
                            <label for="image" class="form-label fw-semibold">{{ __('messages.image') }}</label>
                            <input type="file" name="image" class="form-control shadow-sm" accept="image/*"
                                onchange="previewImage(event)">
                            @if ($competition->hasMedia('competitions_images'))
                                <div class="mt-3">
                                    <img id="imagePreview"
                                        src="{{ $competition->getFirstMediaUrl('competitions_images') }}"
                                        alt="Competition Image" class="img-thumbnail shadow-sm" style="max-width: 220px;">
                                </div>
                            @else
                                <img id="imagePreview" class="mt-3 img-thumbnail shadow-sm d-none" style="max-width: 220px;"
                                    alt="Preview">
                            @endif
                        </div>

                        <!-- Group Selection -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">{{ __('messages.select_groups') }}</label>
                            <div class="border rounded-3 p-3 shadow-sm"
                                style="height: auto; max-height: 280px; overflow-y: auto;">
                                @foreach ($groups as $group)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="groups[]"
                                            value="{{ $group->id }}" id="group_{{ $group->id }}"
                                            {{ in_array($group->id, $competition->groups->pluck('id')->toArray()) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="group_{{ $group->id }}">
                                            {{ $group->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('competitions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="fas fa-save me-1"></i>{{ __('messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

@push('scripts')
    <script>
        function previewImage(event) {
            const preview = document.getElementById('imagePreview');
            preview.src = URL.createObjectURL(event.target.files[0]);
            preview.classList.remove('d-none');
        }
    </script>
            </div>
        </div>
    </div>
@endpush
