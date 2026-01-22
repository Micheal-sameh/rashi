@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold display-6 text-dark mb-2">{{ __('messages.edit_info_video') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.update_info_video_details') }}</p>
            </div>
            <a href="{{ route('info-videos.index') }}" class="btn btn-outline-secondary rounded-3">
                <i class="fa fa-arrow-left me-1"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('info-videos.update', $infoVideo->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $infoVideo->name) }}"
                                   placeholder="{{ __('messages.enter_name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('messages.status') }} <span class="text-danger">*</span></label>
                            <select name="appear" class="form-select @error('appear') is-invalid @enderror" required>
                                @foreach($appearanceStatuses as $status)
                                    <option value="{{ $status['value'] }}" {{ old('appear', $infoVideo->appear) == $status['value'] ? 'selected' : '' }}>
                                        {{ $status['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('appear')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('messages.link') }} <span class="text-danger">*</span></label>
                        <textarea name="link"
                                  rows="3"
                                  class="form-control @error('link') is-invalid @enderror"
                                  placeholder="{{ __('messages.enter_video_link') }}"
                                  required>{{ old('link', $infoVideo->link) }}</textarea>
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
