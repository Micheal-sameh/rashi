@extends('layouts.sideBar')

@section('content')
<div class="container py-4" style="width: 95%;">
    <h2 class="mb-4">{{__('messages.Application Settings')}}</h2>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <table  class="table table-bordered">
            <thead>
              <tr>
                <th>{{ __('messages.name') }}</th>
                <th>{{ __('messages.value') }}</th>
                <th>{{ __('messages.type') }}</th>
                {{-- <th>{{ __('messages.actions') }}</th> --}}
              </tr>
            </thead>
            <tbody>
                @foreach($settings as $setting)
                <tr>
                    <td>
                        <input type="text" name="settings[{{ $setting->id }}][name]"
                               value="{{ $setting->name }}" class="form-control" readonly disabled>
                    </td>
                    <td>
                        <input type="{{ $setting->type === 'file' ? 'file' : 'text' }}"
                               name="settings[{{ $setting->id }}][value]"
                               value="{{ $setting->value }}" class="form-control">
                    </td>
                    <td>
                        <input
                               name="settings[{{ $setting->id }}][value]"
                               value="{{ $setting->type }}" class="form-control" disabled>
                    </td>
                </tr>

                @endforeach
            </tbody>
          </table>

        <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
    </form>
</div>
@endsection
