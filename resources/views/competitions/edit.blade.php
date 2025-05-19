@extends('layouts.sideBar')

@section('content')
<div class="container" style="width: 95%;">
    <h2>{{ __('messages.edit_competition') }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('competitions.update', $competition->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3 col-6">
            <label for="name" class="form-label">{{ __('messages.name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $competition->name) }}" required>
        </div>

        <div class="mb-3 col-4">
            <label for="start_at" class="form-label">{{ __('messages.start_at') }}</label>
            <input type="date" name="start_at" class="form-control" value="{{ old('start_at', \Carbon\Carbon::parse($competition->start_at)->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3 col-4">
            <label for="end_at" class="form-label">{{ __('messages.end_at') }}</label>
            <input type="date" name="end_at" class="form-control" value="{{ old('end_at', \Carbon\Carbon::parse($competition->end_at)->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3 col-6">
            <label for="image" class="form-label">{{ __('messages.image') }}</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            @if ($competition->hasMedia('competitions_images'))
                <div class="mt-2">
                    <img src="{{ $competition->getFirstMediaUrl('competitions_images') }}" alt="Competition Image" style="max-width: 200px;">
                </div>
            @endif
        </div>

        {{-- Group Selection --}}
        <div class="mb-3">
            <label class="form-label">{{ __('messages.select_groups') }}</label>
            <div class="form-control" style="height: auto; max-height: 300px; overflow-y: auto;">
                @foreach ($groups as $group)
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="groups[]"
                            value="{{ $group->id }}"
                            id="group_{{ $group->id }}"
                            {{ in_array($group->id, $competition->groups->pluck('id')->toArray()) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="group_{{ $group->id }}">
                            {{ $group->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
    </form>
</div>
@endsection
