<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
@php
    $faviconUrl = Cache::remember('app_logo_url', 3600, function () {
        $logo = \App\Models\Setting::where('name', 'logo')->first();
        return $logo?->getFirstMediaUrl('app_logo') ?? asset('default-logo.png');
    });
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    <link rel="icon" href="{{ $faviconUrl }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

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

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            overflow-y: auto;
            z-index: 1040;
            transition: transform .3s ease;
            will-change: transform;
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
            border-radius: 8px;
            loading: lazy;
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
            transition: background-color .2s;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: var(--sidebar-hover);
        }

        .sidebar nav i {
            width: 20px;
            margin-right: 10px;
        }

        [dir="rtl"] .sidebar nav i {
            margin-right: 0;
            margin-left: 10px;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            position: fixed;
            top: 0;
            inset-inline: 0;
            height: var(--mobile-header-height);
            background: var(--sidebar-bg);
            color: white;
            padding: 0 15px;
            align-items: center;
            justify-content: space-between;
            z-index: 1050;
        }

        .btn-menu {
            background: transparent;
            border: none;
            color: inherit;
            font-size: 1.3rem;
        }

        /* Main Content */
        .content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin .3s ease;
            background: #fff;
            will-change: margin;
        }

        [dir="rtl"] .content-wrapper {
            margin-left: 0;
            margin-right: var(--sidebar-width);
        }

        /* Horizontal scrolling for tables on mobile */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            overflow-y: hidden;
        }

        .table-responsive table {
            min-width: 600px;
            white-space: nowrap;
            table-layout: fixed;
        }

        .table-responsive th,
        .table-responsive td {
            white-space: nowrap;
            padding: 0.5rem;
            min-width: 80px;
        }

        .table-responsive th:last-child,
        .table-responsive td:last-child {
            min-width: 120px;
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
                margin-left: 0;
                padding-top: calc(var(--mobile-header-height) + 10px);
            }

            [dir="rtl"] .content-wrapper {
                margin-right: 0;
            }

            /* Enhanced mobile table scrolling */
            .table-responsive {
                margin: 0 -15px;
                padding: 0 15px;
                border-radius: 0;
                position: relative;
            }

            .table-responsive::after {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                width: 20px;
                background: linear-gradient(to left, rgba(255,255,255,0.9), transparent);
                pointer-events: none;
                z-index: 1;
            }

            .table-responsive::-webkit-scrollbar {
                height: 8px;
            }

            .table-responsive::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 4px;
            }

            .table-responsive::-webkit-scrollbar-thumb {
                background: #007bff;
                border-radius: 4px;
                border: 2px solid #f1f1f1;
            }

            .table-responsive::-webkit-scrollbar-thumb:hover {
                background: #0056b3;
            }

            /* Ensure table scrolls properly */
            .table-responsive table {
                margin-bottom: 0;
            }
        }
    </style>


</head>

