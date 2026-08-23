@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <x-page-header icon="fa-house-user" :title="__('messages.families')"
            :subtitle="__('messages.total_families') . ': ' . number_format($totalFamilies)" />

        <!-- Total Families Card -->
        <div class="row g-3 mb-4">
            <div class="col-md-4 col-sm-6">
                <div class="rs-stat-card tone-primary">
                    <div class="rs-stat-top">
                        <span class="rs-label-md">{{ __('messages.total_families') }}</span>
                        <div class="rs-stat-icon"><i class="fas fa-home"></i></div>
                    </div>
                    <div class="rs-stat-value">{{ number_format($totalFamilies) }}</div>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="card rounded-4 shadow-soft border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('families.index') }}" class="row g-3">
                    <div class="col-md-8">
                        <label for="search" class="rs-label-md form-label">{{ __('messages.search') }}</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="{{ __('messages.search_by_name_or_code') }}"
                            value="{{ $search ?? '' }}" required>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search me-1"></i>{{ __('messages.search') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($search && count($families) > 0)
            <!-- Families List -->
            <div class="row g-3">
                @foreach($families as $family)
                    <div class="col-md-6 col-lg-4">
                        <div class="card rounded-4 shadow-soft hover-lift border-0 h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="rs-title-lg mb-0">
                                        <i class="fas fa-users me-2" style="color: var(--color-primary);"></i>{{ __('messages.family') }}: {{ $family['code'] }}
                                    </h5>
                                    <span class="badge" style="background: var(--color-on-primary-container); color: var(--color-primary);">
                                        {{ count($family['members']) }} {{ __('messages.members') }}
                                    </span>
                                </div>
                                <ul class="list-unstyled mb-3 flex-grow-1">
                                    @foreach($family['members'] as $member)
                                        <li class="mb-1">
                                            <i class="fas fa-user me-2" style="color: var(--color-primary);"></i>
                                            {{ $member->name }} ({{ $member->membership_code }})
                                        </li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('families.show', $family['code']) }}"
                                   class="btn btn-primary w-100">
                                    <i class="fa fa-eye me-1"></i>{{ __('messages.view_details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif($search)
            <div class="card rounded-4 shadow-soft border-0">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <p class="rs-body-lg">{{ __('messages.no_families_found') }}</p>
                </div>
            </div>
        @else
            <div class="card rounded-4 shadow-soft border-0">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <p class="rs-body-lg">{{ __('messages.enter_search_to_find_families') }}</p>
                </div>
            </div>
        @endif
    </div>
@endsection
