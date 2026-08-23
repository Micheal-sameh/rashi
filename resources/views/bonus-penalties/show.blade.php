@extends('layouts.sideBar')

@section('content')
    <div class="container py-4" style="max-width: 800px;">
        <x-page-header icon="fa-balance-scale" :title="__('messages.bonus_penalty_details')">
            <x-slot:actions>
                <a href="{{ route('bonus-penalties.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i>{{ __('messages.back') }}
                </a>
            </x-slot>
        </x-page-header>

        <div class="card rounded-4 shadow-soft">
            <div class="card-body">
                <div class="row g-4">
                    <!-- User -->
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.user_name') }}</label>
                        <p class="mb-0 fw-semibold">{{ $bonusPenalty->user->name ?? '' }}</p>
                    </div>

                    <!-- Type -->
                    <div class="col-md-6">
                        <label class="form-label d-block">{{ __('messages.type') }}</label>
                        <span class="badge {{ $bonusPenalty->type == \App\Enums\BonusPenaltyType::BONUS ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                            {{ \App\Enums\BonusPenaltyType::getStringValue($bonusPenalty->type) }}
                        </span>
                    </div>

                    <!-- Points -->
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.points') }}</label>
                        <p class="mb-0 fw-semibold">{{ $bonusPenalty->points }}</p>
                    </div>

                    <!-- Reason -->
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.reason') }}</label>
                        <p class="mb-0">{{ $bonusPenalty->reason }}</p>
                    </div>

                    <!-- Creator -->
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.creator') }}</label>
                        <p class="mb-0">{{ $bonusPenalty->creator->name ?? '' }}</p>
                    </div>

                    <!-- Created At -->
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.created_at') }}</label>
                        <p class="mb-0">{{ $bonusPenalty->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
