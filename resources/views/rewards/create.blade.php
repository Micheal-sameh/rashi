@extends('layouts.sideBar')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
        <div>
            <h1 class="fw-bold display-6 text-dark mb-2">{{ __('messages.create_reward') }}</h1>
            <p class="text-muted mb-0">{{ __('messages.add_new_reward_for_users') }}</p>
        </div>
        <a href="{{ route('rewards.index') }}" class="btn btn-outline-secondary btn-lg px-4 py-3 shadow-sm rounded-pill d-flex align-items-center gap-2">
            <i class="fa fa-arrow-left fa-lg"></i>
            <span class="fw-semibold">{{ __('messages.back_to_rewards') }}</span>
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fa fa-exclamation-triangle fa-lg"></i>
                        <h5 class="mb-0 fw-bold">{{ __('messages.validation_errors') }}</h5>
                    </div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-primary text-white border-0 py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fa fa-gift fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">{{ __('messages.new_reward_details') }}</h4>
                            <small class="opacity-75">{{ __('messages.fill_all_required_fields') }}</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">
                    <form action="{{ route('rewards.store') }}" method="POST" enctype="multipart/form-data" id="rewardForm">
                        @csrf

                        <div class="row g-4">
                            <!-- Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold mb-2">
                                        <i class="fa fa-tag me-2 text-primary"></i>
                                        {{ __('messages.reward_name') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fa fa-gift text-primary"></i>
                                        </span>
                                        <input type="text"
                                               class="form-control border-0 bg-light rounded-end"
                                               id="name"
                                               name="name"
                                               value="{{ old('name') }}"
                                               placeholder="{{ __('messages.enter_reward_name') }}"
                                               style="height: 56px;"
                                               required>
                                    </div>
                                    <small class="text-muted mt-1 d-block">{{ __('messages.reward_name_help') }}</small>
                                </div>
                            </div>

                            <!-- Points -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold mb-2">
                                        <i class="fa fa-star me-2 text-warning"></i>
                                        {{ __('messages.points_required') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fa fa-star text-warning"></i>
                                        </span>
                                        <input type="number"
                                               class="form-control border-0 bg-light rounded-end"
                                               id="points"
                                               name="points"
                                               value="{{ old('points') }}"
                                               min="0"
                                               placeholder="0"
                                               style="height: 56px;"
                                               required>
                                    </div>
                                    <small class="text-muted mt-1 d-block">{{ __('messages.points_help') }}</small>
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold mb-2">
                                        <i class="fa fa-box me-2 text-success"></i>
                                        {{ __('messages.initial_quantity') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fa fa-box text-success"></i>
                                        </span>
                                        <input type="number"
                                               class="form-control border-0 bg-light rounded-end"
                                               id="quantity"
                                               name="quantity"
                                               value="{{ old('quantity', 1) }}"
                                               min="1"
                                               placeholder="1"
                                               style="height: 56px;"
                                               required>
                                        <span class="input-group-text bg-light border-0">Units</span>
                                    </div>
                                    <small class="text-muted mt-1 d-block">{{ __('messages.quantity_help') }}</small>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold mb-2">
                                        <i class="fa fa-info-circle me-2 text-info"></i>
                                        {{ __('messages.status') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fa fa-circle text-info"></i>
                                        </span>
                                        <select class="form-select border-0 bg-light rounded-end"
                                                id="status"
                                                name="status"
                                                style="height: 56px;"
                                                required>
                                            <option value="" disabled selected>{{ __('messages.select_status') }}</option>
                                            @foreach (collect(App\Enums\RewardStatus::all())->except([3, 1]) as $enum)
                                                <option value="{{ $enum['value'] }}"
                                                        {{ old('status') == $enum['value'] ? 'selected' : '' }}
                                                        data-color="{{ $enum['value'] == \App\Enums\RewardStatus::ACTIVE ? 'success' : ($enum['value'] == \App\Enums\RewardStatus::CANCELLED ? 'danger' : 'secondary') }}">
                                                    {{ $enum['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="text-muted mt-1 d-block">{{ __('messages.status_help') }}</small>
                                </div>
                            </div>

                            <!-- Group -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold mb-2">
                                        <i class="fa fa-users me-2 text-purple"></i>
                                        {{ __('messages.assign_to_group') }}
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fa fa-users text-purple"></i>
                                        </span>
                                        <select class="form-select border-0 bg-light rounded-end"
                                                id="group_id"
                                                name="group_id"
                                                style="height: 56px;">
                                            <option value="" selected>{{ __('messages.select_group_optional') }}</option>
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}"
                                                        {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                                    {{ $group->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="text-muted mt-1 d-block">{{ __('messages.group_help') }}</small>
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label fw-semibold mb-2">
                                        <i class="fa fa-image me-2 text-danger"></i>
                                        {{ __('messages.reward_image') }}
                                    </label>

                                    <!-- Image Upload Area -->
                                    <div class="border-2 border-dashed rounded-4 p-5 text-center bg-light"
                                         id="imageUploadArea"
                                         style="cursor: pointer;">
                                        <div id="uploadPlaceholder">
                                            <i class="fa fa-cloud-upload fa-3x text-muted mb-3"></i>
                                            <h5 class="mb-2">{{ __('messages.upload_image') }}</h5>
                                            <p class="text-muted mb-0">{{ __('messages.drag_drop_or_click') }}</p>
                                            <small class="text-muted">{{ __('messages.max_size_5mb') }}</small>
                                        </div>
                                        <img id="imagePreview"
                                             class="img-fluid rounded-3 shadow-sm d-none mt-3"
                                             style="max-height: 200px;">
                                    </div>

                                    <!-- Hidden file input -->
                                    <input type="file"
                                           class="d-none"
                                           id="image"
                                           name="image"
                                           accept="image/*">
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-3 mt-5 pt-4 border-top">
                            <button type="reset" class="btn btn-outline-secondary rounded-pill px-5 py-2 fw-semibold flex-fill">
                                <i class="fa fa-redo me-2"></i>
                                {{ __('messages.reset') }}
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold flex-fill">
                                <i class="fa fa-plus-circle me-2"></i>
                                {{ __('messages.create_reward') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa fa-lightbulb me-2 text-warning"></i>
                        {{ __('messages.tips_for_great_rewards') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <div class="text-success">
                                    <i class="fa fa-check-circle"></i>
                                </div>
                                <div>
                                    <small class="fw-semibold">{{ __('messages.use_clear_images') }}</small>
                                    <p class="text-muted small mb-0">{{ __('messages.image_tip') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <div class="text-warning">
                                    <i class="fa fa-star"></i>
                                </div>
                                <div>
                                    <small class="fw-semibold">{{ __('messages.set_appropriate_points') }}</small>
                                    <p class="text-muted small mb-0">{{ __('messages.points_tip') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <div class="text-info">
                                    <i class="fa fa-box"></i>
                                </div>
                                <div>
                                    <small class="fw-semibold">{{ __('messages.start_with_small_quantity') }}</small>
                                    <p class="text-muted small mb-0">{{ __('messages.quantity_tip') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <div class="text-primary">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div>
                                    <small class="fw-semibold">{{ __('messages.use_groups_wisely') }}</small>
                                    <p class="text-muted small mb-0">{{ __('messages.group_tip') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-gradient-success text-white rounded-top-4 py-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-25 rounded-circle p-2">
                        <i class="fa fa-check fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-bold">{{ __('messages.reward_created') }}</h5>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fa fa-gift fa-4x text-success mb-3"></i>
                <h4 class="fw-bold mb-3">{{ __('messages.congratulations') }}</h4>
                <p class="text-muted mb-4">{{ __('messages.reward_created_successfully') }}</p>
                <div class="d-flex gap-2">
                    <a href="{{ route('rewards.index') }}" class="btn btn-outline-success rounded-pill px-4 py-2 flex-fill">
                        {{ __('messages.view_all_rewards') }}
                    </a>
                    <button type="button" class="btn btn-success rounded-pill px-4 py-2 flex-fill" onclick="resetForm()">
                        {{ __('messages.create_another') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="modal fade" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent shadow-none">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">{{ __('messages.creating_reward') }}...</span>
                </div>
                <h5 class="mt-3 text-dark">{{ __('messages.creating_reward') }}...</h5>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .card {
        transition: transform 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .border-dashed {
        border-style: dashed !important;
    }

    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        border-color: #667eea;
    }

    .input-group-text {
        transition: all 0.2s ease;
    }

    .input-group:focus-within .input-group-text {
        background-color: rgba(102, 126, 234, 0.1);
        border-color: #667eea;
    }

    .text-purple {
        color: #764ba2;
    }

    #imageUploadArea {
        transition: all 0.3s ease;
        border-color: #dee2e6;
    }

    #imageUploadArea:hover {
        border-color: #667eea !important;
        background-color: rgba(102, 126, 234, 0.05);
    }

    #imageUploadArea.dragover {
        border-color: #667eea !important;
        background-color: rgba(102, 126, 234, 0.1);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image Upload Handling
        const imageUploadArea = document.getElementById('imageUploadArea');
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');

        // Click to upload
        imageUploadArea.addEventListener('click', () => imageInput.click());

        // File selection
        imageInput.addEventListener('change', function(e) {
            handleImageFile(e.target.files[0]);
        });

        function handleImageFile(file) {
            if (file) {
                if (!file.type.startsWith('image/')) {
                    showToast('{{ __("messages.please_select_image_file") }}', 'danger');
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    showToast('{{ __("messages.file_size_exceeds_5mb") }}', 'danger');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                    uploadPlaceholder.classList.add('d-none');
                    imageUploadArea.style.borderStyle = 'solid';
                    imageUploadArea.style.backgroundColor = 'rgba(102, 126, 234, 0.05)';
                };
                reader.readAsDataURL(file);
            }
        }

        // Drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            imageUploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            imageUploadArea.addEventListener(eventName, () => {
                imageUploadArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            imageUploadArea.addEventListener(eventName, () => {
                imageUploadArea.classList.remove('dragover');
            }, false);
        });

        imageUploadArea.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                handleImageFile(files[0]);
            }
        });

        // Form submission
        const form = document.getElementById('rewardForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate required fields
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                showToast('{{ __("messages.please_fill_all_required_fields") }}', 'danger');
                return;
            }

            // Show loading overlay
            const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            loadingModal.show();

            // Submit form
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            })
            .then(response => {
                loadingModal.hide();
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success modal
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                } else {
                    showToast(data.message || '{{ __("messages.failed_to_create_reward") }}', 'danger');
                }
            })
            .catch(error => {
                loadingModal.hide();
                console.error('Error:', error);
                showToast('{{ __("messages.error_occurred") }}', 'danger');
            });
        });

        // Status select styling
        const statusSelect = document.getElementById('status');
        statusSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const color = selectedOption.dataset.color;
            if (color) {
                this.style.borderLeft = `4px solid var(--bs-${color})`;
            }
        });

        // Initialize status select if value exists
        if (statusSelect.value) {
            statusSelect.dispatchEvent(new Event('change'));
        }

        // Reset form function
        window.resetForm = function() {
            document.getElementById('rewardForm').reset();
            imagePreview.classList.add('d-none');
            uploadPlaceholder.classList.remove('d-none');
            imageUploadArea.style.borderStyle = 'dashed';
            imageUploadArea.style.backgroundColor = '';

            const successModal = bootstrap.Modal.getInstance(document.getElementById('successModal'));
            if (successModal) {
                successModal.hide();
            }

            // Focus on first field
            document.getElementById('name').focus();
        };

        // Toast notification function
        function showToast(message, type = 'success') {
            const container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-4';
            container.style.zIndex = '1080';

            const toast = document.createElement('div');
            toast.className = `toast border-0 shadow-lg rounded-3 bg-${type} text-white`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
                <div class="toast-body">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} fa-lg"></i>
                        <span>${message}</span>
                    </div>
                </div>
            `;

            container.appendChild(toast);
            document.body.appendChild(container);

            const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
            bsToast.show();

            toast.addEventListener('hidden.bs.toast', () => {
                container.remove();
            });
        }

        // Auto-calculate points based on name length (example feature)
        const nameInput = document.getElementById('name');
        const pointsInput = document.getElementById('points');

        nameInput.addEventListener('input', function() {
            if (!pointsInput.value) {
                // Example: Calculate points based on name length (customize as needed)
                const calculatedPoints = Math.min(1000, Math.max(10, this.value.length * 10));
                pointsInput.value = calculatedPoints;
            }
        });

        // Session messages
        @if (session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if (session('error'))
            showToast('{{ session('error') }}', 'danger');
        @endif
    });
</script>
@endsection