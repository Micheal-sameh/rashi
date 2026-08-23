<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>

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

        .auth-brand-subtitle {
            color: var(--color-on-surface-variant);
            font-size: 1rem;
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

        .lang-switcher-fixed {
            position: fixed;
            top: 20px;
            inset-inline-end: 20px;
            z-index: 10;
        }

        .lang-switcher-fixed .btn {
            border-radius: var(--radius-btn);
            border-color: var(--color-outline-variant);
            color: var(--color-on-surface-variant);
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.03em;
        }

        .login-tabs {
            display: flex;
            gap: 4px;
            background: var(--color-background);
            border-radius: var(--radius-btn);
            padding: 4px;
            margin-bottom: 1.5rem;
        }

        .login-tabs button {
            flex: 1;
            border: none;
            background: transparent;
            padding: 0.6rem 1rem;
            border-radius: calc(var(--radius-btn) - 2px);
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--color-on-surface-variant);
            transition: background 0.2s ease, color 0.2s ease;
        }

        .login-tabs button.active {
            background: var(--color-surface-container-lowest);
            color: var(--color-primary);
            box-shadow: var(--shadow-1);
        }

        #qrPane {
            display: none;
        }

        #qrReader {
            display: none;
            width: 100%;
            border-radius: var(--radius-input);
            overflow: hidden;
            margin-bottom: 1rem;
            background: #000;
        }

    </style>
</head>

<body>

    <div class="dropdown lang-switcher-fixed">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="langSwitchToggle"
            data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('messages.language') }}">
            {{ strtoupper(app()->getLocale()) }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langSwitchToggle">
            <li>
                <a class="dropdown-item {{ app()->isLocale('en') ? 'active' : '' }}" href="{{ route('lang.switch', 'en') }}">
                    English
                </a>
            </li>
            <li>
                <a class="dropdown-item {{ app()->isLocale('ar') ? 'active' : '' }}" href="{{ route('lang.switch', 'ar') }}">
                    العربية
                </a>
            </li>
        </ul>
    </div>

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
                <p class="auth-brand-subtitle mb-0">{{ __('messages.auth_subtitle') }}</p>
            </div>

            <div class="auth-card">
                <!-- Global Messages -->
                @if (session('error'))
                    <div class="alert alert-danger small">
                        {{ session('error') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success small">
                        {{ session('success') }}
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

            <div class="d-grid">
                <a href="{{ route('avarewase.login') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i> {{ __('messages.login_with_sso') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
