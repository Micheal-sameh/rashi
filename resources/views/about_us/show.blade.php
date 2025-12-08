@extends('layouts.sideBar')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route("$route.edit") }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> {{ __('messages.edit') }}
        </a>
    </div>

    <!-- Show the saved HTML as a real rendered page -->
    {!! $aboutUs->value !!}

</div>
@endsection
