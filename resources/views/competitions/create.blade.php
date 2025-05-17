@extends('layouts.sideBar')

@section('content')
<div class="container" style="width: 95%;">
    <h2>{{__('messages.create_competitions')}}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('competitions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3 col-6">
            <label for="name" class="form-label">{{__('messages.name')}}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3 col-6">
            <label for="start_at" class="form-label">{{__('messages.start_at')}}</label>
            <input type="date" name="start_at" class="form-control" value="{{ old('start_at') }}" required>
        </div>

        <div class="mb-3 col-6">
            <label for="end_at" class="form-label">{{__('messages.end_at')}}</label>
            <input type="date" name="end_at" class="form-control" value="{{ old('end_at') }}" required>
        </div>

        <div class="mb-3 col-6">
            <label for="image" class="form-label">{{__('messages.image')}}</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">{{__('messages.create')}}</button>
    </form>
</div>
@endsection
