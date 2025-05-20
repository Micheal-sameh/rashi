<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>{{ __('messages.name') }}</th>
            <th>{{ __('messages.email') }}</th>
            <th>{{ __('messages.membership_code') }}</th>
            <th>{{ __('messages.phone') }}</th>
            <th>{{ __('messages.score') }}</th>
            <th>{{ __('messages.points') }}</th>
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
                <td>{{ $user->point }}</td>
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
