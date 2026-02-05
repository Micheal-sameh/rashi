@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-primary">{{ __('messages.families') }}</h1>
        </div>

        <!-- Total Families Card -->
        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 opacity-75">{{ __('messages.total_families') }}</h6>
                                <h2 class="mb-0 fw-bold">{{ number_format($totalFamilies) }}</h2>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fas fa-home"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('families.index') }}" class="row g-3 mb-4">
            <div class="col-md-8">
                <label for="search" class="form-label fw-semibold">{{ __('messages.search') }}</label>
                <input type="text" name="search" id="search" class="form-control"
                    placeholder="{{ __('messages.search_by_name_or_code') }}"
                    value="{{ $search ?? '' }}" required>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary shadow-sm">
                    <i class="fa fa-search me-1"></i>{{ __('messages.search') }}
                </button>
            </div>
        </form>

        @if($search && count($families) > 0)
            <!-- Families List -->
            <div class="row">
                @foreach($families as $family)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card shadow-sm hover-lift h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-users me-2"></i>{{ __('messages.family') }}: {{ $family['code'] }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">
                                    <strong>{{ __('messages.members') }}:</strong> {{ count($family['members']) }}
                                </p>
                                <ul class="list-unstyled mb-3">
                                    @foreach($family['members'] as $member)
                                        <li class="mb-1">
                                            <i class="fas fa-user text-primary me-2"></i>
                                            {{ $member->name }} ({{ $member->membership_code }})
                                        </li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('families.show', $family['code']) }}"
                                   class="btn btn-info w-100">
                                    <i class="fa fa-eye me-1"></i>{{ __('messages.view_details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif($search)
            <div class="text-center text-muted py-5">
                <i class="fas fa-search fa-3x mb-3"></i>
                <p>{{ __('messages.no_families_found') }}</p>
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="fas fa-search fa-3x mb-3"></i>
                <p>{{ __('messages.enter_search_to_find_families') }}</p>
            </div>
        @endif
    </div>
@endsection
