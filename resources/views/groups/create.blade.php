@extends('layouts.sideBar')

@section('content')
<div class="container" style="width: 95%;">
    <h2>{{__('messages.create_groups')}}</h2>

    <form action="{{ route('groups.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">{{__('messages.name')}}</label>
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

        <button type="submit" class="btn btn-success">{{__('messages.create')}}</button>
        <a href="{{ route('groups.index') }}" class="btn btn-secondary">{{__('messages.cancel')}}</a>
    </form>
</div>
@endsection
