@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>{{ __('messages.transfer_details') }}</h4>
                        <span class="badge bg-light text-dark">#{{ $transfer->id }}</span>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border-danger mb-3">
                                    <div class="card-header bg-danger text-white">
                                        <i class="fas fa-user-minus me-2"></i>{{ __('messages.sender') }}
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $transfer->sender->name }}</h5>
                                        <p class="mb-2">
                                            <strong>{{ __('messages.membership_code') }}:</strong><br>
                                            <span class="badge bg-secondary">{{ $transfer->sender->membership_code }}</span>
                                        </p>
                                        <p class="mb-0">
                                            <strong>{{ __('messages.current_points') }}:</strong><br>
                                            <span class="badge bg-info">{{ $transfer->sender->points }} {{ __('messages.points') }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-success mb-3">
                                    <div class="card-header bg-success text-white">
                                        <i class="fas fa-user-plus me-2"></i>{{ __('messages.receiver') }}
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $transfer->receiver->name }}</h5>
                                        <p class="mb-2">
                                            <strong>{{ __('messages.membership_code') }}:</strong><br>
                                            <span class="badge bg-secondary">{{ $transfer->receiver->membership_code }}</span>
                                        </p>
                                        <p class="mb-0">
                                            <strong>{{ __('messages.current_points') }}:</strong><br>
                                            <span class="badge bg-info">{{ $transfer->receiver->points }} {{ __('messages.points') }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <strong><i class="fas fa-coins me-2 text-primary"></i>{{ __('messages.points_transferred') }}:</strong>
                                        <h3 class="mb-0 text-success">{{ $transfer->points }}</h3>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong><i class="fas fa-users me-2 text-primary"></i>{{ __('messages.family_code') }}:</strong>
                                        <h5 class="mb-0"><span class="badge bg-info">{{ $transfer->family_code }}</span></h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($transfer->reason)
                            <div class="mb-4">
                                <strong><i class="fas fa-comment me-2"></i>{{ __('messages.reason') }}:</strong>
                                <p class="mb-0 mt-2 p-3 bg-light rounded">{{ $transfer->reason }}</p>
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <strong><i class="fas fa-user me-2"></i>{{ __('messages.created_by') }}:</strong>
                                <p>{{ $transfer->creator->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar me-2"></i>{{ __('messages.transfer_date') }}:</strong>
                                <p>{{ $transfer->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('point-transfers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
