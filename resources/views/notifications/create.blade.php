@extends('layouts.sideBar')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.send_notification') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('notifications.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('notifications.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="target_type">Send to:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="user" value="user" {{ old('target_type', 'user') === 'user' ? 'checked' : '' }}>
                                <label class="form-check-label" for="user">
                                    Individual User
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="group" value="group" {{ old('target_type') === 'group' ? 'checked' : '' }}>
                                <label class="form-check-label" for="group">
                                    Group
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="user-select">
                            <label for="user_id">{{ __('messages.select_user') }}</label>
                            <select name="user_id" class="form-control">
                                <option value="">{{ __('messages.choose_a_user') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" >{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="group-select" style="display: none;">
                            <label for="group_id">{{ __('messages.select_group') }}</label>
                            <select name="group_id" class="form-control">
                                <option value="">{{ __('messages.choose_a_group') }}</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" name="target_type" id="target_type_hidden" value="user">

                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Enter your notification message..." required>{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> {{ __('messages.send_notification') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userRadio = document.getElementById('user');
    const groupRadio = document.getElementById('group');
    const userSelect = document.getElementById('user-select');
    const groupSelect = document.getElementById('group-select');
    const targetTypeHidden = document.getElementById('target_type_hidden');

    function toggleSelect() {
        if (userRadio.checked) {
            userSelect.style.display = 'block';
            groupSelect.style.display = 'none';
            userSelect.querySelector('select').setAttribute('required', 'required');
            groupSelect.querySelector('select').removeAttribute('required');
            targetTypeHidden.value = 'user';
        } else {
            userSelect.style.display = 'none';
            groupSelect.style.display = 'block';
            groupSelect.querySelector('select').setAttribute('required', 'required');
            userSelect.querySelector('select').removeAttribute('required');
            targetTypeHidden.value = 'group';
        }
    }

    // Set initial state based on old input or default
    const oldTargetType = '{{ old("target_type", "user") }}';
    if (oldTargetType === 'group') {
        groupRadio.checked = true;
        toggleSelect();
    } else {
        userRadio.checked = true;
        toggleSelect();
    }

    userRadio.addEventListener('change', toggleSelect);
    groupRadio.addEventListener('change', toggleSelect);
});
</script>
@endsection
