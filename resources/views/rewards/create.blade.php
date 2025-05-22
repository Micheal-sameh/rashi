@extends('layouts.sideBar')

@section('content')
<div class="container" style="width: 95%;">
    <h2>{{ __('messages.create_reward') }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>{{ __('messages.whoops') }}</strong> {{ __('messages.input_problems') }}<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Add enctype="multipart/form-data" -->
    <form action="{{ route('rewards.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('messages.name') }}</label>
            <input type="text" name="name" class="form-control" placeholder="{{ __('messages.name') }}" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">{{ __('messages.quantity') }}</label>
            <input type="number" name="quantity" class="form-control" placeholder="{{ __('messages.quantity') }}" value="{{ old('quantity') }}" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">{{ __('messages.status') }}</label>
            <select name="status" class="form-control" required>
                <option value="">{{ __('messages.select') }}</option>
                @foreach (collect(App\Enums\RewardStatus::all())->except([1]) as $enum)
                <option value="{{ $enum['value'] }}" {{ old('status') == $enum['value'] ? 'selected' : '' }}>{{ $enum['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="points" class="form-label">{{ __('messages.points') }}</label>
            <input type="number" name="points" class="form-control" placeholder="{{ __('messages.points') }}" value="{{ old('points') }}" required>
        </div>

        <!-- Image Upload Field -->
        <div class="mb-3">
            <label for="image" class="form-label">{{ __('messages.image') }}</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">{{ __('messages.create') }}</button>
    </form>
</div>
@endsection
