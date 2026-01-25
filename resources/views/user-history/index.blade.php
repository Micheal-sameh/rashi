@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-primary">{{ __('messages.user_history') }}</h1>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('user-history.index') }}" class="row g-3 mb-4">
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

        @if($search && $user)
            <!-- User Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <img src="{{ $user->getFirstMediaUrl('profile_images') ?: asset('images/default.png') }}"
                             alt="{{ $user->name }}"
                             class="rounded-circle me-3"
                             style="width: 50px; height: 50px; object-fit: cover; border: 2px solid white;">
                        <div>
                            <h5 class="mb-0">{{ $user->name }}</h5>
                            <small>{{ $user->membership_code }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">{{ __('messages.current_points') }}</h6>
                                <h3 class="text-primary mb-0">{{ $user->points }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">{{ __('messages.total_credit') }}</h6>
                                <h3 class="text-success mb-0">+{{ $totalCredit }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">{{ __('messages.total_debit') }}</h6>
                                <h3 class="text-danger mb-0">-{{ $totalDebit }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">{{ __('messages.net_balance') }}</h6>
                                <h3 class="mb-0" style="color: {{ ($totalCredit - $totalDebit) >= 0 ? '#10b981' : '#ef4444' }}">
                                    {{ $totalCredit - $totalDebit }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Point History Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-history me-2"></i>{{ __('messages.point_history') }}
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
                                        <th>{{ __('messages.source') }}</th>
                                        <th>{{ __('messages.description') }}</th>
                                        <th class="text-end">{{ __('messages.credit') }}</th>
                                        <th class="text-end">{{ __('messages.debit') }}</th>
                                        <th class="text-end">{{ __('messages.balance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $runningBalance = 0; @endphp
                                    @forelse($pointHistory as $history)
                                        @php
                                            if ($history->type == 'credit') {
                                                $runningBalance += $history->points;
                                            } else {
                                                $runningBalance -= $history->points;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <span class="badge {{ $history->type == 'credit' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($history->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $history->source ?? 'N/A' }}</td>
                                            <td>{{ $history->description ?? '-' }}</td>
                                            <td class="text-end text-success fw-bold">
                                                {{ $history->type == 'credit' ? '+' . $history->points : '-' }}
                                            </td>
                                            <td class="text-end text-danger fw-bold">
                                                {{ $history->type == 'debit' ? '-' . $history->points : '-' }}
                                            </td>
                                            <td class="text-end fw-bold">{{ $runningBalance }}</td>
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
                                        <span class="badge {{ $history->type == 'credit' ? 'bg-success' : 'bg-danger' }}">
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
                                <p>{{ __('messages.no_history_found') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @elseif($search)
            <div class="text-center text-muted py-5">
                <i class="fas fa-user-slash fa-3x mb-3"></i>
                <p>{{ __('messages.user_not_found') }}</p>
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="fas fa-search fa-3x mb-3"></i>
                <p>{{ __('messages.enter_user_name_or_code') }}</p>
            </div>
        @endif
    </div>
@endsection
