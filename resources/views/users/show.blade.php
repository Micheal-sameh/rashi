@extends('layouts.sideBar')

@section('content')
    <div class="container mt-4" style="width:95%">
        <!-- User Details Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>{{ __('messages.user_details') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>{{ __('messages.name') }}:</strong> {{ $user->name }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ __('messages.email') }}:</strong> {{ $user->email }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ __('messages.groups') }}:</strong>
                        {{ $user->groups->pluck('name')->join(', ') ?? __('messages.not_assigned') }}
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <strong>{{ __('messages.points') }}:</strong> {{ $user->points_sum ?? '0' }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ __('messages.score') }}:</strong> {{ $user->score }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ __('messages.created_at') }}:</strong> {{ $user->created_at->format('Y-m-d') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Points History Table -->
        <div class="card">
            <div class="card-header">
                <h4>{{ __('messages.points_history') }}</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.amount') }}</th>
                                <th>{{ __('messages.total_points') }}</th>
                                <th>{{ __('messages.total_score') }}</th>
                                <th>{{ __('messages.subject_name') }}</th>
                                <th>{{ __('messages.subject_type') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($points as $point)
                                <tr>
                                    <td>{{ $point->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $point->amount }}</td>
                                    <td>{{ $point->points }}</td>
                                    <td>{{ $point->score }}</td>
                                    <td>{{ optional($point->subject)->name ?? $point->subject_id }}
                                    </td>
                                    <td>{{ __('messages.' . class_basename($point->subject_type)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        {{ __('messages.no_points_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($points, 'links'))
                    <div class="mt-3 ms-3">
                        {{ $points->links() }}
                    </div>
                @endif
            </div>
        </div>


    </div>
@endsection
