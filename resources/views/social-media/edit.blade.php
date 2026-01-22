@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold display-6 text-dark mb-2">{{ __('messages.edit_social_media') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.update_social_media_link') }}</p>
            </div>
            <a href="{{ route('social-media.index') }}" class="btn btn-outline-secondary rounded-3">
                <i class="fa fa-arrow-left me-1"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('social-media.update', $socialMedia->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('messages.name') }}</label>
                            <input type="text" class="form-control" value="{{ $socialMedia->name }}" disabled>
                            <small class="text-muted">{{ __('messages.name_cannot_be_edited') }}</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('messages.icon') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa {{ $socialMedia->icon }} fa-lg"></i>
                                </span>
                                <input type="text" class="form-control" value="{{ $socialMedia->icon }}" disabled>
                            </div>
                            <small class="text-muted">{{ __('messages.icon_cannot_be_edited') }}</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('messages.link') }} <span class="text-danger">*</span></label>
                        <input type="url"
                               name="link"
                               class="form-control @error('link') is-invalid @enderror"
                               value="{{ old('link', $socialMedia->link) }}"
                               placeholder="https://example.com"
                               required>
                        @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">
                            <i class="fa fa-save me-1"></i> {{ __('messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
