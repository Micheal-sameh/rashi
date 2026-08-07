@extends('layouts.sideBar')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    <x-page-header icon="fa-info-circle" :title="__('messages.about_us')">
        <x-slot:actions>
            <a href="{{ route("$route.edit") }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> {{ __('messages.edit') }}
            </a>
        </x-slot>
    </x-page-header>

    <div class="card">
        <div class="card-body">
            <!-- Show the saved HTML as a real rendered page -->
            {!! $aboutUs->value !!}
        </div>
    </div>

</div>
@endsection
