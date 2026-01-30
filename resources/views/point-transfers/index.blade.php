@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-primary">{{ __('messages.point_transfers') }}</h1>
            <a href="{{ route('point-transfers.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa fa-plus me-1"></i>{{ __('messages.new_transfer') }}
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search Filter Form -->
        <form method="GET" action="{{ route('point-transfers.index') }}" class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="search" class="form-label fw-semibold">{{ __('messages.search') }}</label>
                <input type="text" name="search" id="search" class="form-control"
                    placeholder="{{ __('messages.search_by_name_or_code') }}"
                    value="{{ request('search') }}">
            </div>

            <div class="col-md-2">
                <label for="family_code" class="form-label fw-semibold">{{ __('messages.family_code') }}</label>
                <input type="text" name="family_code" id="family_code" class="form-control"
                    placeholder="E1C1F001"
                    value="{{ request('family_code') }}">
            </div>

            <div class="col-md-2">
                <label for="date_from" class="form-label fw-semibold">{{ __('messages.from_date') }}</label>
                <input type="date" name="date_from" id="date_from" class="form-control"
                    value="{{ request('date_from') }}">
            </div>

            <div class="col-md-2">
                <label for="date_to" class="form-label fw-semibold">{{ __('messages.to_date') }}</label>
                <input type="date" name="date_to" id="date_to" class="form-control"
                    value="{{ request('date_to') }}">
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary shadow-sm me-2">
                    <i class="fa fa-search me-1"></i>{{ __('messages.search') }}
                </button>
                <a href="{{ route('point-transfers.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="fa fa-redo me-1"></i>{{ __('messages.reset') }}
                </a>
            </div>
        </form>

        <!-- Desktop Table View -->
        <div class="d-none d-md-block">
            <div class="table-responsive shadow-sm rounded-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.id') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.sender') }}</th>
                            <th>{{ __('messages.receiver') }}</th>
                            <th>{{ __('messages.points') }}</th>
                            <th>{{ __('messages.family_code') }}</th>
                            <th>{{ __('messages.reason') }}</th>
                            <th>{{ __('messages.created_by') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                            <tr>
                                <td>{{ $transfer->id }}</td>
                                <td>{{ $transfer->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $transfer->sender->name }}</div>
                                    <small class="text-muted">{{ $transfer->sender->membership_code }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $transfer->receiver->name }}</div>
                                    <small class="text-muted">{{ $transfer->receiver->membership_code }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $transfer->points }}</span>
                                </td>
                                <td><span class="badge bg-info">{{ $transfer->family_code }}</span></td>
                                <td>{{ Str::limit($transfer->reason, 30) }}</td>
                                <td>{{ $transfer->creator->name }}</td>
                                <td>
                                    <a href="{{ route('point-transfers.show', $transfer->id) }}"
                                        class="btn btn-info btn-sm shadow-sm">
                                        <i class="fa fa-eye me-1"></i>{{ __('messages.view') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">{{ __('messages.no_transfers_found') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="d-md-none">
            @forelse($transfers as $transfer)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">#{{ $transfer->id }}</h6>
                            <span class="badge bg-success">{{ $transfer->points }} pts</span>
                        </div>
                        <div class="mb-2">
                            <strong>{{ __('messages.sender') }}:</strong> {{ $transfer->sender->name }}<br>
                            <small class="text-muted">{{ $transfer->sender->membership_code }}</small>
                        </div>
                        <div class="mb-2">
                            <strong>{{ __('messages.receiver') }}:</strong> {{ $transfer->receiver->name }}<br>
                            <small class="text-muted">{{ $transfer->receiver->membership_code }}</small>
                        </div>
                        <div class="mb-2">
                            <strong>{{ __('messages.family_code') }}:</strong>
                            <span class="badge bg-info">{{ $transfer->family_code }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>{{ __('messages.reason') }}:</strong> {{ $transfer->reason }}
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">
                                {{ __('messages.created_by') }}: {{ $transfer->creator->name }} |
                                {{ $transfer->created_at->format('Y-m-d H:i') }}
                            </small>
                        </div>
                        <a href="{{ route('point-transfers.show', $transfer->id) }}"
                            class="btn btn-info btn-sm w-100">
                            <i class="fa fa-eye me-1"></i>{{ __('messages.view') }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">{{ __('messages.no_transfers_found') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $transfers->links() }}
        </div>
    </div>
@endsection
