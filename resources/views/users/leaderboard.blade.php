@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">{{ __('messages.leaderboard') }}</h2>
        </div>

        <!-- Filter Form -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('users.leaderboard') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="group_id" class="form-label">{{ __('messages.group') }}</label>
                        <select name="group_id" id="group_id" class="form-select">
                            <option value="">{{ __('messages.all_groups') }}</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }} ({{ $group->abbreviation }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">{{ __('messages.filter') }}</button>
                    </div>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($users->count())
            <!-- Desktop Table View -->
            <div class="card shadow-sm border-0 rounded-4 d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.rank') }}</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.score') }}</th>
                                <th>{{ __('messages.points') }}</th>
                                <th>{{ __('messages.image') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td class="fw-semibold">{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $user->name }}</td>
                                    <td><span class="badge bg-warning text-dark">{{ $user->score }}</span></td>
                                    <td><span class="badge bg-success">{{ $user->points }}</span></td>
                                    <td>
                                        @if ($user->image)
                                            <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="zoomable-image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;" onclick="openModal('{{ asset('storage/' . $user->image) }}')">
                                        @else
                                            <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="zoomable-image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;" onclick="openModal('{{ asset('images/default-avatar.png') }}')">
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                @foreach ($users as $index => $user)
                    <div class="card shadow-sm border-0 rounded-4 mb-3">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <span class="badge bg-primary fs-6 fw-bold">{{ $index + 1 }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1 fw-bold">{{ $user->name }}</h6>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-warning text-dark">{{ __('messages.score') }}: {{ $user->score }}</span>
                                    <span class="badge bg-success">{{ __('messages.points') }}: {{ $user->points }}</span>
                                </div>
                            </div>
                            <div>
                                @if ($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="zoomable-image rounded-circle" style="width: 60px; height: 60px; object-fit: cover;" onclick="openModal('{{ asset('storage/' . $user->image) }}')">
                                @else
                                    <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="zoomable-image rounded-circle" style="width: 60px; height: 60px; object-fit: cover;" onclick="openModal('{{ asset('images/default-avatar.png') }}')">
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle me-2"></i> No users found.
            </div>
        @endif
    </div>

    <!-- Image Modal -->
    <div id="popupModal" class="modal" onclick="closeModal()">
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

        .zoomable-image {
            cursor: pointer;
            transition: transform 0.3s;
        }

        .zoomable-image:hover {
            transform: scale(1.1);
        }
    </style>

    <script>
        function openModal(src) {
            document.getElementById('popupModal').style.display = "flex";
            document.getElementById('modalImage').src = src;
        }

        function closeModal() {
            document.getElementById('popupModal').style.display = "none";
        }
    </script>
@endsection
