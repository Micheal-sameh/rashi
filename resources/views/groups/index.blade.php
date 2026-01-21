@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">

        <!-- Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">{{ __('messages.groups') }}</h2>
            <a href="{{ route('groups.create') }}" class="btn btn-success">
                <i class="fa fa-plus-circle me-1"></i> {{ __('messages.create_groups') }}
            </a>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Groups Table Card -->
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.abbreviation') }}</th>
                            <th class="text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $index => $group)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="group-name-clickable text-primary fw-semibold"
                                        data-id="{{ $group->id }}" data-name="{{ $group->name }}"
                                        data-abbreviation="{{ $group->abbreviation }}">
                                        {{ $group->name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="group-name-clickable text-primary fw-semibold">
                                        {{ $group->abbreviation }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('groups.usersedit', $group->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-users me-1"></i> {{ __('messages.edit_group_users') }}
                                    </a>
                                    <a href="{{ route('groups.edit', $group->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-edit me-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">{{ __('messages.no_groups') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-center pt-3">
            @if ($groups->hasPages())
                <nav>
                    <ul class="pagination">
                        {{-- Previous Page Link --}}
                        @if ($groups->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $groups->previousPageUrl() }}" rel="prev">&laquo;</a>
                            </li>
                        @endif

                        @php
                            $current = $groups->currentPage();
                            $last = $groups->lastPage();
                            $start = max($current - 2, 2);
                            $end = min($current + 2, $last - 1);
                        @endphp

                        {{-- First page --}}
                        <li class="page-item {{ $current === 1 ? 'active' : '' }}">
                            <a class="page-link" href="{{ $groups->url(1) }}">1</a>
                        </li>

                        {{-- Dots before start --}}
                        @if ($start > 2)
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        @endif

                        {{-- Page range --}}
                        @for ($page = $start; $page <= $end; $page++)
                            <li class="page-item {{ $current === $page ? 'active' : '' }}">
                                <a class="page-link" href="{{ $groups->url($page) }}">{{ $page }}</a>
                            </li>
                        @endfor

                        {{-- Dots after end --}}
                        @if ($end < $last - 1)
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        @endif

                        {{-- Last page --}}
                        @if ($last > 1)
                            <li class="page-item {{ $current === $last ? 'active' : '' }}">
                                <a class="page-link" href="{{ $groups->url($last) }}">{{ $last }}</a>
                            </li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($groups->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $groups->nextPageUrl() }}" rel="next">&raquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                        @endif
                    </ul>
                </nav>
            @endif
        </div>
    </div>

    <!-- Modal for Editing Group Name -->
    <div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editGroupModalLabel">{{ __('messages.edit_name') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editGroupForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="group_name" class="form-label">{{ __('messages.name') }}</label>
                            <input type="text" class="form-control form-control-lg" id="group_name" name="name"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-3"
                            data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                        <button type="submit" class="btn btn-primary rounded-3">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const groupNames = document.querySelectorAll('.group-name-clickable');

            groupNames.forEach(groupName => {
                groupName.style.cursor = 'pointer';
                groupName.addEventListener('mouseover', () => groupName.classList.add(
                    'text-decoration-underline'));
                groupName.addEventListener('mouseout', () => groupName.classList.remove(
                    'text-decoration-underline'));

                groupName.addEventListener('click', function() {
                    const groupId = groupName.dataset.id;
                    const groupNameText = groupName.dataset.name;

                    const modal = new bootstrap.Modal(document.getElementById('editGroupModal'));
                    document.getElementById('group_name').value = groupNameText;
                    document.getElementById('editGroupForm').action = '/groups/' + groupId;

                    modal.show();
                });
            });
        });
    </script>
@endpush
