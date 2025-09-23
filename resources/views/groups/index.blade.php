@extends('layouts.sideBar')

@section('content')
    <div class="container py-4" style="width: 95%;">

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
                            <th class="text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $index => $group)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="group-name-clickable text-primary fw-semibold"
                                        data-id="{{ $group->id }}" data-name="{{ $group->name }}">
                                        {{ $group->name }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('groups.edit', $group->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-users me-1"></i> {{ __('messages.edit_group_users') }}
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
