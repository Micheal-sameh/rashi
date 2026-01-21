@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">{{ __('messages.quizzes') }}</h2>
            <a href="{{ route('quizzes.create') }}" class="btn btn-success">
                <i class="fa fa-plus-circle me-1"></i> {{ __('messages.create_quizzes') }}
            </a>
        </div>

        {{-- Search Filters --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body">
                <form action="{{ route('quizzes.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="competition_id" class="form-label">{{ __('messages.competition') }}</label>
                            <select name="competition_id" id="competition_id" class="form-select">
                                <option value="">{{ __('messages.all') }}</option>
                                @foreach ($competitions as $competition)
                                    <option value="{{ $competition->id }}"
                                        {{ request('competition_id') == $competition->id ? 'selected' : '' }}>
                                        {{ $competition->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="search" class="form-label">{{ __('messages.search') }}</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="{{ __('messages.search_quiz_name') }}"
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search me-1"></i> {{ __('messages.search') }}
                            </button>
                            <a href="{{ route('quizzes.index') }}" class="btn btn-secondary">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($quizzes->count())
            {{-- Desktop Table --}}
            <div class="card shadow-sm border-0 rounded-4 d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.competition') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quizzes as $quiz)
                                <tr>
                                    <td class="fw-semibold">{{ $quiz->name }}</td>
                                    <td>{{ $quiz->competition->name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($quiz->date)->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('quizzes.edit', $quiz->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('quizzes.delete', $quiz->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="delete-modal"
                                                    data-message="{{ __('messages.confirm_delete_quiz', ['name' => $quiz->name]) }}"
                                                    title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('questions.create', ['quiz_id' => $quiz->id]) }}"
                                                class="btn btn-sm btn-outline-success"
                                                title="{{ __('messages.create_question') }}">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($quizzes->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $quizzes->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            {{-- Mobile Cards --}}
            <div class="d-md-none">
                @foreach ($quizzes as $quiz)
                    <div class="card mb-3 shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h5 class="fw-bold text-primary mb-2">{{ $quiz->name }}</h5>
                            <p class="mb-1"><strong>{{ __('messages.competition') }}:</strong>
                                {{ $quiz->competition->name ?? '-' }}</p>
                            <p class="mb-3"><strong>{{ __('messages.date') }}:</strong>
                                {{ \Carbon\Carbon::parse($quiz->date)->format('d M Y') }}</p>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('quizzes.edit', $quiz->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('quizzes.delete', $quiz->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="delete-modal"
                                        data-message="{{ __('messages.confirm_delete_quiz', ['name' => $quiz->name]) }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                                <a href="{{ route('questions.create', ['quiz_id' => $quiz->id]) }}"
                                    class="btn btn-sm btn-outline-success">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if ($quizzes->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $quizzes->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle me-2"></i> No quizzes found.
            </div>
        @endif
    </div>

    {{-- Image Modal (if you need for future previewing images in quizzes) --}}
    <div id="imageModal" class="modal" onclick="closeModal()">
        <span class="close">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            max-width: 90%;
            max-height: 80%;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.5);
        }

        .close {
            position: absolute;
            top: 15px;
            right: 25px;
            font-size: 35px;
            color: #fff;
            cursor: pointer;
        }

        .close:hover {
            color: #bbb;
        }
    </style>

    <script>
        function openModal(src) {
            document.getElementById('imageModal').style.display = "flex";
            document.getElementById('modalImage').src = src;
        }

        function closeModal() {
            document.getElementById('imageModal').style.display = "none";
        }
    </script>
@endsection
