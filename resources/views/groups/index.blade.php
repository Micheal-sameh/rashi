@extends('layouts.sideBar')

@section('content')
    <div class="container" style="width: 95%;">
        <h2>{{ __('messages.groups') }}</h2>

        <a href="{{ route('groups.create') }}" class="btn btn-success mb-3">
            {{ __('messages.create_groups') }}
        </a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $index => $group)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="group-name-clickable" data-id="{{ $group->id }}" data-name="{{ $group->name }}">
                                {{ $group->name }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-sm btn-primary">
                                {{__('messages.edit_group_users')}}</i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">{{ __('messages.no_groups') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal for Editing Group Name -->
    <div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGroupModalLabel">{{ __('messages.edit_name') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editGroupForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="group_name" class="form-label">{{ __('messages.name') }}</label>
                            <input
                                type="text"
                                class="form-control"
                                id="group_name"
                                name="name"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Select all group names that can be clicked
        const groupNames = document.querySelectorAll('.group-name-clickable');

        groupNames.forEach(groupName => {
            groupName.addEventListener('click', function () {
                const groupId = groupName.getAttribute('data-id');
                const groupNameText = groupName.getAttribute('data-name');

                // Fill the modal with current group name
                const modal = new bootstrap.Modal(document.getElementById('editGroupModal'));
                const inputField = document.getElementById('group_name');
                inputField.value = groupNameText;

                // Set the form action to the group edit route
                const form = document.getElementById('editGroupForm');
                form.action = '/groups/' + groupId;

                modal.show();
            });
        });
    });
</script>
@endpush
