@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <x-page-header icon="fa-history" :title="__('messages.user_history')" />

        <!-- Search Form -->
        <div class="card rounded-4 shadow-soft border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('user-history.index') }}" class="row g-3">
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

        @if($search && $user)
            <!-- User Info Card -->
            <div class="card rounded-4 shadow-soft border-0 mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <img src="{{ $user->getFirstMediaUrl('profile_images') ?: asset('images/default.png') }}"
                             alt="{{ $user->name }}"
                             class="rounded-circle me-3"
                             style="width: 50px; height: 50px; object-fit: cover; border: 2px solid var(--color-outline-variant);">
                        <div>
                            <h5 class="rs-title-lg mb-0">{{ $user->name }}</h5>
                            <small class="text-muted">{{ $user->membership_code }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <div class="rs-stat-card tone-primary">
                                <div class="rs-stat-top">
                                    <span class="rs-label-md">{{ __('messages.current_points') }}</span>
                                    <div class="rs-stat-icon"><i class="fas fa-coins"></i></div>
                                </div>
                                <div class="rs-stat-value">{{ $user->points }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="rs-stat-card tone-success">
                                <div class="rs-stat-top">
                                    <span class="rs-label-md">{{ __('messages.total_credit') }}</span>
                                    <div class="rs-stat-icon"><i class="fas fa-arrow-up"></i></div>
                                </div>
                                <div class="rs-stat-value">+{{ $totalCredit }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="rs-stat-card tone-error">
                                <div class="rs-stat-top">
                                    <span class="rs-label-md">{{ __('messages.total_debit') }}</span>
                                    <div class="rs-stat-icon"><i class="fas fa-arrow-down"></i></div>
                                </div>
                                <div class="rs-stat-value">-{{ $totalDebit }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="rs-stat-card {{ ($totalCredit - $totalDebit) >= 0 ? 'tone-success' : 'tone-error' }}">
                                <div class="rs-stat-top">
                                    <span class="rs-label-md">{{ __('messages.net_balance') }}</span>
                                    <div class="rs-stat-icon"><i class="fas fa-scale-balanced"></i></div>
                                </div>
                                <div class="rs-stat-value">{{ $totalCredit - $totalDebit }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Point History Table -->
            <div class="card rounded-4 shadow-soft border-0">
                <div class="card-header">
                    <h5 class="rs-title-lg mb-0">
                        <i class="fas fa-history me-2" style="color: var(--color-primary);"></i>{{ __('messages.point_history') }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <!-- Desktop Table View -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('messages.date') }}</th>
                                        <th>{{ __('messages.type') }}</th>
                                        <th class="text-end">{{ __('messages.credit') }}</th>
                                        <th class="text-end">{{ __('messages.debit') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $runningBalance = 0; @endphp
                                    @forelse($pointHistory as $history)
                                        @php
                                            $credit = ['Bonus', 'Return', 'Quiz'];
                                        @endphp
                                        <tr>
                                            <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <span class="badge" style="{{ in_array($history->type, $credit) ? 'background: var(--color-success-container); color: var(--color-on-success-container);' : 'background: var(--color-error-container); color: var(--color-on-error-container);' }}">
                                                    {{ ucfirst($history->type) }}
                                                </span>
                                            </td>
                                            <td class="text-end text-success fw-bold">
                                                {{ $history->type == in_array($history->type, $credit) ? '+' . $history->amount : '-' }}
                                            </td>
                                            <td class="text-end text-danger fw-bold">
                                                {{ $history->type == !in_array($history->type, $credit) ? '-' . $history->amount : '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                {{ __('messages.no_history_found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-md-none p-3">
                        @php $runningBalance = 0; @endphp
                        @forelse($pointHistory as $history)
                            @php
                                if ($history->type == 'credit') {
                                    $runningBalance += $history->points;
                                } else {
                                    $runningBalance -= $history->points;
                                }
                            @endphp
                            <div class="card mb-3 border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge" style="{{ $history->type == 'credit' ? 'background: var(--color-success-container); color: var(--color-on-success-container);' : 'background: var(--color-error-container); color: var(--color-on-error-container);' }}">
                                            {{ ucfirst($history->type) }}
                                        </span>
                                        <small class="text-muted">{{ $history->created_at->format('Y-m-d H:i') }}</small>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted">{{ __('messages.source') }}:</small>
                                        <div>{{ $history->source ?? 'N/A' }}</div>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted">{{ __('messages.description') }}:</small>
                                        <div>{{ $history->description ?? '-' }}</div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <small class="text-muted">{{ __('messages.amount') }}:</small>
                                            <div class="fw-bold {{ $history->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                                {{ $history->type == 'credit' ? '+' : '-' }}{{ $history->points }}
                                            </div>
                                        </div>
                                        <div>
                                            <small class="text-muted">{{ __('messages.balance') }}:</small>
                                            <div class="fw-bold">{{ $runningBalance }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p class="rs-body-lg">{{ __('messages.no_history_found') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @elseif($search)
            <div class="card rounded-4 shadow-soft border-0">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-user-slash fa-3x mb-3"></i>
                    <p class="rs-body-lg">{{ __('messages.user_not_found') }}</p>
                </div>
            </div>
        @else
            <div class="card rounded-4 shadow-soft border-0">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <p class="rs-body-lg">{{ __('messages.enter_user_name_or_code') }}</p>
                </div>
            </div>
        @endif
    </div>
@endsection
