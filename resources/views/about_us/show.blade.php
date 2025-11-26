@extends('layouts.sideBar')

@section('content')
<div class="container py-4">

    <!-- Show the saved HTML as a real rendered page -->
    {!! $aboutUs->value !!}

</div>
@endsection
