<div class="table-responsive w-100">
    <table class="table table-bordered table-striped w-100">
        <thead>
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
                    <td>{{ $user->name ?? 'N/A' }}</td>
                    <td>{{ $user->email ?? 'N/A' }}</td>
                    <td>{{ $user->membership_code ?? 'N/A' }}</td>
                    <td>{{ $user->phone ?? 'N/A' }}</td>
                    <td>{{ $user->score ?? 0 }}</td>
                    <td>{{ $user->points ?? 0 }}</td>
                    <td>
                        @if ($user->hasMedia('profile_images'))
                            <img src="{{ $user->getFirstMediaUrl('profile_images') }}" alt="{{ $user->name }}"
                                width="60" class="zoomable-image" onclick="openPopup(this.src)"
                                style="cursor:pointer;">
                        @else
                            N/A
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
                    <td colspan="8" class="text-center">{{ __('messages.no_users') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
