@extends('layouts.sideBar')

@php
    use App\Enums\BonusPenaltyStatus;
    use App\Enums\BonusPenaltyType;
    use Carbon\Carbon;
@endphp
@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-primary">
                <i class="fas fa-users me-2"></i>{{ __('messages.family') }}: {{ $familyCode }}
            </h1>
            <div class="d-flex gap-2">
                <a href="{{ route('families.export', $familyCode) }}" class="btn btn-success">
                    <i class="fa fa-file-excel me-1"></i>Export to Excel
                </a>
                <a href="{{ route('families.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i>{{ __('messages.back') }}
                </a>
            </div>
        </div>

        @foreach ($membersData as $memberData)
            <div class="card shadow-sm mb-4">
                <div class="card-header card-header-primary">
                    <div class="d-flex align-items-center gap-3 text-white">
                        <img src="{{ $memberData['user']->getFirstMediaUrl('profile_images') ?: asset('images/default.png') }}"
                            alt="{{ $memberData['user']->name }}" class="rounded-circle flex-shrink-0"
                            style="width: 60px; height: 60px; object-fit: cover; border: 3px solid rgba(255,255,255,0.5);">
                        <div>
                            <h4 class="mb-1 fw-bold">{{ $memberData['user']->name ?: $memberData['user']->membership_code }}</h4>
                            <p class="mb-0 opacity-75 small">{{ $memberData['user']->membership_code }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Score & Points Summary -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-gradient-primary text-white text-center">
                                <div class="small opacity-75 mb-1">{{ __('messages.final_score') }}</div>
                                <div class="h3 fw-bold mb-0">{{ $memberData['final_score'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-gradient-success text-white text-center">
                                <div class="small opacity-75 mb-1">{{ __('messages.final_points') }}</div>
                                <div class="h3 fw-bold mb-0">{{ $memberData['final_points'] }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Quizzes -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-question-circle text-primary"></i>
                                    <small class="text-muted fw-semibold">{{ __('messages.quizzes_solved') }}</small>
                                </div>
                                <div class="fw-bold">{{ $memberData['quizzes_solved'] }} / {{ $memberData['total_quizzes'] }}</div>
                            </div>
                        </div>

                        <!-- Last Quiz -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-clipboard-check text-info"></i>
                                    <small class="text-muted fw-semibold">{{ __('messages.last_quiz') }}</small>
                                </div>
                                @if ($memberData['last_quiz'])
                                    <div class="fw-semibold small">{{ $memberData['last_quiz']['name'] }}</div>
                                    <small class="text-muted">{{ $memberData['last_quiz']['date'] ? Carbon::parse($memberData['last_quiz']['date'])->format('Y-m-d H:i') : 'N/A' }}</small>
                                @else
                                    <div class="text-muted small">{{ __('messages.no_data') }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Last Redeem -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-gift text-warning"></i>
                                    <small class="text-muted fw-semibold">{{ __('messages.last_redeem') }}</small>
                                </div>
                                @if ($memberData['last_order'])
                                    <div class="fw-semibold small">{{ $memberData['last_order']['reward'] }}</div>
                                    <small class="text-muted">{{ Carbon::parse($memberData['last_order']['date'])->format('Y-m-d H:i') }}</small>
                                @else
                                    <div class="text-muted small">{{ __('messages.no_data') }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Last Bonus -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-arrow-up text-success"></i>
                                    <small class="text-muted fw-semibold">{{ __('messages.last_bonus') }}</small>
                                </div>
                                @if ($memberData['last_bonus'])
                                    <div class="fw-bold text-success">+{{ $memberData['last_bonus']['value'] }} {{ __('messages.points') }}</div>
                                    <small class="text-muted">{{ Carbon::parse($memberData['last_bonus']['date'])->format('Y-m-d H:i') }}</small>
                                @else
                                    <div class="text-muted small">{{ __('messages.no_data') }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Last Penalty -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-arrow-down text-danger"></i>
                                    <small class="text-muted fw-semibold">{{ __('messages.last_penalty') }}</small>
                                </div>
                                @if ($memberData['last_penalty'])
                                    <div class="fw-bold text-danger">-{{ $memberData['last_penalty']['value'] }} {{ __('messages.points') }}</div>
                                    <small class="text-muted">{{ Carbon::parse($memberData['last_penalty']['date'])->format('Y-m-d H:i') }}</small>
                                @else
                                    <div class="text-muted small">{{ __('messages.no_data') }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Last Competition -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-trophy text-warning"></i>
                                    <small class="text-muted fw-semibold">{{ __('messages.last_competition') }}</small>
                                </div>
                                @if ($memberData['last_competition'])
                                    <div class="fw-semibold small">{{ $memberData['last_competition']['name'] }}</div>
                                    <small class="text-muted">{{ $memberData['last_competition']['date'] ? Carbon::parse($memberData['last_competition']['date'])->format('Y-m-d H:i') : 'N/A' }}</small>
                                @else
                                    <div class="text-muted small">{{ __('messages.no_data') }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Groups -->
                        <div class="col-12">
                            <div class="p-3 border rounded-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fas fa-layer-group text-primary"></i>
                                    <small class="text-muted fw-semibold">{{ __('messages.groups') }}</small>
                                </div>
                                @if ($memberData['groups']->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($memberData['groups'] as $group)
                                            <span class="badge badge-gradient-info">{{ $group->name }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted small">{{ __('messages.no_groups') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
