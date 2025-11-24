@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="mb-0 fw-bold text-primary">
                <i class="fas fa-bell me-2"></i> Notifications
            </h2>

            <a href="{{ route('notifications.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus me-1"></i> Send Notification
            </a>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filters --}}
        <div class="card mb-3 shadow-sm border-0 rounded-3">
            <div class="card-body p-3">
                <form class="row g-2" method="GET" action="{{ route('notifications.index') }}">
                    <div class="col-md-6">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search message or type...">
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="success" @selected(request('type') == 'success')>Success</option>
                            <option value="warning" @selected(request('type') == 'warning')>Warning</option>
                            <option value="error" @selected(request('type') == 'error')>Error</option>
                            <option value="info" @selected(request('type') == 'info')>Info</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-end">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Message</th>
                                <th style="width: 120px;">Type</th>
                                <th style="width: 180px;">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $notification)
                                <tr class="text-center">
                                    <td class="fw-semibold">{{ $notification->id }}</td>
                                    <td class="text-start">
                                        <span class="d-block text-truncate" style="max-width: 400px;">
                                            {{ $notification->message }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $badgeColors = [
                                                'success' => 'success',
                                                'warning' => 'warning',
                                                'error' => 'danger',
                                                'info' => 'info',
                                            ];
                                        @endphp
                                        <span
                                            class="badge bg-{{ $badgeColors[$notification->type] ?? 'secondary' }} px-3 py-2 text-uppercase">
                                            {{ $notification->type }}
                                        </span>
                                    </td>
                                    <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No notifications found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-center pt-2">
                @if ($notifications->hasPages())
                    <nav>
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($notifications->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $notifications->previousPageUrl() }}"
                                        rel="prev">&laquo;</a>
                                </li>
                            @endif

                            @php
                                $current = $notifications->currentPage();
                                $last = $notifications->lastPage();
                                $start = max($current - 2, 2);
                                $end = min($current + 2, $last - 1);
                            @endphp

                            {{-- First page --}}
                            <li class="page-item {{ $current === 1 ? 'active' : '' }}">
                                <a class="page-link" href="{{ $notifications->url(1) }}">1</a>
                            </li>

                            {{-- Dots before start --}}
                            @if ($start > 2)
                                <li class="page-item disabled"><span class="page-link">…</span></li>
                            @endif

                            {{-- Page range --}}
                            @for ($page = $start; $page <= $end; $page++)
                                <li class="page-item {{ $current === $page ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $notifications->url($page) }}">{{ $page }}</a>
                                </li>
                            @endfor

                            {{-- Dots after end --}}
                            @if ($end < $last - 1)
                                <li class="page-item disabled"><span class="page-link">…</span></li>
                            @endif

                            {{-- Last page --}}
                            @if ($last > 1)
                                <li class="page-item {{ $current === $last ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $notifications->url($last) }}">{{ $last }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($notifications->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $notifications->nextPageUrl() }}"
                                        rel="next">&raquo;</a>
                                </li>
                            @else
                                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>

        </div>
    </div>
@endsection
