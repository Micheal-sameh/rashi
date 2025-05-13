@extends('layouts.sideBar')

@section('content')
<div class="container">
    <h2>Edit Competition</h2>

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
            <label for="name" class="form-label">{{__('messages.name')}}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $competition->name) }}" required>
        </div>

        <div class="mb-3 col-4">
            <label for="start_at" class="form-label">{{__('messages.start_at')}}</label>
            <input type="date" name="start_at" class="form-control" value="{{ old('start_at', Carbon\carbon::parse($competition->start_at)->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3 col-4">
            <label for="end_at" class="form-label">{{__('messages.end_at')}}</label>
            <input type="date" name="end_at" class="form-control" value="{{ old('end_at', Carbon\carbon::parse($competition->end_at)->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3 col-6">
            <label for="image" class="form-label">{{__('messages.image')}}</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            @if ($competition->hasMedia('competitions_images'))
                <div class="mt-2">
                    <img src="{{ $competition->getFirstMediaurl('competitions_images') }}" alt="Competition Image" style="max-width 200px;">
                </div>
            @endif

        </div>

        <button type="submit" class="btn btn-primary">{{__('messages.update')}}</button>
    </form>
</div>
@endsection
