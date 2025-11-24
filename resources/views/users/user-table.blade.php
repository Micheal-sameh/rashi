<!-- Desktop Table View -->
<div class="d-none d-lg-block">
    <div class="table-responsive shadow-sm rounded-4 overflow-hidden w-100">
        <table class="table table-hover table-striped align-middle mb-0 w-100">
            <thead class="table-light">
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.email') }}</th>
                    <th>{{ __('messages.membership_code') }}</th>
                    <th>{{ __('messages.phone') }}</th>
                    <th onclick="applySort('score')" style="cursor:pointer;">
                        {{ __('messages.score') }}
                        @if (request('sort_by') === 'score')
                            <i class="fa fa-sort-{{ request('direction') === 'asc' ? 'asc' : 'desc' }}"></i>
                        @else
                            <i class="fa fa-sort text-muted"></i>
                        @endif
                    </th>
                    <th onclick="applySort('points')" style="cursor:pointer;">
                        {{ __('messages.points') }}
                        @if (request('sort_by') === 'points')
                            <i class="fa fa-sort-{{ request('direction') === 'asc' ? 'asc' : 'desc' }}"></i>
                        @else
                            <i class="fa fa-sort text-muted"></i>
                        @endif
                    </th>
                    <th>{{ __('messages.image') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->name ?? 'N/A' }}</td>
                        <td>{{ $user->email ?? 'N/A' }}</td>
                        <td>
                            @if ($user->membership_code)
                                <span class="badge bg-secondary">{{ $user->membership_code }}</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $user->phone ?? 'N/A' }}</td>
                        <td class="text-end">{{ $user->score ?? 0 }}</td>
                        <td class="text-end">{{ $user->points ?? 0 }}</td>
                        <td>
                            @if ($user->hasMedia('profile_images'))
                                <img src="{{ $user->getFirstMediaUrl('profile_images') }}" alt="{{ $user->name }}"
                                    width="60" class="rounded-circle shadow-sm zoomable-image"
                                    onclick="openPopup(this.src)" style="cursor:pointer;">
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-primary shadow-sm"
                                title="{{ __('messages.view') }}">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">{{ __('messages.no_users') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Card View -->
<div class="d-lg-none">
    <div class="row g-3">
        @forelse($users as $user)
            <div class="col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3">
                            @if ($user->hasMedia('profile_images'))
                                <img src="{{ $user->getFirstMediaUrl('profile_images') }}" alt="{{ $user->name }}"
                                    width="60" height="60" class="rounded-circle shadow-sm zoomable-image"
                                    onclick="openPopup(this.src)" style="cursor:pointer;">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px;">
                                    <span class="text-muted">N/A</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="card-title fw-bold mb-1">{{ $user->name ?? 'N/A' }}</h6>
                            <p class="card-text mb-1"><small class="text-muted">{{ $user->email ?? 'N/A' }}</small></p>
                            <p class="card-text mb-1"><small class="text-muted">{{ $user->phone ?? 'N/A' }}</small></p>
                            @if ($user->membership_code)
                                <span class="badge bg-secondary mb-2">{{ $user->membership_code }}</span>
                            @endif
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">{{ __('messages.score') }}: {{ $user->score ?? 0 }}</small>
                                <small class="text-muted">{{ __('messages.points') }}:
                                    {{ $user->points ?? 0 }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-2 d-flex justify-content-end">
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-primary shadow-sm"
                            title="{{ __('messages.view') }}">
                            <i class="fa fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center text-muted py-4">{{ __('messages.no_users') }}</div>
            </div>
        @endforelse
    </div>
</div>
<div class="d-flex justify-content-center pt-2">
    @if ($users->hasPages())
        <nav>
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($users->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $users->previousPageUrl() }}" rel="prev">&laquo;</a>
                    </li>
                @endif

                @php
                    $current = $users->currentPage();
                    $last = $users->lastPage();
                    $start = max($current - 2, 2);
                    $end = min($current + 2, $last - 1);
                @endphp

                {{-- First page --}}
                <li class="page-item {{ $current === 1 ? 'active' : '' }}">
                    <a class="page-link" href="{{ $users->url(1) }}">1</a>
                </li>

                {{-- Dots before start --}}
                @if ($start > 2)
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                @endif

                {{-- Page range --}}
                @for ($page = $start; $page <= $end; $page++)
                    <li class="page-item {{ $current === $page ? 'active' : '' }}">
                        <a class="page-link" href="{{ $users->url($page) }}">{{ $page }}</a>
                    </li>
                @endfor

                {{-- Dots after end --}}
                @if ($end < $last - 1)
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                @endif

                {{-- Last page --}}
                @if ($last > 1)
                    <li class="page-item {{ $current === $last ? 'active' : '' }}">
                        <a class="page-link" href="{{ $users->url($last) }}">{{ $last }}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($users->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $users->nextPageUrl() }}" rel="next">&raquo;</a>
                    </li>
                @else
                    <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                @endif
            </ul>
        </nav>
    @endif
</div>


<!-- Additional CSS -->
<style>
    .table-hover tbody tr:hover {
        background-color: #f1f3f5;
        transition: background 0.2s;
    }

    .zoomable-image {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .zoomable-image:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .table th {
        user-select: none;
    }
</style>
