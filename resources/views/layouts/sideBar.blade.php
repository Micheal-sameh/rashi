<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/jpg">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Sidebar and other styles here... */
        html,
        body {
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        /* Sidebar Styles */
        #sidebar {
            position: fixed;
            top: 0;
            height: 100vh;
            width: 200px;
            background-color: #333;
            color: white;
            transition: left 0.3s ease, right 0.3s ease;
            z-index: 1000;
            left: 0;
        }

        /* RTL adjustments for sidebar */
        [dir="rtl"] #sidebar {
            left: auto;
            right: 0px;
        }

        #sidebar.show {
            left: 0;
        }

        [dir="rtl"] #sidebar.show {
            right: 0;
        }

        /* Content Area */
        .content-wrapper {
            display: flex;
            width: 90%;
        }

        .content-area {
            flex-grow: 1;
            margin-left: 200px;
        }

        /* RTL content margin */
        [dir="rtl"] .content-area {
            margin-right: 200px;
        }

        /* Sidebar Toggle Button */
        .btn-toggle-sidebar {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }

        /* RTL adjustments for toggle button */
        [dir="rtl"] .btn-toggle-sidebar {
            right: 20px;
            left: auto;
        }

        /* Mobile View - Sidebar hidden off-screen by default */
        @media (max-width: 767px) {
            #sidebar {
                left: -200px;
            }

            [dir="rtl"] #sidebar {
                right: -200px;
            }

            #sidebar.show {
                left: 0;
            }

            [dir="rtl"] #sidebar.show {
                right: 0;
            }

            .content-area {
                flex-grow: 1;
                margin-left: 0px;
            }

            [dir="rtl"] .content-area {
                margin-right: 0;
            }

            .btn-toggle-sidebar {
                display: block;
            }

            /* Back Arrow Button */
            .btn-back {
                position: fixed;
                top: 20px;
                left: 60px;
                z-index: 1002;
                background: none;
                border: none;
                padding: 0;
                cursor: pointer;
            }

            [dir="rtl"] .btn-back {
                left: 10;
            }
        }

        /* Sidebar Menu Items */
        #sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #sidebar .nav-item {
            padding: 8px;
        }

        /* World Icon Button */
        #sidebar .world-icon-btn {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: none;
            border: none;
            padding: 10px;
            cursor: pointer;
            color: white;
            font-size: 24px;
            z-index: 1001;
        }

        #sidebar .world-icon-btn:hover {
            color: #f8f9fa;
        }

        [dir="rtl"] #sidebar .world-icon-btn {
            left: auto;
            right: 50%;
            transform: translateX(50%);
        }

        #sidebar img {
            width: 150px;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row content-wrapper">
            <!-- Sidebar -->
            <div id="sidebar">
                @php
                    $logo = App\Models\Setting::where('name', 'logo')->first();
                @endphp
                <img src="{{ $logo->getFirstMediaUrl('app_logo') }}" alt="Logo" class="img-fluid mb-3">
                <ul class="nav flex-column">
                    @auth
                        <li class="nav-item text-begin"><a class="nav-item text-white" style="text-decoration: none;"
                                href="{{ url('/') }}"> {{ __('messages.home') }} </a></li>
                        {{-- <li class="nav-item text-begin"><a class="nav-item text-white" style="text-decoration: none;" href="{{ route('users.profile') }}">{{__('messages.profile')}} </a></li> --}}
                        {{-- @can('users_list') --}}
                        <li class="nav-item text-begin"><a class="nav-item text-white" style="text-decoration: none;" href="{{ route('users.index') }}"> {{__('messages.users')}}</a></li>
                        {{-- @endcan --}}
                        {{-- @can('competitions_list') --}}
                        <li class="nav-item text-begin"><a class="nav-item text-white" style="text-decoration: none;"
                                href="{{ route('competitions.index') }}">{{ __('messages.competitions') }}</a></li>
                        {{-- @endcan --}}
                        {{-- @can('reservations_list') --}}
                        <li class="nav-item text-begin"><a class="nav-item text-white" style="text-decoration: none;"
                                href="{{ route('quizzes.index') }}">{{ __('messages.quizzes') }}</a></li>
                        {{-- @endcan --}}
                        {{-- @can('workDays_list') --}}
                        <li class="nav-item text-begin"><a class="nav-item text-white" style="text-decoration: none;"
                                href="{{ route('questions.index') }}">{{ __('messages.questions') }}</a></li>
                        {{-- @endcan --}}
                        {{-- @can('reports_list') --}}
                        <li class="nav-item text-begin"><a class="nav-item text-white" style="text-decoration: none;"
                                href="{{ route('settings.index') }}">{{ __('messages.settings') }}</a></li>
                        {{-- @endcan --}}
                        {{-- @can('reports_list') --}}
                        <li class="nav-item text-begin"><a class="nav-item text-white" style="text-decoration: none;"
                                href="{{ route('groups.index') }}">{{ __('messages.groups') }}</a></li>
                        {{-- @endcan --}}
                    @endauth
                    @auth
                        <li class="nav-item text-begin">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-item text-white"
                                    style="background: none; border: none; padding: 0; cursor: pointer;">
                                    {{ __('messages.logout') }}
                                </button>
                            </form>
                        </li>
                    @else
                        <a href="{{ route('loginPage') }}"
                            class="nav-link text-white text-begin">{{ __('messages.login') }}</a>
                    @endauth

                </ul>

                <!-- World Icon Button for language selection -->
                <button class="world-icon-btn" id="languageSwitcher" aria-label="Change Language">
                    <i class="fas fa-globe"></i>
                </button>
            </div>

            <!-- Content Area -->
            <div class="content-area p-3">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Mobile Toggle Button -->
    <button class="btn-toggle-sidebar d-md-none" id="toggleSidebar" aria-label="Open Sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Back Arrow Button for Mobile -->
    <button class="btn-back d-md-none" id="backArrow" aria-label="Go Back">
        <i class="fas fa-arrow-left"></i>
    </button>

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle JS -->
    <script>
        document.getElementById("toggleSidebar").addEventListener("click", function() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("show");
            this.setAttribute('aria-label', sidebar.classList.contains("show") ? 'Close Sidebar' : 'Open Sidebar');
        });

        // Language Switcher Logic
        document.getElementById('languageSwitcher').addEventListener('click', function() {
            const currentLang = "{{ app()->getLocale() }}";
            const newLang = currentLang === 'en' ? 'ar' : 'en';
            window.location.href = `/lang/${newLang}`;
        });

        // Back Arrow functionality
        document.getElementById("backArrow").addEventListener("click", function() {
            window.history.back();
        });
    </script>

    <!-- Additional Scripts -->
    @stack('scripts')
</body>

</html>
