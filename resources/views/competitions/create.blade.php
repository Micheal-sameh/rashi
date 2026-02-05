@extends('layouts.sideBar')

@section('content')
<div class="container-fluid px-3 px-lg-5 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
    <div class="card shadow-soft border-0 rounded-4">
        <div class="card-header text-white rounded-top-4" style="background: var(--primary-gradient);">
            <h4 class="mb-0 d-flex align-items-center">
                <i class="fas fa-trophy me-3"></i>
                <div>
                    <div class="fw-bold">{{ __('messages.create_competitions') }}</div>
                    <small class="opacity-90">{{ __('messages.fill_competition_details') }}</small>
                </div>
            </h4>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('competitions.store') }}" method="POST" enctype="multipart/form-data" id="competitionForm">
                @csrf

                <div class="row g-4">
                    <!-- Name -->
                    <div class="col-md-12">
                        <label for="name" class="form-label fw-semibold">
                            <i class="fas fa-tag me-2 text-primary"></i>{{ __('messages.competition_name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name"
                            class="form-control form-control-lg @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            placeholder="{{ __('messages.enter_competition_name') }}"
                            required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Start At -->
                    <div class="col-md-6">
                        <label for="start_at" class="form-label fw-semibold">
                            <i class="fas fa-calendar-start me-2 text-success"></i>{{ __('messages.start_date') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="start_at" id="start_at"
                            class="form-control form-control-lg @error('start_at') is-invalid @enderror"
                            value="{{ old('start_at') }}" required>
                        @error('start_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- End At -->
                    <div class="col-md-6">
                        <label for="end_at" class="form-label fw-semibold">
                            <i class="fas fa-calendar-check me-2 text-danger"></i>{{ __('messages.end_date') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="end_at" id="end_at"
                            class="form-control form-control-lg @error('end_at') is-invalid @enderror"
                            value="{{ old('end_at') }}" required>
                        @error('end_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image -->
                    <div class="col-12">
                        <label for="image" class="form-label fw-semibold">
                            <i class="fas fa-image me-2 text-info"></i>{{ __('messages.competition_image') }}
                        </label>
                        <div class="input-group">
                            <input type="file" name="image" id="image"
                                class="form-control form-control-lg @error('image') is-invalid @enderror"
                                accept="image/*"
                                onchange="previewImage(event)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ __('messages.recommended_image_size') }}: 800x600px (JPG, PNG - Max: 2MB)
                        </small>

                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <img id="preview" src="" alt="Preview"
                                class="img-fluid rounded-3 shadow-sm"
                                style="max-height: 200px;">
                        </div>
                    </div>

                    <!-- Groups -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-users me-2 text-warning"></i>{{ __('messages.select_groups') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="card border-0 bg-light">
                            <div class="card-body p-3" style="max-height: 300px; overflow-y: auto;" id="groupsContainer">
                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                    @foreach ($groups as $group)
                                        <div class="col">
                                            <div class="group-checkbox-wrapper">
                                                <input class="btn-check group-checkbox"
                                                    type="checkbox"
                                                    name="groups[]"
                                                    value="{{ $group->id }}"
                                                    id="group_{{ $group->id }}"
                                                    {{ in_array($group->id, old('groups', [])) ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100 text-start group-label"
                                                    for="group_{{ $group->id }}">
                                                    <i class="fas fa-users me-2"></i>{{ $group->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @error('groups')
                            <div class="text-danger mt-1 small">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-1"></i>
                            {{ __('messages.select_multiple_groups_tip') }}
                        </small>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-12">
                        <div class="d-flex gap-3 justify-content-end mt-3">
                            <a href="{{ route('competitions.index') }}" class="btn btn-light btn-lg px-5">
                                <i class="fas fa-times me-2"></i>{{ __('messages.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5 hover-lift">
                                <i class="fas fa-plus-circle me-2"></i>{{ __('messages.create_competition') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
        </div>
    </div>
</div>

<script>
    // Wrap everything in try-catch to prevent white screen errors
    (function() {
        'use strict';

        // Image Preview
        window.previewImage = function(event) {
            try {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('preview');
                        const imagePreview = document.getElementById('imagePreview');
                        if (preview && imagePreview) {
                            preview.src = e.target.result;
                            imagePreview.style.display = 'block';
                        }
                    }
                    reader.onerror = function() {
                        console.error('Error reading file');
                    }
                    reader.readAsDataURL(file);
                }
            } catch (error) {
                console.error('Image preview error:', error);
            }
        }

        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Prevent event propagation issues with group checkboxes
                const groupLabels = document.querySelectorAll('.group-label');
                groupLabels.forEach(label => {
                    label.addEventListener('click', function(e) {
                        // Prevent any potential bubbling issues
                        e.stopPropagation();
                    });
                });

                // Prevent scroll issues when checking boxes at bottom
                const groupsContainer = document.getElementById('groupsContainer');
                if (groupsContainer) {
                    groupsContainer.addEventListener('click', function(e) {
                        // Prevent container click from interfering
                        if (e.target === this) {
                            e.stopPropagation();
                        }
                    });
                }

                // Form Validation
                const competitionForm = document.getElementById('competitionForm');
                if (competitionForm) {
                    competitionForm.addEventListener('submit', function(e) {
                        try {
                            const startDateInput = document.getElementById('start_at');
                            const endDateInput = document.getElementById('end_at');

                            if (startDateInput && endDateInput) {
                                const startDate = new Date(startDateInput.value);
                                const endDate = new Date(endDateInput.value);

                                if (endDate <= startDate) {
                                    e.preventDefault();
                                    alert('{{ __("messages.end_date_must_be_after_start") }}');
                                    return false;
                                }
                            }

                            // Check if at least one group is selected
                            const checkedGroups = document.querySelectorAll('input[name="groups[]"]:checked');
                            if (checkedGroups.length === 0) {
                                e.preventDefault();
                                alert('{{ __("messages.select_at_least_one_group") }}');
                                return false;
                            }
                        } catch (error) {
                            console.error('Form validation error:', error);
                            e.preventDefault();
                            return false;
                        }
                    });
                }
            } catch (error) {
                console.error('Initialization error:', error);
            }
        });
    })();
</script>

<style>
    /* Group checkbox styling */
    .group-checkbox-wrapper {
        position: relative;
    }

    .group-checkbox {
        position: absolute;
        clip: rect(0, 0, 0, 0);
        pointer-events: none;
    }

    .group-label {
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    .btn-check:checked + .btn-outline-primary {
        background: var(--primary-gradient);
        border-color: transparent;
        color: white;
    }

    .btn-check:focus + .btn-outline-primary {
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }

    /* Prevent layout shift on click */
    .group-label:active {
        transform: scale(0.98);
    }

    /* Smooth scrolling for groups container */
    #groupsContainer {
        scroll-behavior: smooth;
    }

    /* Ensure overflow works properly */
    #groupsContainer .card-body {
        overflow-x: hidden;
        overflow-y: auto;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
    }

    .form-control-lg {
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }

    /* Hover effects */
    .hover-lift {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    /* Prevent white screen on errors */
    body {
        background-color: #f8f9fa;
    }
</style>
@endsection
