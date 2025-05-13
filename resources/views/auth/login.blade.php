@extends('layouts.sideBar')

<title>{{ config('app.name') }}</title>

@section('content')
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100">
            <div class="col-md-6 mx-auto">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-header text-center bg-dark text-white py-4">
                        <h3 class="font-weight-bold">{{ __('messages.login') }}</h3>
                    </div>
                    @if(session('error'))
                    <div class="alert alert-success fixed-top w-50 mx-auto fade show" id="flashMessage" style="top: 20px; left: 50%; transform: translateX(-50%); z-index: 1050;">
                        {{ session('error') }}
                    </div>
                @endif
                    <div class="card-body p-5">
                        <!-- Error messages -->
                        <div id="error-messages" class="alert alert-danger d-none">
                            <ul id="error-list"></ul>
                        </div>

                        <!-- Login form -->
                        <form id="login-form" method="POST" action="{{ route('loginPage') }}">
                            @csrf
                            <div class="form-group mb-4">
                                <label for="membership_code" class="form-label">{{ __('messages.membership_code') }}</label>
                                <input type="membership_code" id="membership_code" class="form-control form-control-lg" name="membership_code" required autofocus placeholder="{{ __('messages.enter') }} {{ __('messages.membership_code') }}">
                            </div>

                            <div class="form-group mb-4">
                                <label for="password" class="form-label">{{ __('messages.password') }}</label>
                                <input type="password" id="password" class="form-control form-control-lg" name="password" required placeholder="{{ __('messages.enter') }} {{ __('messages.password') }}">
                            </div>

                            <div class="form-group form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    {{ __('messages.remember_me') }}
                                </label>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-dark w-100 py-3 rounded-3 font-weight-bold">
                                    {{ __('messages.login') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* General Styles */
        body {
            background-color: #f5f5f5;
            font-family: 'Arial', sans-serif;
            background-image: url("http://192.168.1.6:8000/images/logo.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }

        .container {
            max-width: 500px;
        }

        .card {
            border-radius: 10px;
            border: none;
            background-color: rgba(255, 255, 255, 0.9); /* Optional: make the card slightly transparent */
        }

        /* Button Styling */
        .btn-dark {
            background-color: #343a40;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
        }

        .btn-dark:hover {
            background-color: #23272b;
        }

        /* Input Styles */
        .form-control-lg {
            font-size: 16px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: none;
            margin-bottom: 15px;
        }

        /* Form Error Messages */
        #error-messages {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Text Link Styling */
        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
@endsection

@section('scripts')
    <script>
        const form = document.getElementById('login-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const errors = [];
            if (!document.getElementById('email').value) {
                errors.push('{{ __("Email is required.") }}');
            }
            if (!document.getElementById('password').value) {
                errors.push('{{ __("Password is required.") }}');
            }

            if (errors.length > 0) {
                const errorMessagesDiv = document.getElementById('error-messages');
                const errorList = document.getElementById('error-list');

                errorList.innerHTML = '';
                errors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    errorList.appendChild(li);
                });

                errorMessagesDiv.classList.remove('d-none');
            } else {
                form.submit();
            }
        });
    </script>
@endsection
