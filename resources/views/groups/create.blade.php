@extends('layouts.sideBar')

@section('content')
    <div class="container py-4" style="max-width: 600px;">

        <!-- Heading -->
        <h2 class="mb-4 fw-bold text-primary">{{ __('messages.create_groups') }}</h2>

        <!-- Form Card -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">

                <form action="{{ route('groups.store') }}" method="POST">
                    @csrf

                    {{-- Group Name --}}
                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">{{ __('messages.name') }}</label>
                        <input type="text" name="name" id="name"
                            class="form-control form-control-lg @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="{{ __('messages.enter') }} {{ __('messages.name') }}"
                            required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Select Users --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('messages.select_users') }}</label>
                        <div id="usersList" class="border rounded-3 p-3" style="max-height: 350px; overflow-y: auto;">
                            @foreach ($users as $user)
                                <div class="form-check user-card mb-2 p-2 rounded-2 d-flex align-items-center">
                                    <input class="form-check-input me-2" type="checkbox" name="users[]"
                                        value="{{ $user->id }}" id="user_{{ $user->id }}">
                                    <label class="form-check-label" for="user_{{ $user->id }}">
                                        {{ $user->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('users')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success w-100 py-2 rounded-3">
                            <i class="fa fa-plus-circle me-1"></i> {{ __('messages.create') }}
                        </button>
                        <a href="{{ route('groups.index') }}" class="btn btn-secondary w-100 py-2 rounded-3">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* User cards hover */
        .user-card:hover {
            background-color: #f8f9fa;
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        }

        /* Scrollbar styling */
        #usersList::-webkit-scrollbar {
            width: 6px;
        }

        #usersList::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }

        /* Mobile responsive */
        @media (max-width: 576px) {
            #usersList {
                max-height: 250px;
            }
        }
    </style>
@endsection
