@extends('layouts.sideBar')

@section('content')
<div class="container" style="width: 95%;">
    <h2>{{ __('messages.create_groups') }}</h2>

    <form action="{{ route('groups.store') }}" method="POST">
        @csrf

        {{-- Group Name Field --}}
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('messages.name') }}</label>
            <input
                type="text"
                name="name"
                id="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}"
                required
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Select Users Section --}}
        <div class="mb-3" style="max-width: 300px;">
            <label class="form-label">{{ __('messages.select_users') }}</label>
            <div class="form-control" style="height: auto; max-height: 300px; overflow-y: auto; width: 100%;">
                @foreach ($users as $user)
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="users[]"
                            value="{{ $user->id }}"
                            id="user_{{ $user->id }}"
                            style="border: 2px solid black;"  {{-- Bold black border for checkboxes --}}
                        >
                        <label class="form-check-label text-truncate d-block" style="max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" for="user_{{ $user->id }}">
                            {{ $user->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            @error('users')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Action Buttons --}}
        <button type="submit" class="btn btn-success">{{ __('messages.create') }}</button>
        <a href="{{ route('groups.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
    </form>
</div>
@endsection
