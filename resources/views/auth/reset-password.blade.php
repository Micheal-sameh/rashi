<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - {{ __('messages.reset_password_title') }}</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-primary: #3525cd;
            --color-primary-container: #4f46e5;
            --color-on-primary: #ffffff;
            --color-on-surface: #191c1e;
            --color-on-surface-variant: #464555;
            --color-outline: #777587;
            --color-outline-variant: #c7c4d8;
            --color-background: #f7f9fb;
            --color-surface-container-lowest: #ffffff;
            --radius-card: 16px;
            --radius-btn: 10px;
            --radius-input: 8px;
            --shadow-1: 0px 4px 6px -1px rgba(15, 23, 42, 0.05), 0px 2px 4px -2px rgba(15, 23, 42, 0.05);
        }

        body {
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--color-background);
            color: var(--color-on-surface);
        }

        .auth-brand-title {
            font-weight: 900;
            font-size: 2rem;
            color: var(--color-primary);
            margin-bottom: 0.25rem;
        }

        .auth-card {
            background: var(--color-surface-container-lowest);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-1);
            border: 1px solid var(--color-outline-variant);
            padding: 2.5rem;
        }

        label,
        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: var(--color-on-surface-variant);
        }

        .form-control {
            border-radius: var(--radius-input);
            border: 1px solid var(--color-outline-variant);
            padding: 0.75rem 1rem;
            background: var(--color-surface-container-lowest);
        }

        .form-control:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(53, 37, 205, 0.12);
        }

        .btn-primary {
            background: var(--color-primary);
            border: none;
            border-radius: var(--radius-btn);
            padding: 0.75rem 1.25rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: var(--color-primary-container);
        }

        .btn-outline-secondary {
            border-radius: var(--radius-input);
            border-color: var(--color-outline-variant);
            color: var(--color-outline);
        }

        a {
            color: var(--color-primary);
        }

        .alert {
            border-radius: 12px;
            border: none;
        }
    </style>
</head>

<body>

    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="w-100" style="max-width: 420px;">
            @php
                $logo = App\Models\Setting::where('name', 'logo')->first();
            @endphp

            <!-- Brand -->
            <div class="text-center mb-4">
                @if ($logo?->getFirstMediaUrl('app_logo'))
                    <img src="{{ $logo?->getFirstMediaUrl('app_logo') }}" alt="Logo" class="img-fluid mb-3"
                        style="max-height: 72px; border-radius: 12px;">
                @endif
                <h1 class="auth-brand-title">{{ config('app.name') }}</h1>
            </div>

            <div class="auth-card">
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

                <!-- Reset Password Form -->
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <div class="text-center mb-4">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;border-radius:var(--radius-input);background:rgba(53,37,205,0.08);color:var(--color-primary);">
                            <i class="bi bi-shield-lock fs-5"></i>
                        </div>
                        <h4 class="fw-bold mb-1">{{ __('messages.reset_password_title') }}</h4>
                        <p class="text-muted small mb-0">{{ __('messages.reset_password_description') }}</p>
                    </div>

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('messages.email_address') }}</label>
                        <input id="email" type="email" name="email" required autofocus
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $request->email) }}">

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('messages.new_password') }}</label>
                        <div class="input-group">
                            <input id="password" type="password" name="password" required
                                class="form-control @error('password') is-invalid @enderror">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">{{ __('messages.confirm_new_password') }}</label>
                        <div class="input-group">
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                class="form-control @error('password_confirmation') is-invalid @enderror">
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">{{ __('messages.reset_password_button') }}</button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('loginPage') }}" class="text-decoration-none small fw-semibold">{{ __('messages.back_to_login') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById("togglePassword").addEventListener("click", function() {
            let password = document.getElementById("password");
            let icon = this.querySelector("i");
            if (password.type === "password") {
                password.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                password.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        });

        document.getElementById("toggleConfirmPassword").addEventListener("click", function() {
            let password = document.getElementById("password_confirmation");
            let icon = this.querySelector("i");
            if (password.type === "password") {
                password.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                password.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        });
    </script>
</body>

</html>
