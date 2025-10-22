@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">{{ __('messages.leaderboard') }}</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($users->count())
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
                                            <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="zoomable-image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;" onclick="openPopup('{{ asset('storage/' . $user->image) }}')">
                                        @else
                                            <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="zoomable-image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;" onclick="openPopup('{{ asset('images/default-avatar.png') }}')">
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
