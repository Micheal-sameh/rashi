@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold display-6 text-dark mb-2">{{ __('messages.social_media') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.manage_social_media_links') }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($socialMedia->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fa fa-share-alt fa-4x text-muted opacity-25 mb-3"></i>
                </div>
                <h3 class="text-muted mb-3">{{ __('messages.no_social_media_found') }}</h3>
                <p class="text-muted mb-4">{{ __('messages.no_social_media_configured') }}</p>
            </div>
        @else
            <!-- Social Media Table -->
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 fw-semibold text-muted">{{ __('messages.id') }}</th>
                                    <th class="py-3 fw-semibold text-muted">{{ __('messages.name') }}</th>
                                    <th class="py-3 fw-semibold text-muted">{{ __('messages.icon') }}</th>
                                    <th class="py-3 fw-semibold text-muted">{{ __('messages.link') }}</th>
                                    <th class="pe-4 py-3 fw-semibold text-muted text-end">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($socialMedia as $social)
                                    <tr class="border-bottom border-light">
                                        <td class="ps-4 py-3">{{ $social->id }}</td>
                                        <td class="py-3">
                                            <span class="fw-semibold">{{ $social->name }}</span>
                                        </td>
                                        <td class="py-3">
                                            <i class="fa {{ $social->icon }} fa-2x text-primary"></i>
                                        </td>
                                        <td class="py-3">
                                            <a href="{{ $social->link }}" target="_blank" class="text-decoration-none">
                                                {{ Str::limit($social->link, 50) }}
                                            </a>
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <a href="{{ route('social-media.edit', $social->id) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="fa fa-edit me-1"></i>
                                                {{ __('messages.edit') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
