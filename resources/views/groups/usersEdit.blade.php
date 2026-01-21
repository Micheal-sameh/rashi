@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">

        <!-- Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">
                {{ __('messages.update_users_for_group') }} : {{ $group->name }}
            </h2>
            <a href="{{ route('groups.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-1"></i> {{ __('messages.back') }}
            </a>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Form Card -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <form action="{{ route('groups.updateUsers', $group->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Search Bar -->
                    <div class="mb-3">
                        <label for="search" class="form-label fw-semibold">
                            {{ __('messages.search_users') }}
                        </label>
                        <input type="text" id="search" class="form-control form-control-lg"
                            placeholder="{{ __('messages.search_for_user') }}" onkeyup="searchUsers()">
                    </div>

                    <!-- Users Grid -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.select_users') }}</label>
                        <div id="usersList" class="d-flex flex-wrap gap-2 border rounded-3 p-3"
                            style="max-height: 400px; overflow-y: auto;">
                            @forelse ($users as $user)
                                <div class="user-card p-2 border rounded-2 d-flex align-items-center w-100 w-md-auto">
                                    <input class="form-check-input me-2" type="checkbox" name="users[]"
                                        value="{{ $user->id }}" id="user_{{ $user->id }}"
                                        {{ $group->users->contains($user->id) ? 'checked' : '' }}>
                                    <label class="form-check-label mb-0" for="user_{{ $user->id }}">
                                        {{ $user->name }}
                                    </label>
                                </div>
                            @empty
                                <p class="text-muted">{{ __('messages.no_users_found') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 mt-2">
                        <i class="fa fa-save me-1"></i> {{ __('messages.update') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function searchUsers() {
            const filter = document.getElementById('search').value.toLowerCase();
            const userCards = document.querySelectorAll('#usersList .user-card');

            userCards.forEach(card => {
                const label = card.querySelector('label').textContent.toLowerCase();
                card.style.display = label.includes(filter) ? '' : 'none';
            });
        }
    </script>
@endpush

@push('styles')
    <style>
        /* User Card Hover */
        .user-card:hover {
            background-color: #f8f9fa;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
            cursor: pointer;
        }

        /* Scrollbar Styling */
        #usersList::-webkit-scrollbar {
            width: 6px;
        }

        #usersList::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }

        /* Mobile responsive cards */
        @media (max-width: 576px) {
            .user-card {
                width: 100%;
            }
        }
    </style>
@endpush
