@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">

        <!-- Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">{{ __('messages.groups_competitions') }}</h2>
        </div>

        <!-- Groups with Competitions -->
        @foreach($groupsWithCompetitions as $item)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        {{ $item['group']->name }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Last Finished Competition -->
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-flag-checkered me-2"></i>
                                    {{ __('messages.last_finished_competition') }}
                                </h6>
                                @if($item['lastFinished'])
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            {{-- <a href="{{ route('competitions.show', $item['lastFinished']->id) }}"
                                               class="text-decoration-none fw-bold text-info">
                                                {{ $item['lastFinished']->name }}
                                            </a> --}}
                                            <div class="text-muted small mt-1">
                                                <i class="far fa-calendar me-1"></i>
                                                {{ __('messages.ended') }}: {{ Carbon\carbon::parse($item['lastFinished']->end_at)->format('Y-m-d') }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">{{ __('messages.no_finished_competitions') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Active Competitions -->
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-play-circle me-2"></i>
                                    {{ __('messages.active_competitions') }}
                                    <span class="badge bg-warning text-dark ms-2">{{ $item['activeCompetitions']->count() }}</span>
                                </h6>
                                @if($item['activeCompetitions']->isNotEmpty())
                                    <ul class="list-unstyled mb-0">
                                        @foreach($item['activeCompetitions'] as $competition)
                                            <li class="mb-2">
                                                {{-- <a href="{{ route('competitions.show', $competition->id) }}"
                                                   class="text-decoration-none fw-bold text-warning">
                                                    {{ $competition->name }}
                                                </a> --}}
                                                <div class="text-muted small">
                                                    <i class="far fa-calendar me-1"></i>
                                                    {{ Carbon\carbon::parse($competition->start_at)->format('Y-m-d') }} - {{ Carbon\carbon::parse($competition->end_at)->format('Y-m-d') }}
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted mb-0">{{ __('messages.no_active_competitions') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Next Pending Competition -->
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-clock me-2"></i>
                                    {{ __('messages.next_pending_competition') }}
                                </h6>
                                @if($item['nextPending'])
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            {{-- <a href="{{ route('competitions.show', $item['nextPending']->id) }}"
                                               class="text-decoration-none fw-bold text-primary">
                                                {{ $item['nextPending']->name }}
                                            </a> --}}
                                            <div class="text-muted small mt-1">
                                                <i class="far fa-calendar me-1"></i>
                                                {{ __('messages.starts') }}: {{ $item['nextPending']->start_at->format('Y-m-d') }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">{{ __('messages.no_pending_competitions') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if($groupsWithCompetitions->isEmpty())
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ __('messages.no_groups_found') }}
            </div>
        @endif

    </div>
@endsection
