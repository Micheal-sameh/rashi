@extends('layouts.sideBar')

@section('content')
    <div class="container py-4" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-primary">{{ __('messages.bonus_penalty_details') }}</h1>
            <a href="{{ route('bonus-penalties.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fa fa-arrow-left me-1"></i>{{ __('messages.back') }}
            </a>
        </div>

        <div class="card shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="row g-3">
                    <!-- User -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('messages.user_name') }}</label>
                        <p class="mb-0">{{ $bonusPenalty->user->name ?? '' }}</p>
                    </div>

                    <!-- Type -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('messages.type') }}</label>
                        <p class="mb-0">
                            <span class="badge {{ $bonusPenalty->type == \App\Enums\BonusPenaltyType::BONUS ? 'bg-success' : 'bg-danger' }}">
                                {{ \App\Enums\BonusPenaltyType::getStringValue($bonusPenalty->type) }}
                            </span>
                        </p>
                    </div>

                    <!-- Points -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('messages.points') }}</label>
                        <p class="mb-0">{{ $bonusPenalty->points }}</p>
                    </div>

                    <!-- Reason -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('messages.reason') }}</label>
                        <p class="mb-0">{{ $bonusPenalty->reason }}</p>
                    </div>

                    <!-- Creator -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('messages.creator') }}</label>
                        <p class="mb-0">{{ $bonusPenalty->creator->name ?? '' }}</p>
                    </div>

                    <!-- Created At -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('messages.created_at') }}</label>
                        <p class="mb-0">{{ $bonusPenalty->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
