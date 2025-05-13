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

        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $competition->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="start_at" class="form-label">Start At:</label>
            <input type="date" name="start_at" class="form-control" value="{{ old('start_at', Carbon\carbon::parse($competition->start_at)->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label for="end_at" class="form-label">End At:</label>
            <input type="date" name="end_at" class="form-control" value="{{ old('end_at', Carbon\carbon::parse($competition->end_at)->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image:</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            @if ($competition->hasMedia('competitions_images'))
                <div class="mt-2">
                    <img src="{{ $competition->getFirstMediaurl('competitions_images') }}" alt="Competition Image" style="max-width: 200px;">
                </div>
            @endif

        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
