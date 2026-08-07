@extends('layouts.sideBar')

@php
    use App\Enums\BonusPenaltyStatus;
    use App\Enums\BonusPenaltyType;
    use Carbon\Carbon;
@endphp
@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <x-page-header icon="fa-users" :title="__('messages.family') . ': ' . $familyCode"
            :subtitle="__('messages.members') . ': ' . count($membersData)">
            <x-slot:actions>
                <a href="{{ route('families.export', $familyCode) }}" class="btn btn-success">
                    <i class="fa fa-file-excel me-1"></i>Export to Excel
                </a>
                <a href="{{ route('families.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i>{{ __('messages.back') }}
                </a>
            </x-slot>
        </x-page-header>

        @foreach ($membersData as $memberData)
            <div class="card rounded-4 shadow-soft border-0 mb-4">
                <div class="card-header d-flex align-items-center">
                    <img src="{{ $memberData['user']->getFirstMediaUrl('profile_images') ?: asset('images/default.png') }}"
                        alt="{{ $memberData['user']->name }}" class="rounded-circle me-3"
                        style="width: 56px; height: 56px; object-fit: cover; border: 2px solid var(--color-outline-variant);">
                    <div>
                        <h4 class="rs-title-lg mb-1">{{ $memberData['user']->name ?: $memberData['user']->membership_code }}</h4>
                        <p class="mb-0 text-muted">{{ $memberData['user']->membership_code }}</p>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Final Score and Points KPIs -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6 col-sm-6">
                            <div class="rs-stat-card tone-primary">
                                <div class="rs-stat-top">
                                    <span class="rs-label-md">{{ __('messages.final_score') }}</span>
                                    <div class="rs-stat-icon"><i class="fas fa-star"></i></div>
                                </div>
                                <div class="rs-stat-value">{{ $memberData['final_score'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="rs-stat-card tone-success">
                                <div class="rs-stat-top">
                                    <span class="rs-label-md">{{ __('messages.final_points') }}</span>
                                    <div class="rs-stat-icon"><i class="fas fa-coins"></i></div>
                                </div>
                                <div class="rs-stat-value">{{ $memberData['final_points'] }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Quizzes -->
                        <div class="col-md-6">
                            <div class="p-3 rounded-4" style="background: var(--color-surface-container-low);">
                                <h6 class="rs-label-md mb-2">{{ __('messages.quizzes_solved') }}</h6>
                                <p class="h5 mb-0">{{ $memberData['quizzes_solved'] }} /
                                    {{ $memberData['total_quizzes'] }}</p>
                            </div>
                        </div>

                        <!-- Last Quiz -->
                        <div class="col-md-6">
                            <div class="p-3 rounded-4" style="background: var(--color-surface-container-low);">
                                <h6 class="rs-label-md mb-2">{{ __('messages.last_quiz') }}</h6>
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
                            <div class="p-3 rounded-4" style="background: var(--color-surface-container-low);">
                                <h6 class="rs-label-md mb-2">{{ __('messages.last_redeem') }}</h6>
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
                            <div class="p-3 rounded-4" style="background: var(--color-surface-container-low);">
                                <h6 class="rs-label-md mb-2">{{ __('messages.last_bonus') }}</h6>
                                @if ($memberData['last_bonus'])
                                    <p class="mb-1" style="color: var(--color-success);"><strong>+{{ $memberData['last_bonus']['value'] }}
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
                            <div class="p-3 rounded-4" style="background: var(--color-surface-container-low);">
                                <h6 class="rs-label-md mb-2">{{ __('messages.last_penalty') }}</h6>
                                @if ($memberData['last_penalty'])
                                    <p class="mb-1" style="color: var(--color-error);"><strong>-{{ $memberData['last_penalty']['value'] }}
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
                            <div class="p-3 rounded-4" style="background: var(--color-surface-container-low);">
                                <h6 class="rs-label-md mb-2">{{ __('messages.last_competition') }}</h6>
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
                            <div class="p-3 rounded-4" style="background: var(--color-surface-container-low);">
                                <h6 class="rs-label-md mb-2">{{ __('messages.groups') }}</h6>
                                @if ($memberData['groups']->isNotEmpty())
                                    @foreach ($memberData['groups'] as $group)
                                        <span class="badge me-1" style="background: var(--color-on-primary-container); color: var(--color-primary);">{{ $group->name }}</span>
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
