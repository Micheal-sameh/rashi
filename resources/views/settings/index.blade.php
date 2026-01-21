@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary mb-0">
                <i class="fa fa-cogs me-2"></i> {{ __('messages.Application Settings') }}
            </h2>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-3">
                <i class="fa fa-arrow-left me-1"></i> {{ __('messages.back') }}
            </a>
        </div>

        <!-- Settings Form -->
        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Settings Grid -->
            <div class="row g-4">
                @foreach ($settings as $setting)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">

                                <!-- Setting Header -->
                                <div class="mb-3">
                                    <h6 class="fw-bold text-primary text-capitalize mb-1">
                                        {{ str_replace('_', ' ', $setting->name) }}
                                    </h6>
                                    <span class="badge bg-light text-dark border">{{ $setting->type }}</span>
                                </div>

                                <!-- Setting Value -->
                                <div class="mb-3">
                                    @if ($setting->type === 'file')
                                        <input type="file" name="settings[{{ $setting->id }}][value]"
                                            class="form-control">

                                        @if ($setting->value)
                                            @php
                                                $url = $setting->getFirstMediaUrl('app_logo');
                                                $isImage = preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $url);
                                            @endphp
                                            <div class="mt-3 text-center">
                                                @if ($isImage)
                                                    {{-- <img src="{{ $url }}" alt="Setting Image"
                                                        class="rounded border"
                                                        style="width: 80px; height: 80px; object-fit: contain;"> --}}
                                                @else
                                                    <a href="{{ $url }}" target="_blank"
                                                        class="text-decoration-none">
                                                        <i class="fa fa-file me-1"></i> {{ __('messages.view_file') }}
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <input type="text" name="settings[{{ $setting->id }}][value]"
                                            value="{{ $setting->value }}" class="form-control">
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Save Button -->
            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">
                    <i class="fa fa-save me-1"></i> {{ __('messages.save') }}
                </button>
            </div>
        </form>

        <!-- Delete All Tokens Section -->
        <div class="mt-5">
            <div class="card border-danger shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="card-title text-danger mb-3">
                        <i class="fa fa-exclamation-triangle me-2"></i> {{ __('messages.danger_zone') }}
                    </h5>
                    <p class="card-text text-muted mb-4">
                        {{ __('messages.logout_all_mobiles') }}
                    </p>
                    <button type="button" class="btn btn-danger px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#deleteTokensModal">
                        <i class="fa fa-trash me-1"></i> {{ __('messages.logout_all_mobiles') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Tokens Modal -->
        <div class="modal fade" id="deleteTokensModal" tabindex="-1" aria-labelledby="deleteTokensModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="deleteTokensModalLabel">
                            <i class="fa fa-exclamation-triangle me-2"></i> {{ __('messages.confirm_delete_all_tokens') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('settings.deleteAllTokens') }}">
                        @csrf
                        <div class="modal-body">
                            <p class="text-muted mb-3">
                                {{ __('messages.delete_all_tokens_warning') }}
                            </p>
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">{{ __('messages.enter_password_to_confirm') }}</label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-trash me-1"></i> {{ __('messages.delete_all_api_tokens') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            transition: all 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }

        input[type="file"] {
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1.25rem;
            }
        }
    </style>
@endpush
