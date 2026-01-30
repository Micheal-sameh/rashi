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
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex align-items-center text-white">
                        <img src="{{ $memberData['user']->getFirstMediaUrl('profile_images') ?: asset('images/default.png') }}"
                            alt="{{ $memberData['user']->name }}" class="rounded-circle me-3"
                            style="width: 60px; height: 60px; object-fit: cover; border: 3px solid white;">
                    </div>
                    <div>
                        <h4 class="mb-1">{{ $memberData['user']->name ?: $memberData['user']->membership_code }}</h4>
                        <p class="mb-0">{{ $memberData['user']->membership_code }}</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Final Score and Points -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">{{ __('messages.final_score') }}</h6>
                                <p class="h4 mb-0 text-primary">{{ $memberData['final_score'] }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">{{ __('messages.final_points') }}</h6>
                                <p class="h4 mb-0 text-success">{{ $memberData['final_points'] }}</p>
                            </div>
                        </div>

                        <!-- Quizzes -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">{{ __('messages.quizzes_solved') }}</h6>
                                <p class="h5 mb-0">{{ $memberData['quizzes_solved'] }} /
                                    {{ $memberData['total_quizzes'] }}</p>
                            </div>
                        </div>

                        <!-- Last Quiz -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">{{ __('messages.last_quiz') }}</h6>
                                @if ($memberData['last_quiz'])
                                    <p class="mb-1"><strong>{{ $memberData['last_quiz']['name'] }}</strong></p>
                                    <small
                                        class="text-muted">{{ $memberData['last_quiz']['date'] ? Carbon::parse($memberData['last_quiz']['date'])->format('Y-m-d H:i') : 'N/A' }}</small>
                                @else
                                    <p class="mb-0 text-muted">{{ __('messages.no_data') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Last Redeem -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">{{ __('messages.last_redeem') }}</h6>
                                @if ($memberData['last_order'])
                                    <p class="mb-1"><strong>{{ $memberData['last_order']['reward'] }}</strong></p>
                                    <small
                                        class="text-muted">{{ Carbon::parse($memberData['last_order']['date'])->format('Y-m-d H:i') }}</small>
                                @else
                                    <p class="mb-0 text-muted">{{ __('messages.no_data') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Last Bonus -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">{{ __('messages.last_bonus') }}</h6>
                                @if ($memberData['last_bonus'])
                                    <p class="mb-1 text-success"><strong>+{{ $memberData['last_bonus']['value'] }}
                                            {{ __('messages.points') }}</strong></p>
                                    <small
                                        class="text-muted">{{ Carbon::parse($memberData['last_bonus']['date'])->format('Y-m-d H:i') }}</small>
                                @else
                                    <p class="mb-0 text-muted">{{ __('messages.no_data') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Last Penalty -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">{{ __('messages.last_penalty') }}</h6>
                                @if ($memberData['last_penalty'])
                                    <p class="mb-1 text-danger"><strong>-{{ $memberData['last_penalty']['value'] }}
                                            {{ __('messages.points') }}</strong></p>
                                    <small
                                        class="text-muted">{{ Carbon::parse($memberData['last_penalty']['date'])->format('Y-m-d H:i') }}</small>
                                @else
                                    <p class="mb-0 text-muted">{{ __('messages.no_data') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Last Competition -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">{{ __('messages.last_competition') }}</h6>
                                @if ($memberData['last_competition'])
                                    <p class="mb-1"><strong>{{ $memberData['last_competition']['name'] }}</strong></p>
                                    <small
                                        class="text-muted">{{ $memberData['last_competition']['date'] ? Carbon::parse($memberData['last_competition']['date'])->format('Y-m-d H:i') : 'N/A' }}</small>
                                @else
                                    <p class="mb-0 text-muted">{{ __('messages.no_data') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Groups -->
                        <div class="col-12">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">{{ __('messages.groups') }}</h6>
                                @if ($memberData['groups']->isNotEmpty())
                                    @foreach ($memberData['groups'] as $group)
                                        <span class="badge bg-info me-1">{{ $group->name }}</span>
                                    @endforeach
                                @else
                                    <p class="mb-0 text-muted">{{ __('messages.no_groups') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
