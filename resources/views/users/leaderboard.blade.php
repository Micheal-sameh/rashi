@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <x-page-header icon="fa-trophy" :title="__('messages.leaderboard')" subtitle="Top performing members ranked by score and points.">
            <x-slot:actions>
                <a href="{{ route('users.leaderboard.export', request()->query()) }}" class="btn btn-success">
                    <i class="fa fa-download me-2"></i>Export to PDF
                </a>
            </x-slot>
        </x-page-header>

        <!-- Filter Form -->
        <div class="card rounded-4 shadow-soft mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('users.leaderboard') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="group_id" class="rs-label-md">{{ __('messages.group') }}</label>
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
                        <button type="submit" class="btn btn-primary w-100">{{ __('messages.filter') }}</button>
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
            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="rs-stat-card tone-primary">
                        <div class="rs-stat-top">
                            <span class="rs-label-md">{{ __('messages.rank') }} 1</span>
                            <div class="rs-stat-icon"><i class="fas fa-trophy"></i></div>
                        </div>
                        <div class="rs-stat-value">{{ $users->first()->name }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="rs-stat-card tone-warning">
                        <div class="rs-stat-top">
                            <span class="rs-label-md">{{ __('messages.score') }}</span>
                            <div class="rs-stat-icon"><i class="fas fa-star"></i></div>
                        </div>
                        <div class="rs-stat-value">{{ $users->first()->score }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="rs-stat-card tone-success">
                        <div class="rs-stat-top">
                            <span class="rs-label-md">{{ __('messages.points') }}</span>
                            <div class="rs-stat-icon"><i class="fas fa-coins"></i></div>
                        </div>
                        <div class="rs-stat-value">{{ $users->first()->points }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="rs-stat-card tone-secondary">
                        <div class="rs-stat-top">
                            <span class="rs-label-md">{{ __('messages.users') }}</span>
                            <div class="rs-stat-icon"><i class="fas fa-users"></i></div>
                        </div>
                        <div class="rs-stat-value">{{ $users->count() }}</div>
                    </div>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="card rounded-4 shadow-soft overflow-hidden d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="rs-label-md">{{ __('messages.rank') }}</th>
                                <th class="rs-label-md">{{ __('messages.name') }}</th>
                                <th class="rs-label-md">{{ __('messages.score') }}</th>
                                <th class="rs-label-md">{{ __('messages.points') }}</th>
                                <th class="rs-label-md">{{ __('messages.image') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td class="fw-bold">
                                        @if ($index < 3)
                                            <span class="badge" style="background: rgba(53, 37, 205, 0.1); color: var(--color-primary);">
                                                <i class="fas fa-trophy me-1"></i>{{ $index + 1 }}
                                            </span>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $user->name }}</td>
                                    <td><span class="badge" style="background: var(--color-warning-container); color: var(--color-warning);">{{ $user->score }}</span></td>
                                    <td><span class="badge" style="background: var(--color-success-container); color: var(--color-on-success-container);">{{ $user->points }}</span></td>
                                    <td>
                                        @if ($user->image)
                                            <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="zoomable-image" style="width: 44px; height: 44px; object-fit: cover; border-radius: 50%;" onclick="openModal('{{ asset('storage/' . $user->image) }}')">
                                        @else
                                            <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="zoomable-image" style="width: 44px; height: 44px; object-fit: cover; border-radius: 50%;" onclick="openModal('{{ asset('images/default-avatar.png') }}')">
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
                    <div class="card rounded-4 shadow-soft mb-3">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="me-3">
                                <span class="badge fs-6 fw-bold" style="background: rgba(53, 37, 205, 0.1); color: var(--color-primary);">{{ $index + 1 }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1 fw-bold">{{ $user->name }}</h6>
                                <div class="d-flex gap-2">
                                    <span class="badge" style="background: var(--color-warning-container); color: var(--color-warning);">{{ __('messages.score') }}: {{ $user->score }}</span>
                                    <span class="badge" style="background: var(--color-success-container); color: var(--color-on-success-container);">{{ __('messages.points') }}: {{ $user->points }}</span>
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
