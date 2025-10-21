<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
@php
    $faviconUrl = Cache::remember('app_logo_url', 3600, function () {
        $logo = App\Models\Setting::where('name', 'logo')->first();
        return $logo?->getFirstMediaUrl('app_logo');
    });
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    <link rel="icon" href="{{ $faviconUrl }}" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #1565C0;
            --sidebar-hover: #1E88E5;
            --sidebar-text: #E3F2FD;
            --sidebar-width: 220px;
            --mobile-header-height: 60px;
        }

        body {
            margin: 0;
            font-family: system-ui, sans-serif;
            overflow-x: hidden;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            overflow-y: auto;
            z-index: 1040;
            transition: transform 0.3s ease;
        }

        [dir="rtl"] .sidebar {
            left: auto;
            right: 0;
        }

        .sidebar .brand {
            text-align: center;
            padding: 20px 0;
        }

        .sidebar .brand img {
            width: 120px;
            height: auto;
            border-radius: 8px;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar nav a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background-color: var(--sidebar-hover);
        }

        .sidebar nav i {
            width: 20px;
            margin-inline-end: 10px;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--mobile-header-height);
            background: var(--sidebar-bg);
            color: white;
            padding: 0 15px;
            align-items: center;
            justify-content: space-between;
            z-index: 1050;
        }

        [dir="rtl"] .mobile-header {
            right: 0;
            left: auto;
        }

        .btn-menu {
            background: transparent;
            border: none;
            color: white;
            font-size: 1.3rem;
        }

        /* Content */
        .content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin 0.3s ease;
            background-color: #fff;
        }

        [dir="rtl"] .content-wrapper {
            margin-left: 0;
            margin-right: var(--sidebar-width);
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            [dir="rtl"] .sidebar {
                transform: translateX(100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .mobile-header {
                display: flex;
            }

            .content-wrapper {
                margin: 0;
                padding-top: calc(var(--mobile-header-height) + 10px);
            }
        }
    </style>
</head>

<body>
    <!-- Mobile Header -->
    <header class="mobile-header">
        <button class="btn-menu" id="toggleSidebar"><i class="fas fa-bars"></i></button>
        <button class="btn-menu" id="backButton"><i class="fas fa-arrow-left"></i></button>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <img src="{{ $faviconUrl }}" alt="App Logo">
        </div>
        <nav>
            <ul>
                @auth
                    <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>{{ __('messages.users') }}</a></li>

                    <li><a href="{{ route('competitions.index') }}"
                            class="{{ request()->routeIs('competitions.*') ? 'active' : '' }}">
                            <i class="fas fa-trophy"></i>{{ __('messages.competitions') }}</a></li>

                    <li><a href="{{ route('quizzes.index') }}"
                            class="{{ request()->routeIs('quizzes.*') ? 'active' : '' }}">
                            <i class="fas fa-question-circle"></i>{{ __('messages.quizzes') }}</a></li>

                    <li><a href="{{ route('questions.index') }}"
                            class="{{ request()->routeIs('questions.*') ? 'active' : '' }}">
                            <i class="fas fa-edit"></i>{{ __('messages.questions') }}</a></li>

                    <li><a href="{{ route('settings.index') }}"
                            class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>{{ __('messages.settings') }}</a></li>

                    <li><a href="{{ route('groups.index') }}"
                            class="{{ request()->routeIs('groups.*') ? 'active' : '' }}">
                            <i class="fas fa-layer-group"></i>{{ __('messages.groups') }}</a></li>

                    <li><a href="{{ route('bonus-penalties.index') }}"
                            class="{{ request()->routeIs('bonus-penalties.*') ? 'active' : '' }}">
                            <i class="fas fa-layer-group"></i>{{ __('messages.bonus-penalties') }}</a></li>

                    <li><a href="{{ route('rewards.index') }}"
                            class="{{ request()->routeIs('rewards.*') ? 'active' : '' }}">
                            <i class="fas fa-gift"></i>{{ __('messages.rewards') }}</a></li>

                    <li><a href="{{ route('orders.index') }}"
                            class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart"></i>{{ __('messages.orders') }}</a></li>

                    <li class="mt-3 border-top border-light pt-2">
                        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit"
                                class="w-100 d-flex align-items-center border-0 bg-transparent text-start text-white py-2 px-3"
                                style="font-size: 15px;">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                <span>{{ __('trans.logout') }}</span>
                            </button>
                        </form>
                    </li>

                @endauth
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="content-wrapper">
        <div class="container-fluid py-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('toggleSidebar');
            const back = document.getElementById('backButton');

            toggle.addEventListener('click', () => sidebar.classList.toggle('show'));
            back.addEventListener('click', () => window.history.back());

            document.addEventListener('click', e => {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target) && window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
