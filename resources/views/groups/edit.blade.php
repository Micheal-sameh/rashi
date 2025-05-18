@extends('layouts.sideBar')

@section('content')
    <div class="container" style="width: 95%;">
        <h2>{{ __('messages.update_users_for_group') }}</h2>

        <a href="{{ route('groups.index') }}" class="btn btn-secondary mb-3">{{ __('messages.back_to_groups') }}</a>

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

        <form action="{{ route('groups.updateUsers', $group->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Search Bar -->
            <div class="mb-3">
                <label for="search" class="form-label">{{ __('messages.search_users') }}</label>
                <input
                    type="text"
                    id="search"
                    class="form-control"
                    placeholder="{{ __('messages.search_for_user') }}"
                    onkeyup="searchUsers()"
                >
            </div>

            <!-- Users List -->
            <div class="mb-3">
                <label for="users" class="form-label">{{ __('messages.select_users') }}</label>
                <div id="usersList" class="form-control" style="height: auto; max-height: 300px; overflow-y: auto; width: 100%;">
                    @foreach ($users as $user)
                        <div class="form-check user-item">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="users[]"
                                value="{{ $user->id }}"
                                id="user_{{ $user->id }}"
                                {{ in_array($user->id, $group->users->pluck('id')->toArray()) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="user_{{ $user->id }}">
                                {{ $user->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
        </form>
    </div>

    @push('scripts')
    <script>
        // JavaScript function to filter users
        function searchUsers() {
            let input = document.getElementById('search');
            let filter = input.value.toLowerCase();
            let usersList = document.getElementById('usersList');
            let userItems = usersList.getElementsByClassName('user-item');

            for (let i = 0; i < userItems.length; i++) {
                let label = userItems[i].getElementsByTagName('label')[0];
                if (label) {
                    let textValue = label.textContent || label.innerText;
                    if (textValue.toLowerCase().indexOf(filter) > -1) {
                        userItems[i].style.display = "";
                    } else {
                        userItems[i].style.display = "none";
                    }
                }
            }
        }
    </script>
    @endpush
@endsection
