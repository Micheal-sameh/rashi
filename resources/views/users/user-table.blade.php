@php
    $currentSortBy = request('sort_by');
    $currentSortDirection = request('sort_direction', 'asc');
    function sortDirection($column, $currentSortBy, $currentSortDirection) {
        return $currentSortBy === $column && $currentSortDirection === 'asc' ? 'desc' : 'asc';
    }
@endphp

<div class="table-responsive w-100">
    <table class="table table-bordered table-striped w-100">
        <thead>
            <tr>
                <th>{{ __('messages.name') }}</th>
                <th>{{ __('messages.email') }}</th>
                <th>{{ __('messages.membership_code') }}</th>
                <th>{{ __('messages.phone') }}</th>

                <th onclick="applySort('score')" style="cursor: pointer;">
                    <span class="d-flex align-items-center gap-1">
                        {{ __('messages.score') }}
                        @if(request('sort_by') === 'score')
                            <i class="fa fa-sort-{{ request('sort_direction') === 'asc' ? 'asc' : 'desc' }}"></i>
                        @else
                            <i class="fa fa-sort text-muted"></i>
                        @endif
                    </span>
                </th>

                <th onclick="applySort('points')" style="cursor: pointer;">
                    <span class="d-flex align-items-center gap-1">
                        {{ __('messages.points') }}
                        @if(request('sort_by') === 'points')
                            <i class="fa fa-sort-{{ request('sort_direction') === 'asc' ? 'asc' : 'desc' }}"></i>
                        @else
                            <i class="fa fa-sort text-muted"></i>
                        @endif
                    </span>
                </th>


                <th>{{ __('messages.image') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->membership_code }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->score }}</td>
                    <td>{{ $user->points }}</td>
                    <td>
                        @if($user->hasMedia('profile_images'))
                            <img src="{{ $user->getFirstMediaUrl('profile_images') }}"
                                 alt="Image"
                                 width="60"
                                 class="zoomable-image"
                                 style="cursor: pointer;"
                                 onclick="openModal(this.src)">
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">{{ __('messages.no_users') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
