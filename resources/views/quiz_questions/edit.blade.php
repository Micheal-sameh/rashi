@extends('layouts.sideBar')

@section('content')
<div class="container">
    <h2>Edit quiz</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('quizzes.update', $quiz->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3 col-6">
            <label for="name" class="form-label">{{__('messages.name')}}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $quiz->name) }}" required>
        </div>

        <div class="mb-3 col-4">
            <label for="date" class="form-label">{{__('messages.date')}}</label>
            <input type="date" name="date" class="form-control" value="{{ old('date', Carbon\carbon::parse($quiz->date)->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3 col-4">
            <label for="competition" class="form-label">{{__('messages.competition')}}</label>
            <input type="text" name="competition" class="form-control"
            value="{{ old('competition', $quiz->relationLoaded('competition') ? $quiz->competition->name : '') }}"
            disabled>
                </div>


        </div>

        <button type="submit" class="btn btn-primary">{{__('messages.update')}}</button>
    </form>
</div>
@endsection
