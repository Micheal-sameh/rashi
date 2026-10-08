@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <x-page-header icon="fa-user" :title="$user->name" subtitle="{{ __('messages.user_details') }}" />

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="rs-stat-card tone-success">
                    <div class="rs-stat-top">
                        <span class="rs-label-md">{{ __('messages.points') }}</span>
                        <div class="rs-stat-icon"><i class="fas fa-coins"></i></div>
                    </div>
                    <div class="rs-stat-value">{{ $user->points ?? '0' }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="rs-stat-card tone-warning">
                    <div class="rs-stat-top">
                        <span class="rs-label-md">{{ __('messages.score') }}</span>
                        <div class="rs-stat-icon"><i class="fas fa-star"></i></div>
                    </div>
                    <div class="rs-stat-value">{{ $user->score }}</div>
                </div>
            </div>
        </div>

        <!-- User Details Card -->
        <div class="card rounded-4 shadow-soft mb-4">
            <div class="card-header">
                <h4 class="rs-title-lg mb-0">{{ __('messages.user_details') }}</h4>
            </div>
            <div class="card-body">
                <div class="row gy-4">
                    <div class="col-md-4">
                        <p class="rs-label-md mb-1">{{ __('messages.name') }}</p>
                        <div class="fw-semibold">{{ $user->name }}</div>
                    </div>
                    <div class="col-md-4">
                        <p class="rs-label-md mb-1">{{ __('messages.email') }}</p>
                        <div class="fw-semibold">{{ $user->email }}</div>
                    </div>
                    <div class="col-md-4">
                        <p class="rs-label-md mb-1">{{ __('messages.groups') }}</p>
                        <div class="fw-semibold">
                            <span id="userGroupsText">
                                {{ $user->groups->pluck('name')->join(', ') ?: __('messages.not_assigned') }}
                            </span>
                            <button type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#updateGroupsModal">
                                <i class="fa fa-edit me-1"></i>{{ __('messages.edit') }}
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <p class="rs-label-md mb-1">{{ __('messages.role') }}</p>
                        <div class="fw-semibold">
                            <span id="userRoleText">
                                {{ $user->roles->pluck('name')->join(', ') ?: __('messages.not_assigned') }}
                            </span>
                            <button type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#updateRoleModal">
                                <i class="fa fa-edit me-1"></i>{{ __('messages.edit') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row gy-4 mt-1">
                    <div class="col-md-4">
                        <p class="rs-label-md mb-1">{{ __('messages.created_at') }}</p>
                        <div class="fw-semibold">{{ $user->created_at->format('Y-m-d') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Points History Table -->
        <div class="card rounded-4 shadow-soft">
            <div class="card-header">
                <h4 class="rs-title-lg mb-0">{{ __('messages.points_history') }}</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.amount') }}</th>
                                <th>{{ __('messages.total_points') }}</th>
                                <th>{{ __('messages.total_score') }}</th>
                                <th>{{ __('messages.subject_name') }}</th>
                                <th>{{ __('messages.subject_type') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($points as $point)
                                <tr>
                                    <td>{{ $point->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $point->amount }}</td>
                                    <td><span class="badge" style="background: var(--color-success-container); color: var(--color-on-success-container);">{{ $point->points }}</span></td>
                                    <td><span class="badge" style="background: var(--color-warning-container); color: var(--color-warning);">{{ $point->score }}</span></td>
                                    <td>{{ optional($point->subject)->name ?? $point->subject_id }}</td>
                                    <td>{{ __('messages.' . class_basename($point->subject_type)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        {{ __('messages.no_points_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    @if ($points->hasPages())
                        <nav>
                            <ul class="pagination pagination-sm">
                                <li class="page-item {{ $points->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $points->previousPageUrl() }}">&laquo;</a>
                                </li>

                                @foreach ($points->getUrlRange(1, $points->lastPage()) as $page => $url)
                                    <li class="page-item {{ $points->currentPage() === $page ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endforeach

                                <li class="page-item {{ $points->hasMorePages() ? '' : 'disabled' }}">
                                    <a class="page-link" href="{{ $points->nextPageUrl() }}">&raquo;</a>
                                </li>
                            </ul>
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Update Groups Modal -->
    <div class="modal fade" id="updateGroupsModal" tabindex="-1" aria-labelledby="updateGroupsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateGroupsModalLabel">
                        <i class="fa fa-users me-2"></i>{{ __('messages.update_user_groups') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('users.updateGroups', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div style="max-height: 400px; overflow-y: auto;">
                            @foreach ($groups as $group)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="groups[]"
                                        value="{{ $group->id }}" id="modal_group_{{ $group->id }}"
                                        {{ $user->groups->contains($group->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="modal_group_{{ $group->id }}">
                                        {{ $group->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>{{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i>{{ __('messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Role Modal -->
    <div class="modal fade" id="updateRoleModal" tabindex="-1" aria-labelledby="updateRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateRoleModalLabel">
                        <i class="fa fa-user-shield me-2"></i>{{ __('messages.update_user_role') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('users.updateRole', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <select class="form-select" name="role" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" {{ $user->roles->pluck('name')->contains($role) ? 'selected' : '' }}>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>{{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i>{{ __('messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
