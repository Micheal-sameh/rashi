<!-- Desktop Table View -->
<div class="d-none d-lg-block">
    <div class="table-responsive w-100">
        <table class="table table-hover align-middle mb-0 w-100">
            <thead>
                <tr>
                    <th class="rs-label-md">{{ __('messages.name') }}</th>
                    <th class="rs-label-md">{{ __('messages.email') }}</th>
                    <th class="rs-label-md">{{ __('messages.membership_code') }}</th>
                    <th class="rs-label-md">{{ __('messages.phone') }}</th>
                    <th class="rs-label-md" onclick="applySort('score')" style="cursor:pointer;">
                        {{ __('messages.score') }}
                        @if (request('sort_by') === 'score')
                            <i class="fa fa-sort-{{ request('direction') === 'asc' ? 'asc' : 'desc' }}"></i>
                        @else
                            <i class="fa fa-sort text-muted"></i>
                        @endif
                    </th>
                    <th class="rs-label-md" onclick="applySort('points')" style="cursor:pointer;">
                        {{ __('messages.points') }}
                        @if (request('sort_by') === 'points')
                            <i class="fa fa-sort-{{ request('direction') === 'asc' ? 'asc' : 'desc' }}"></i>
                        @else
                            <i class="fa fa-sort text-muted"></i>
                        @endif
                    </th>
                    <th class="rs-label-md">{{ __('messages.image') }}</th>
                    <th class="rs-label-md">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->name ?? __('messages.not_available') }}</td>
                        <td>{{ $user->email ?? __('messages.not_available') }}</td>
                        <td>
                            @if ($user->membership_code)
                                <span class="badge" style="background: var(--color-secondary-container); color: var(--color-secondary);">{{ $user->membership_code }}</span>
                            @else
                                {{ __('messages.not_available') }}
                            @endif
                        </td>
                        <td>{{ $user->phone ?? __('messages.not_available') }}</td>
                        <td class="text-end fw-semibold">{{ $user->score ?? 0 }}</td>
                        <td class="text-end fw-semibold">{{ $user->points ?? 0 }}</td>
                        <td>
                            @if ($user->hasMedia('profile_images'))
                                <img src="{{ $user->getFirstMediaUrl('profile_images') }}" alt="{{ $user->name }}"
                                    width="44" height="44" class="rounded-circle shadow-sm zoomable-image"
                                    onclick="openPopup(this.src)" style="cursor:pointer; object-fit: cover;">
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-primary"
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
                <div class="card rounded-4 shadow-soft h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="me-3">
                            @if ($user->hasMedia('profile_images'))
                                <img src="{{ $user->getFirstMediaUrl('profile_images') }}" alt="{{ $user->name }}"
                                    width="60" height="60" class="rounded-circle shadow-sm zoomable-image"
                                    onclick="openPopup(this.src)" style="cursor:pointer; object-fit: cover;">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px; background: var(--color-surface-container-low);">
                                    <span class="text-muted small">{{ __('messages.not_available') }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="card-title fw-bold mb-1">{{ $user->name ?? 'N/A' }}</h6>
                            <p class="card-text mb-1"><small class="text-muted">{{ $user->email ?? 'N/A' }}</small></p>
                            <p class="card-text mb-1"><small class="text-muted">{{ $user->phone ?? 'N/A' }}</small></p>
                            @if ($user->membership_code)
                                <span class="badge mb-2" style="background: var(--color-secondary-container); color: var(--color-secondary);">{{ $user->membership_code }}</span>
                            @endif
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">{{ __('messages.score') }}: {{ $user->score ?? 0 }}</small>
                                <small class="text-muted">{{ __('messages.points') }}:
                                    {{ $user->points ?? 0 }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-2 d-flex justify-content-end">
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-primary"
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

    .pagination .page-link {
        border-radius: var(--radius-input, 8px);
        margin: 0 2px;
        border: 1px solid var(--color-border, #e2e8f0);
        color: var(--color-on-surface-variant, #464555);
    }

    .pagination .page-item.active .page-link {
        background-color: var(--color-primary, #3525cd);
        border-color: var(--color-primary, #3525cd);
    }
</style>
