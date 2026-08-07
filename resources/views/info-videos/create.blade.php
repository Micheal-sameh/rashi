@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <!-- Header Section -->
        <x-page-header icon="fa-video" :title="__('messages.create_info_video')" :subtitle="__('messages.add_new_info_video')">
            <x-slot:actions>
                <a href="{{ route('info-videos.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> {{ __('messages.back') }}
                </a>
            </x-slot>
        </x-page-header>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('info-videos.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label rs-label-md">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="{{ __('messages.enter_name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label rs-label-md">{{ __('messages.status') }} <span class="text-danger">*</span></label>
                            <select name="appear" class="form-select @error('appear') is-invalid @enderror" required>
                                @foreach($appearanceStatuses as $status)
                                    <option value="{{ $status['value'] }}" {{ old('appear', 1) == $status['value'] ? 'selected' : '' }}>
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
                        <label class="form-label rs-label-md">{{ __('messages.link') }} <span class="text-danger">*</span></label>
                        <textarea name="link"
                                  rows="3"
                                  class="form-control @error('link') is-invalid @enderror"
                                  placeholder="{{ __('messages.enter_video_link') }}"
                                  required>{{ old('link') }}</textarea>
                        @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i> {{ __('messages.create') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
