@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-primary">{{ __('messages.bonus_penalties') }}</h1>
            <a href="{{ route('bonus-penalties.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa fa-plus me-1"></i>{{ __('messages.add_bonus_penalty') }}
            </a>
        </div>

        <!-- Search Filter Form -->
        <form method="GET" action="{{ route('bonus-penalties.index') }}" class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="search" class="form-label fw-semibold">{{ __('messages.search') }}</label>
                <input type="text" name="search" id="search" class="form-control"
                    placeholder="{{ __('messages.search_by_name_or_code') }}"
                    value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <label for="created_by" class="form-label fw-semibold">{{ __('messages.created_by') }}</label>
                <select name="created_by" id="created_by" class="form-select">
                    <option value="">{{ __('messages.all') }}</option>
                    @foreach (App\Models\User::whereHas('bonusPenaltiesCreated')->orderBy('name')->get() as $creator)
                        <option value="{{ $creator->id }}" {{ request('created_by') == $creator->id ? 'selected' : '' }}>
                            {{ $creator->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="user_id" class="form-label fw-semibold">{{ __('messages.user_name') }}</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">{{ __('messages.all') }}</option>
                    @foreach (App\Models\User::orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary shadow-sm me-2">
                    <i class="fa fa-search me-1"></i>{{ __('messages.search') }}
                </button>
                <a href="{{ route('bonus-penalties.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="fa fa-redo me-1"></i>{{ __('messages.reset') }}
                </a>
            </div>
        </form>

        <!-- Desktop Table View -->
        <div class="d-none d-md-block">
            <div class="table-responsive shadow-sm rounded-4 ">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.user_name') }}</th>
                            <th>{{ __('messages.type') }}</th>
                            <th>{{ __('messages.points') }}</th>
                            <th>{{ __('messages.reason') }}</th>
                            <th>{{ __('messages.creator') }}</th>
                            <th>{{ __('messages.approved_by') }}</th>
                            <th>{{ __('messages.created_at') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bonusPenalties as $bonusPenalty)
                            <tr>
                                <td>
                                    <span class="text-primary user-detail" style="cursor:pointer;"
                                        data-name="{{ $bonusPenalty->user->name ?? '' }}"
                                        data-membership_code="{{ $bonusPenalty->user->membership_code ?? '' }}"
                                        data-phone="{{ $bonusPenalty->user->phone ?? '' }}">
                                        {{ $bonusPenalty->user->name ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $bonusPenalty->type == \App\Enums\BonusPenaltyType::BONUS ? 'bg-success' : 'bg-danger' }}">
                                        {{ \App\Enums\BonusPenaltyType::getStringValue($bonusPenalty->type) }}
                                    </span>
                                </td>
                                <td>{{ $bonusPenalty->points }}</td>
                                <td>{{ $bonusPenalty->reason }}</td>
                                <td>{{ $bonusPenalty->creator->name ?? '' }}</td>
                                <td>{{ $bonusPenalty->approver->name ?? '-' }}</td>
                                <td>{{ $bonusPenalty->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('bonus-penalties.show', $bonusPenalty->id) }}"
                                        class="btn btn-info btn-sm shadow-sm">
                                        <i class="fa fa-eye me-1"></i>{{ __('messages.view') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    {{ __('messages.no_bonus_penalties_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="d-md-none">
            @forelse($bonusPenalties as $bonusPenalty)
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="text-primary user-detail fw-semibold" style="cursor:pointer;"
                                data-name="{{ $bonusPenalty->user->name ?? '' }}"
                                data-membership_code="{{ $bonusPenalty->user->membership_code ?? '' }}"
                                data-phone="{{ $bonusPenalty->user->phone ?? '' }}">
                                {{ $bonusPenalty->user->name ?? '' }}
                            </span>
                            <span
                                class="badge {{ $bonusPenalty->type == \App\Enums\BonusPenaltyType::BONUS ? 'bg-success' : 'bg-danger' }}">
                                {{ \App\Enums\BonusPenaltyType::getStringValue($bonusPenalty->type) }}
                            </span>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">{{ __('messages.points') }}</small>
                                <div class="fw-semibold">{{ $bonusPenalty->points }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">{{ __('messages.creator') }}</small>
                                <div class="fw-semibold">{{ $bonusPenalty->creator->name ?? '' }}</div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">{{ __('messages.reason') }}</small>
                            <div>{{ $bonusPenalty->reason }}</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $bonusPenalty->created_at->format('Y-m-d H:i') }}</small>
                            <a href="{{ route('bonus-penalties.show', $bonusPenalty->id) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-eye me-1"></i>{{ __('messages.view') }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                    <p>{{ __('messages.no_bonus_penalties_found') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center pt-2">
            @if ($bonusPenalties->hasPages())
                <nav>
                    <ul class="pagination">
                        {{-- Previous Page Link --}}
                        @if ($bonusPenalties->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $bonusPenalties->previousPageUrl() }}"
                                    rel="prev">&laquo;</a>
                            </li>
                        @endif

                        @php
                            $current = $bonusPenalties->currentPage();
                            $last = $bonusPenalties->lastPage();
                            $start = max($current - 2, 2);
                            $end = min($current + 2, $last - 1);
                        @endphp

                        {{-- First page --}}
                        <li class="page-item {{ $current === 1 ? 'active' : '' }}">
                            <a class="page-link" href="{{ $bonusPenalties->url(1) }}">1</a>
                        </li>

                        {{-- Dots before start --}}
                        @if ($start > 2)
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        @endif

                        {{-- Page range --}}
                        @for ($page = $start; $page <= $end; $page++)
                            <li class="page-item {{ $current === $page ? 'active' : '' }}">
                                <a class="page-link" href="{{ $bonusPenalties->url($page) }}">{{ $page }}</a>
                            </li>
                        @endfor

                        {{-- Dots after end --}}
                        @if ($end < $last - 1)
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        @endif

                        {{-- Last page --}}
                        @if ($last > 1)
                            <li class="page-item {{ $current === $last ? 'active' : '' }}">
                                <a class="page-link" href="{{ $bonusPenalties->url($last) }}">{{ $last }}</a>
                            </li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($bonusPenalties->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $bonusPenalties->nextPageUrl() }}" rel="next">&raquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                        @endif
                    </ul>
                </nav>
            @endif
        </div>

    </div>

    <!-- Info Modal -->
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content rounded-4 shadow-sm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="infoModalLabel">{{ __('messages.details') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle modal info display
                document.querySelectorAll('.user-detail').forEach(el => {
                    el.addEventListener('click', function() {
                        let content = '';
                        const imageUrl = this.dataset.image || '{{ asset('images/default.png') }}';

                        content = `<img src="${imageUrl}" alt="User Image" class="img-fluid mb-2" style="max-height: 200px;"><br>
                                   <strong>Name:</strong> ${this.dataset.name}<br>
                                   <strong>Membership Code:</strong> ${this.dataset.membership_code}<br>
                                   <strong>Phone:</strong> ${this.dataset.phone}`;

                        document.getElementById('modalContent').innerHTML = content;
                        const modal = new bootstrap.Modal(document.getElementById('infoModal'));
                        modal.show();
                    });
                });
            });
        </script>
    @endpush
@endsection
