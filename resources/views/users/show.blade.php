@extends('layouts.sideBar')

@section('content')
    <div class="container mt-4" style="width:95%">
        <!-- User Details Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>{{ __('messages.user_details') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>{{ __('messages.name') }}:</strong> {{ $user->name }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ __('messages.email') }}:</strong> {{ $user->email }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ __('messages.groups') }}:</strong>
                        <div class="dropdown d-inline">
                            <span id="groupDropdownToggle" class="text-primary" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false" style="cursor:pointer;">
                                <span id="userGroupsText">
                                    {{ $user->groups->pluck('name')->join(', ') ?: __('messages.not_assigned') }}
                                </span>
                            </span>

                            <div class="dropdown-menu p-3" style="min-width: 250px;" aria-labelledby="groupDropdownToggle">
                                <form id="updateGroupsForm">
                                    @csrf
                                    @foreach ($groups as $group)
                                        <div class="form-check">
                                            <input class="form-check-input group-checkbox" type="checkbox"
                                                value="{{ $group->id }}" id="group_{{ $group->id }}"
                                                {{ $user->groups->contains($group->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="group_{{ $group->id }}">
                                                {{ $group->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                    <button type="submit" class="btn btn-sm btn-primary mt-2"
                                        id="updateButton">{{ __('messages.update') }}</button>
                                    <span id="loadingSpinner" class="d-none ml-2">
                                        <i class="fa fa-spinner fa-spin"></i> {{ __('messages.updating') }}...
                                    </span>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <strong>{{ __('messages.points') }}:</strong> {{ $user->points ?? '0' }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ __('messages.score') }}:</strong> {{ $user->score }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ __('messages.created_at') }}:</strong> {{ $user->created_at->format('Y-m-d') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Points History Table -->
        <div class="card">
            <div class="card-header">
                <h4>{{ __('messages.points_history') }}</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
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
                                    <td>{{ $point->points }}</td>
                                    <td>{{ $point->score }}</td>
                                    <td>{{ optional($point->subject)->name ?? $point->subject_id }}</td>
                                    <td>{{ __('messages.' . class_basename($point->subject_type)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('messages.no_points_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($points, 'links'))
                    <div class="mt-3 ms-3">
                        {{ $points->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('updateGroupsForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent form submission

            const checkboxes = document.querySelectorAll('.group-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('{{ __('messages.select_at_least_one_group') }}');
                return;
            }

            // Get selected group IDs
            const groupIds = Array.from(checkboxes).map(cb => cb.value);

            // Create query string
            const queryString = new URLSearchParams();
            groupIds.forEach((id, index) => {
                queryString.append(`groups[${index}]`, id);
            });

            // Disable button and show loading spinner
            document.getElementById('updateButton').disabled = true;
            document.getElementById('loadingSpinner').classList.remove('d-none');

            // Send PUT request with query parameters
            fetch(`{{ route('users.updateGroups', $user->id) }}?${queryString.toString()}`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('userGroupsText').textContent = data.groups ||
                        '{{ __('messages.not_assigned') }}';

                    // Re-enable button and hide spinner
                    document.getElementById('updateButton').disabled = false;
                    document.getElementById('loadingSpinner').classList.add('d-none');
                })
                .catch(error => {
                    console.error('Error updating groups:', error);
                    alert('Failed to update groups. Please try again.');

                    document.getElementById('updateButton').disabled = false;
                    document.getElementById('loadingSpinner').classList.add('d-none');
                });
        });
    </script>
@endpush
