<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Forgot Password</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow rounded-4 p-4 w-100" style="max-width: 400px;">
            @php
                $logo = App\Models\Setting::where('name', 'logo')->first();
            @endphp

            <!-- Logo -->
            <div class="text-center mb-4">
                <img src="{{ $logo?->getFirstMediaUrl('app_logo') }}" alt="Logo" class="img-fluid"
                    style="max-height: 80px;">
            </div>

            <!-- Global Messages -->
            @if (session('status'))
                <div class="alert alert-success small">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger small">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Forgot Password Form -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="text-center mb-4">
                    <h4>{{ __('messages.forgot_password_title') }}</h4>
                    <p class="text-muted small">{{ __('messages.forgot_password_description') }}</p>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('messages.email_address') }}</label>
                    <input id="email" type="email" name="email" required autofocus
                        class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">

                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">{{ __('messages.send_password_reset_link') }}</button>
                </div>

                <div class="text-center">
                    <a href="{{ route('loginPage') }}" class="text-decoration-none">{{ __('messages.back_to_login') }}</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
