<!DOCTYPE html>
<html lang="en">

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

        #qrCodeManualInput {
            text-align: center;
            letter-spacing: 0.03em;
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
                <p class="auth-brand-subtitle mb-0">Sign in to manage users, competitions, and rewards.</p>
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

                <!-- Login Method Tabs -->
                <div class="login-tabs" role="tablist">
                    <button type="button" id="tabPassword" class="active" role="tab" aria-selected="true">{{ __('messages.login_with_password') }}</button>
                    <button type="button" id="tabQr" role="tab" aria-selected="false">{{ __('messages.login_with_qr') }}</button>
                </div>

                <!-- Password Login Form -->
                <form method="POST" action="{{ route('login') }}" id="passwordPane">
                    @csrf

                    <div class="mb-3">
                        <label for="membership_code" class="form-label">{{ __('messages.membership_code') }}</label>
                        <input id="membership_code" type="text" name="membership_code" required autofocus
                            class="form-control @error('membership_code') is-invalid @enderror" value="{{ old('membership_code') }}">

                        @error('membership_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password with toggle -->
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('messages.password_label') }}</label>
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

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label text-normal text-capitalize" for="remember" style="font-size: 0.9rem; font-weight: 500;">{{ __('messages.remember_me_label') }}</label>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">{{ __('messages.login_button') }}</button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('password.request') }}" class="text-decoration-none small fw-semibold">{{ __('messages.forgot_password_link') }}</a>
                    </div>
                </form>

                <!-- QR Code Login Form -->
                <form method="POST" action="{{ route('login.qr') }}" id="qrPane">
                    @csrf
                    <input type="hidden" name="qr_code" id="qrCodeHidden">

                    <p class="text-muted small mb-3">{{ __('messages.scan_qr_code_instruction') }}</p>

                    <div class="mb-3">
                        <label for="qrCodeManualInput" class="form-label">{{ __('messages.login_with_qr') }}</label>
                        <input type="text" id="qrCodeManualInput" class="form-control @error('qr_code') is-invalid @enderror"
                            placeholder="{{ __('messages.qr_code_input_placeholder') }}" autocomplete="off">
                        @error('qr_code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="qrReader"></div>
                    <div id="qrCameraError" class="alert alert-warning small py-2" style="display:none;"></div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="rememberQr">
                            <label class="form-check-label text-normal text-capitalize" for="rememberQr" style="font-size: 0.9rem; font-weight: 500;">{{ __('messages.remember_me_label') }}</label>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="button" class="btn btn-outline-secondary" id="toggleCameraBtn">{{ __('messages.use_camera_to_scan') }}</button>
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
    </script>

    <script>
        // --- Login method tabs ---
        const tabPassword = document.getElementById('tabPassword');
        const tabQr = document.getElementById('tabQr');
        const passwordPane = document.getElementById('passwordPane');
        const qrPane = document.getElementById('qrPane');

        function showPasswordTab() {
            tabPassword.classList.add('active');
            tabQr.classList.remove('active');
            tabPassword.setAttribute('aria-selected', 'true');
            tabQr.setAttribute('aria-selected', 'false');
            passwordPane.style.display = '';
            qrPane.style.display = 'none';
            stopQrScanner();
        }

        function showQrTab() {
            tabQr.classList.add('active');
            tabPassword.classList.remove('active');
            tabQr.setAttribute('aria-selected', 'true');
            tabPassword.setAttribute('aria-selected', 'false');
            qrPane.style.display = '';
            passwordPane.style.display = 'none';
            qrCodeManualInput.focus();
        }

        tabPassword.addEventListener('click', showPasswordTab);
        tabQr.addEventListener('click', showQrTab);

        @if ($errors->has('qr_code'))
            showQrTab();
        @endif

        // --- Manual / hardware-scanner input ---
        const qrCodeManualInput = document.getElementById('qrCodeManualInput');
        const qrCodeHidden = document.getElementById('qrCodeHidden');
        const qrForm = document.getElementById('qrPane');

        function submitQrCode(value) {
            const trimmed = (value || '').trim();
            if (!trimmed) {
                return;
            }
            qrCodeHidden.value = trimmed;
            qrForm.submit();
        }

        // Hardware QR/barcode scanners behave like a keyboard: they type the
        // payload then send Enter. Submit as soon as Enter arrives.
        qrCodeManualInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitQrCode(qrCodeManualInput.value);
            }
        });

        // --- Optional camera-based scanning (progressive enhancement) ---
        const toggleCameraBtn = document.getElementById('toggleCameraBtn');
        const qrReaderEl = document.getElementById('qrReader');
        const qrCameraError = document.getElementById('qrCameraError');
        let html5QrCode = null;
        let cameraActive = false;

        function stopQrScanner() {
            if (html5QrCode && cameraActive) {
                html5QrCode.stop().catch(() => {});
                cameraActive = false;
            }
            qrReaderEl.style.display = 'none';
            toggleCameraBtn.textContent = '{{ __('messages.use_camera_to_scan') }}';
        }

        toggleCameraBtn.addEventListener('click', function() {
            if (cameraActive) {
                stopQrScanner();
                return;
            }

            qrCameraError.style.display = 'none';

            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js';
            script.onload = startCamera;
            script.onerror = function() {
                qrCameraError.textContent = '{{ __('messages.camera_not_available') }}';
                qrCameraError.style.display = '';
            };

            if (window.Html5Qrcode) {
                startCamera();
            } else {
                document.head.appendChild(script);
            }
        });

        function startCamera() {
            qrReaderEl.style.display = '';
            html5QrCode = new Html5Qrcode('qrReader');

            html5QrCode.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: 220 },
                function(decodedText) {
                    stopQrScanner();
                    submitQrCode(decodedText);
                },
                function() {
                    // ignore per-frame decode failures
                }
            ).then(function() {
                cameraActive = true;
                toggleCameraBtn.textContent = '{{ __('messages.stop_camera') }}';
            }).catch(function() {
                qrCameraError.textContent = '{{ __('messages.camera_not_available') }}';
                qrCameraError.style.display = '';
                qrReaderEl.style.display = 'none';
            });
        }
    </script>
</body>

</html>
