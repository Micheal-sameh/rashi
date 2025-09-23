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
                        @if($user->membership_code)
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
                            <img src="{{ $user->getFirstMediaUrl('profile_images') }}"
                                 alt="{{ $user->name }}" width="60" class="rounded-circle shadow-sm zoomable-image"
                                 onclick="openPopup(this.src)" style="cursor:pointer;">
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('users.show', $user->id) }}"
                           class="btn btn-sm btn-primary shadow-sm"
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
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.table th {
    user-select: none;
}
</style>
