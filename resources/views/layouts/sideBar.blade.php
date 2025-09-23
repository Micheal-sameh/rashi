<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/jpg">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts & Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f6f8fa;
        }

        /* Sidebar */
        #sidebar {
            position: fixed;
            top: 0;
            height: 100vh;
            width: 230px;
            background: linear-gradient(180deg, #0d6efd, #2563eb);
            color: white;
            transition: transform 0.3s ease-in-out;
            z-index: 1050;
            padding: 1rem 0.5rem;
        }

        [dir="rtl"] #sidebar {
            right: 0;
            left: auto;
        }

        #sidebar .logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        #sidebar .logo img {
            max-width: 120px;
            border-radius: 50%;
        }

        #sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #sidebar ul li a {
            display: block;
            padding: 10px 15px;
            margin: 4px 10px;
            border-radius: 10px;
            color: white;
            text-decoration: none;
            transition: background 0.2s;
            font-weight: 500;
        }

        #sidebar ul li a:hover,
        #sidebar ul li a.active {
            background: rgba(255, 255, 255, 0.2);
        }

        #sidebar .world-icon-btn {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.15);
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            cursor: pointer;
            transition: background 0.2s;
        }

        #sidebar .world-icon-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Content */
        .content-area {
            margin-left: 230px;
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }

        [dir="rtl"] .content-area {
            margin-left: 0;
            margin-right: 230px;
        }

        /* Mobile */
        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
            }

            [dir="rtl"] #sidebar {
                transform: translateX(100%);
            }

            #sidebar.show {
                transform: translateX(0);
            }

            .content-area {
                margin: 0;
                padding: 1rem;
            }
        }

        /* Sidebar toggle btn */
        .btn-toggle-sidebar {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
        }

        [dir="rtl"] .btn-toggle-sidebar {
            right: 15px;
            left: auto;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div id="sidebar">
        <div class="logo">
            @php
                $logo = App\Models\Setting::where('name', 'logo')->first();
            @endphp
            <img src="{{ $logo?->getFirstMediaUrl('app_logo') }}" alt="Logo">
        </div>

        <ul>
        @auth
            <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}"><i
                        class="fas fa-home me-2"></i>{{ __('messages.home') }}</a></li>
            <li><a href="{{ route('users.index') }}"><i class="fas fa-users me-2"></i>{{ __('messages.users') }}</a>
            </li>
            <li><a href="{{ route('competitions.index') }}"><i
                        class="fas fa-trophy me-2"></i>{{ __('messages.competitions') }}</a></li>
            <li><a href="{{ route('quizzes.index') }}"><i
                        class="fas fa-question-circle me-2"></i>{{ __('messages.quizzes') }}</a></li>
            <li><a href="{{ route('questions.index') }}"><i
                        class="fas fa-edit me-2"></i>{{ __('messages.questions') }}</a></li>
            <li><a href="{{ route('settings.index') }}"><i
                        class="fas fa-cog me-2"></i>{{ __('messages.settings') }}</a></li>
            <li><a href="{{ route('groups.index') }}"><i
                        class="fas fa-layer-group me-2"></i>{{ __('messages.groups') }}</a></li>
            <li><a href="{{ route('rewards.index') }}"><i
                        class="fas fa-gift me-2"></i>{{ __('messages.rewards') }}</a></li>
            <li><a href="{{ route('orders.index') }}"><i
                        class="fas fa-shopping-cart me-2"></i>{{ __('messages.orders') }}</a></li>
            <li>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="w-100 text-start btn btn-link text-white px-3">
                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('messages.logout') }}
                    </button>
                </form>
            </li>
             @else
                        <a href="{{ route('loginPage') }}"
                            class="nav-link text-white text-begin">{{ __('messages.login') }}</a>
                    @endauth
        </ul>

        <button class="world-icon-btn" id="languageSwitcher"><i class="fas fa-globe"></i></button>
    </div>

    <!-- Sidebar Toggle Button -->
    <button class="btn-toggle-sidebar d-md-none" id="toggleSidebar"><i class="fas fa-bars"></i></button>

    <!-- Main Content -->
    <div class="content-area">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("toggleSidebar").addEventListener("click", function() {
            document.getElementById("sidebar").classList.toggle("show");
        });

        // Language Switcher
        document.getElementById('languageSwitcher').addEventListener('click', function() {
            const currentLang = "{{ app()->getLocale() }}";
            const newLang = currentLang === 'en' ? 'ar' : 'en';
            window.location.href = `/lang/${newLang}`;
        });
    </script>
</body>

</html>
