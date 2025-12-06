@extends('layouts.sideBar')

@section('content')
    <div class="container py-5" style="max-width: 600px;">

        <!-- Heading -->
        <div class="text-center mb-5">
            <h2 class="fw-bold display-6 text-gradient">🎁 {{ __('messages.create_reward') }}</h2>
            <p class="text-muted">Add a new reward for your users in a few simple steps</p>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <strong>{{ __('messages.whoops') }}</strong> {{ __('messages.input_problems') }}
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Form Card -->
        <div class="card shadow-lg rounded-5 overflow-hidden border-0">
            <div class="card-header text-center text-blue p-4 bg-gradient-to-right">
                <h5 class="fw-bold mb-0"><i class="fa fa-gift me-2"></i>Reward Details</h5>
            </div>

            <div class="card-body p-5 bg-white">

                <form action="{{ route('rewards.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Name -->
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control rounded-pill" id="name" placeholder="Reward Name"
                            name="name" value="{{ old('name') }}" required>
                        <label for="name"><i class="fa fa-tag me-1"></i> {{ __('messages.name') }}</label>
                    </div>

                    <!-- Quantity -->
                    <div class="form-floating mb-4">
                        <input type="number" class="form-control rounded-pill" id="quantity" placeholder="Quantity"
                            name="quantity" value="{{ old('quantity') }}" min="1" required>
                        <label for="quantity"><i class="fa fa-boxes me-1"></i> {{ __('messages.quantity') }}</label>
                    </div>

                    <!-- Status -->
                    <div class="form-floating mb-4">
                        <select class="form-select rounded-pill" id="status" name="status" required>
                            <option value="" disabled selected>{{ __('messages.select') }}</option>
                            @foreach (collect(App\Enums\RewardStatus::all())->except([1]) as $enum)
                                <option value="{{ $enum['value'] }}"
                                    {{ old('status') == $enum['value'] ? 'selected' : '' }}>
                                    {{ $enum['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <label for="status"><i class="fa fa-info-circle me-1"></i> {{ __('messages.status') }}</label>
                    </div>

                    <!-- Points -->
                    <div class="form-floating mb-4">
                        <input type="number" class="form-control rounded-pill" id="points" placeholder="Points"
                            name="points" value="{{ old('points') }}" min="0" required>
                        <label for="points"><i class="fa fa-star me-1 text-warning"></i>
                            {{ __('messages.points') }}</label>
                    </div>

                    <!-- Group -->
                    <div class="form-floating mb-4">
                        <select class="form-select rounded-pill" id="group_id" name="group_id">
                            <option value="" selected>{{ __('messages.select_group') }}</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}"
                                    {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="group_id"><i class="fa fa-users me-1"></i> {{ __('messages.group') }}</label>
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-4">
                        <label class="fw-semibold mb-2">{{ __('messages.image') }}</label>
                        <input type="file" class="form-control rounded-pill" id="image" name="image"
                            accept="image/*">
                        <div class="mt-3 text-center">
                            <img id="preview" src="#" class="img-fluid rounded-3 d-none shadow-sm"
                                style="max-height: 200px;">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-gradient w-100 py-2 rounded-pill fw-semibold fs-5">
                        <i class="fa fa-plus-circle me-2"></i> {{ __('messages.create') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* Gradient header */
        .bg-gradient-to-right {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
        }

        /* Gradient button */
        .btn-gradient {
            background: linear-gradient(90deg, #2575fc, #6a11cb);
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        /* Card hover effect */
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        /* Floating label icons */
        .form-floating>label i {
            margin-right: 5px;
        }

        /* Image preview */
        #preview {
            border: 1px solid #ddd;
            padding: 5px;
            background-color: #fff;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .card-body {
                padding: 2rem 1.5rem;
            }

            .form-floating>label {
                font-size: 0.85rem;
            }

            .btn-gradient {
                font-size: 0.95rem;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Image preview
        const imageInput = document.getElementById('image');
        const preview = document.getElementById('preview');
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            }
        });
    </script>
@endsection
