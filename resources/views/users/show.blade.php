@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 mt-4">

        <!-- User Details Card -->
        <div class="card shadow-sm mb-4 rounded-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{{ __('messages.user_details') }}</h4>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-4">
                        <strong>{{ __('messages.name') }}:</strong> {{ $user->name }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ __('messages.email') }}:</strong> {{ $user->email }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ __('messages.groups') }}:</strong>
                        <div class="dropdown d-inline">
                            <span id="groupDropdownToggle" class="text-primary fw-semibold" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                                <span id="userGroupsText">
                                    {{ $user->groups->pluck('name')->join(', ') ?: __('messages.not_assigned') }}
                                </span>
                                <i class="fa fa-caret-down ms-1"></i>
                            </span>

                            <div class="dropdown-menu p-3 shadow-sm" style="min-width: 250px;"
                                aria-labelledby="groupDropdownToggle">
                                <!-- Standard HTML form submission -->
                                <form method="POST" action="{{ route('users.updateGroups', $user->id) }}">
                                    @csrf
                                    @method('PUT')

                                    @foreach ($groups as $group)
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="groups[]"
                                                value="{{ $group->id }}" id="group_{{ $group->id }}"
                                                {{ $user->groups->contains($group->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="group_{{ $group->id }}">
                                                {{ $group->name }}
                                            </label>
                                        </div>
                                    @endforeach

                                    <button type="submit" class="btn btn-sm btn-primary mt-2">
                                        <i class="fa fa-save me-1"></i>{{ __('messages.update') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row gy-3 mt-3">
                    <div class="col-md-4">
                        <strong>{{ __('messages.points') }}:</strong>
                        <span class="badge bg-success">{{ $user->points ?? '0' }}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>{{ __('messages.score') }}:</strong>
                        <span class="badge bg-warning text-dark">{{ $user->score }}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>{{ __('messages.created_at') }}:</strong> {{ $user->created_at->format('Y-m-d') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Points History Table -->
        <div class="card shadow-sm rounded-4">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0">{{ __('messages.points_history') }}</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
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
                                    <td><span class="badge bg-success">{{ $point->points }}</span></td>
                                    <td><span class="badge bg-warning text-dark">{{ $point->score }}</span></td>
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
@endsection
