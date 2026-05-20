@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-primary">{{ __('messages.user_history') }}</h1>
        </div>

        <!-- Search Form -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('user-history.index') }}">
                    <div class="row g-3">
                        <div class="col-md-9 col-lg-10">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" id="search" class="form-control border-start-0"
                                    placeholder="{{ __('messages.search_by_name_or_code') }}"
                                    value="{{ $search ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fa fa-search me-1"></i>{{ __('messages.search') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($search && $user)
            <!-- User Info Card -->
            <div class="card shadow-sm mb-4 border-0 rounded-4">
                <div class="card-header card-header-primary rounded-top-4">
                    <div class="d-flex align-items-center gap-3 text-white">
                        <img src="{{ $user->getFirstMediaUrl('profile_images') ?: asset('images/default.png') }}"
                             alt="{{ $user->name }}"
                             class="rounded-circle flex-shrink-0"
                             style="width: 50px; height: 50px; object-fit: cover; border: 2px solid rgba(255,255,255,0.5);">
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $user->name }}</h5>
                            <small class="opacity-75">{{ $user->membership_code }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="text-center p-3 bg-gradient-primary rounded-3 text-white">
                                <div class="small opacity-75 mb-1">{{ __('messages.current_points') }}</div>
                                <div class="h4 fw-bold mb-0">{{ $user->points }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-center p-3 bg-gradient-success rounded-3 text-white">
                                <div class="small opacity-75 mb-1">{{ __('messages.total_credit') }}</div>
                                <div class="h4 fw-bold mb-0">+{{ $totalCredit }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-center p-3 bg-gradient-danger rounded-3 text-white">
                                <div class="small opacity-75 mb-1">{{ __('messages.total_debit') }}</div>
                                <div class="h4 fw-bold mb-0">-{{ $totalDebit }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            @php $net = $totalCredit - $totalDebit; @endphp
                            <div class="text-center p-3 rounded-3 text-white {{ $net >= 0 ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                                <div class="small opacity-75 mb-1">{{ __('messages.net_balance') }}</div>
                                <div class="h4 fw-bold mb-0">{{ $net }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Point History Table -->
            <div class="card shadow-sm">
                <div class="card-header card-header-primary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-history me-2"></i>{{ __('messages.point_history') }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <!-- Desktop Table View -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-header-primary">
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
                                                <span class="badge {{ in_array($history->type, $credit) ? 'bg-success' : 'bg-danger' }}">
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
