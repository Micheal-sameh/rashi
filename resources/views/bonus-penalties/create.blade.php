@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <x-page-header icon="fa-balance-scale" :title="__('messages.add_bonus_penalty')">
                    <x-slot:actions>
                        <a href="{{ route('bonus-penalties.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left me-1"></i>{{ __('messages.back') }}
                        </a>
                    </x-slot>
                </x-page-header>

                <div class="card rounded-4 shadow-soft">
                    <div class="card-body">
                        <form action="{{ route('bonus-penalties.store') }}" method="POST">
                            @csrf

                            <div class="row g-3">
                                <!-- User Selection -->
                                <div class="col-md-6">
                                    <label for="user_id" class="form-label">{{ __('messages.user_name') }} <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                        <option value="">{{ __('messages.select_user') }}</option>
                                        @foreach (App\Models\User::OrderBy('name')->get() as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->membership_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Type Selection -->
                                <div class="col-md-6">
                                    <label for="type" class="form-label">{{ __('messages.type') }} <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="">{{ __('messages.select_type') }}</option>
                                        @foreach (App\Enums\BonusPenaltyType::all() as $value)
                                            <option value="{{ $value['value'] }}" {{ old('type') == $value['value'] ? 'selected' : '' }}>
                                                {{ $value['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Points -->
                                <div class="col-md-6">
                                    <label for="points" class="form-label">{{ __('messages.points') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="points" id="points" class="form-control @error('points') is-invalid @enderror"
                                           value="{{ old('points') }}" min="1" required>
                                    @error('points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Reason -->
                                <div class="col-md-6">
                                    <label for="reason" class="form-label">{{ __('messages.reason') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror"
                                           value="{{ old('reason') }}" maxlength="255" required>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-1"></i>{{ __('messages.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
