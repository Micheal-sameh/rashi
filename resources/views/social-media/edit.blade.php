@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <!-- Header Section -->
        <x-page-header icon="fa-share-alt" :title="__('messages.edit_social_media')" :subtitle="__('messages.update_social_media_link')">
            <x-slot:actions>
                <a href="{{ route('social-media.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> {{ __('messages.back') }}
                </a>
            </x-slot>
        </x-page-header>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('social-media.update', $socialMedia->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label rs-label-md">{{ __('messages.name') }}</label>
                            <input type="text" class="form-control" value="{{ $socialMedia->name }}" disabled>
                            <small class="text-muted">{{ __('messages.name_cannot_be_edited') }}</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label rs-label-md">{{ __('messages.icon') }}</label>
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
                        <label class="form-label rs-label-md">{{ __('messages.link') }} <span class="text-danger">*</span></label>
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
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i> {{ __('messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