<body>
    <!-- Mobile Header -->
    <header class="mobile-header">
        <button id="toggleSidebar" class="btn-menu"><i class="fas fa-bars"></i></button>
        <button id="backButton" class="btn-menu"><i class="fas fa-arrow-left"></i></button>
    </header>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <div class="brand">
            <img src="{{ $faviconUrl }}" alt="App Logo">
        </div>

        @auth
            <nav>
                <ul>
                    <li><a href="{{ route('users.index') }}" class="{{ $activeRoutes['users'] ? 'active' : '' }}">
                            <i class="fas fa-users"></i>{{ __('messages.users') }}</a></li>

                    <li><a href="{{ route('users.leaderboard') }}"
                            class="{{ $activeRoutes['leaderboard'] ? 'active' : '' }}">
                            <i class="fas fa-trophy"></i>{{ __('messages.leaderboard') }}</a></li>

                    <li><a href="{{ route('competitions.index') }}"
                            class="{{ $activeRoutes['competitions'] ? 'active' : '' }}">
                            <i class="fas fa-flag"></i>{{ __('messages.competitions') }}</a></li>

                    <li><a href="{{ route('quizzes.index') }}"
                            class="{{ $activeRoutes['quizzes'] ? 'active' : '' }}">
                            <i class="fas fa-question-circle"></i>{{ __('messages.quizzes') }}</a></li>

                    <li><a href="{{ route('questions.index') }}"
                            class="{{ $activeRoutes['questions'] ? 'active' : '' }}">
                            <i class="fas fa-edit"></i>{{ __('messages.questions') }}</a></li>

                    <li><a href="{{ route('settings.index') }}"
                            class="{{ $activeRoutes['settings'] ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>{{ __('messages.settings') }}</a></li>

                    <li><a href="{{ route('groups.index') }}"
                            class="{{ $activeRoutes['groups'] ? 'active' : '' }}">
                            <i class="fas fa-layer-group"></i>{{ __('messages.groups') }}</a></li>

                    <li><a href="{{ route('bonus-penalties.index') }}"
                            class="{{ $activeRoutes['bonus-penalties'] ? 'active' : '' }}">
                            <i class="fas fa-balance-scale"></i>{{ __('messages.bonus-penalties') }}</a></li>

                    <li><a href="{{ route('rewards.index') }}"
                            class="{{ $activeRoutes['rewards'] ? 'active' : '' }}">
                            <i class="fas fa-gift"></i>{{ __('messages.rewards') }}</a></li>

                    <li><a href="{{ route('orders.index') }}"
                            class="{{ $activeRoutes['orders'] ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart"></i>{{ __('messages.orders') }}</a></li>

                    <li><a href="{{ route('notifications.index') }}"
                            class="{{ $activeRoutes['notifications'] ? 'active' : '' }}">
                            <i class="fas fa-bell"></i>{{ __('messages.notifications') }}</a></li>

                    <li><a href="{{ route('about_us.show') }}"
                            class="{{ $activeRoutes['about_us'] ? 'active' : '' }}">
                            <i class="fas fa-info-circle"></i>{{ __('messages.about_us') }}</a></li>

                    <li><a href="{{ route('terms.show') }}"
                            class="{{ $activeRoutes['terms'] ? 'active' : '' }}">
                            <i class="fas fa-file-contract"></i>{{ __('messages.terms') }}</a></li>

                    <li class="mt-3 border-top border-light pt-2">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-100 d-flex align-items-center border-0 bg-transparent text-white py-2 px-3">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                {{ __('messages.logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        @endauth
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteModalLabel">
                        <i class="fa fa-exclamation-triangle me-2"></i> {{ __('messages.confirm_delete') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteModalMessage">{{ __('messages.confirm_delete_message') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{{ __('messages.delete') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Backdrop -->
    <div class="modal-backdrop fade" id="deleteModalBackdrop" style="z-index: 1060; display: none;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('toggleSidebar');
            const back = document.getElementById('backButton');

            toggle?.addEventListener('click', () => sidebar.classList.toggle('show'));
            back?.addEventListener('click', () => window.history.back());

            document.addEventListener('click', e => {
                if (window.innerWidth < 992 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            });

            // Delete confirmation modal
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'), {
                backdrop: 'static',
                keyboard: false
            });
            const deleteModalBackdrop = document.getElementById('deleteModalBackdrop');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            let deleteForm = null;

            document.addEventListener('click', function(e) {
                if (e.target.closest('[data-bs-toggle="delete-modal"]')) {
                    e.preventDefault();
                    const btn = e.target.closest('[data-bs-toggle="delete-modal"]');
                    const message = btn.getAttribute('data-message') || '{{ __('messages.confirm_delete_message') }}';
                    document.getElementById('deleteModalMessage').textContent = message;
                    deleteForm = btn.closest('form');
                    deleteModalBackdrop.style.display = 'block';
                    deleteModal.show();
                }
            });

            confirmDeleteBtn.addEventListener('click', function() {
                if (deleteForm) {
                    deleteForm.submit();
                }
                deleteModal.hide();
                deleteModalBackdrop.style.display = 'none';
            });

            document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function() {
                deleteModalBackdrop.style.display = 'none';
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
