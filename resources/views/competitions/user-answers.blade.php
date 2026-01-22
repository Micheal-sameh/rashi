@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">{{ __('messages.competitions') }}: {{ $competition->name }}</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('competitions.leaderboard.export', array_merge(['id' => $competition->id], request()->only(['user_ids', 'group_id']))) }}" class="btn btn-primary">
                    <i class="fa fa-download me-1"></i> {{ __('messages.export_leaderboard_pdf') }}
                </a>
                <a href="{{ route('competitions.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter Form -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('competitions.userAnswers', $competition->id) }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="group_id" class="form-label fw-semibold">{{ __('messages.group') }}</label>
                        <select name="group_id" id="group_id" class="form-select">
                            <option value="">{{ __('messages.all_groups') }}</option>
                            @foreach(\App\Models\Group::all() as $group)
                                <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="user_ids" class="form-label fw-semibold">{{ __('messages.select_users') }}</label>
                        <select name="user_ids[]" id="user_ids" class="form-select" multiple>
                            @foreach($users as $user)
                                @php
                                    $fullUser = \App\Models\User::with('groups')->find($user->id);
                                    $groupIds = $fullUser ? $fullUser->groups->pluck('id')->toArray() : [];
                                @endphp
                                <option value="{{ $user->id }}"
                                    data-groups="{{ json_encode($groupIds) }}"
                                    {{ in_array($user->id, request('user_ids', [])) ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{ __('messages.hold_ctrl_select_multiple') }}</small>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-filter me-1"></i> {{ __('messages.apply_filters') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if ($competition->quizzes->count())
            @foreach ($competition->quizzes as $quiz)
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">{{ $quiz->name }} ({{ \Carbon\Carbon::parse($quiz->date)->format('d M Y') }})</h5>
                    </div>
                    <div class="card-body">
                        @if ($quiz->questions->count())
                            @if(isset($quizStats[$quiz->id]) && count($quizStats[$quiz->id]))
                                <div class="mb-4">
                                    <h6 class="fw-bold">{{ __('messages.user_summary') }}</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('messages.user') }}</th>
                                                    <th>{{ __('messages.total_correct_answers') }}</th>
                                                    <th>{{ __('messages.total_points') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($quizStats[$quiz->id] as $stat)
                                                    <tr>
                                                        <td>{{ $stat['name'] }}</td>
                                                        <td>{{ $stat['total_correct'] }} / {{ $stat['total_questions'] }}</td>
                                                        <td>{{ $stat['total_points'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @foreach ($quiz->questions as $question)
                                <div class="mb-4">
                                    <h6 class="fw-bold">{{ $question->question }} ({{ $question->points }} {{ __('messages.points') }})</h6>
                                    @if ($question->userAnswers->count())
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('messages.user') }}</th>
                                                        <th>{{ __('messages.answer') }}</th>
                                                        <th>{{ __('messages.correct') }}</th>
                                                        <th>{{ __('messages.points') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($question->userAnswers as $userAnswer)
                                                        <tr>
                                                            <td>{{ $userAnswer->user->name }}</td>
                                                            <td>{{ $userAnswer->answer->answer }}</td>
                                                            <td>
                                                                @if($userAnswer->answer->is_correct)
                                                                    <i class="fa fa-check text-success"></i> {{ __('messages.yes') }}
                                                                @else
                                                                    <i class="fa fa-times text-danger"></i> {{ __('messages.no') }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $userAnswer->points }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">{{ __('messages.no_answers_submitted_yet') }}</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">{{ __('messages.no_questions_in_this_quiz') }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle me-2"></i> {{ __('messages.no_quizzes_in_this_competition') }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const groupSelect = document.getElementById('group_id');
            const userSelect = document.getElementById('user_ids');
            const allOptions = Array.from(userSelect.options);

            groupSelect.addEventListener('change', function() {
                const selectedGroupId = this.value;

                // Clear current options
                userSelect.innerHTML = '';

                // Filter and add options based on selected group
                allOptions.forEach(option => {
                    if (!selectedGroupId) {
                        // Show all users if no group selected
                        userSelect.appendChild(option.cloneNode(true));
                    } else {
                        // Check if user belongs to selected group
                        const userGroups = JSON.parse(option.dataset.groups || '[]');
                        if (userGroups.includes(parseInt(selectedGroupId))) {
                            userSelect.appendChild(option.cloneNode(true));
                        }
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
