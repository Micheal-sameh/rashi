@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">{{ __('messages.competitions') }}: {{ $competition->name }}</h2>
            <div>
                <form method="GET" action="{{ route('competitions.userAnswers', $competition->id) }}" class="d-inline">
                    <select name="user_id" class="form-select d-inline w-auto me-2" onchange="this.form.submit()">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </form>
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
                                    <h6 class="fw-bold">User Summary</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('messages.user') }}</th>
                                                    <th>Total Correct Answers</th>
                                                    <th>Total Points</th>
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
                                    <h6 class="fw-bold">{{ $question->question }} ({{ $question->points }} points)</h6>
                                    @if ($question->userAnswers->count())
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('messages.user') }}</th>
                                                        <th>{{ __('messages.answer') }}</th>
                                                        <th>Correct</th>
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
                                                                    <i class="fa fa-check text-success"></i> Yes
                                                                @else
                                                                    <i class="fa fa-times text-danger"></i> No
                                                                @endif
                                                            </td>
                                                            <td>{{ $userAnswer->points }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">No answers submitted yet.</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No questions in this quiz.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle me-2"></i> No quizzes in this competition.
            </div>
        @endif
    </div>
@endsection
