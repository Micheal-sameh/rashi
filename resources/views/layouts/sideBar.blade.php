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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        :root {
            --sidebar-bg: linear-gradient(180deg, #1a237e 0%, #283593 100%);
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --sidebar-text: #E3F2FD;
            --sidebar-width: 260px;
            --mobile-header-height: 60px;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --danger-gradient: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
            --card-shadow: 0 4px 20px rgba(0,0,0,0.08);
            --hover-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: #667eea #f1f5f9;
        }

        *::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        *::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        *::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #667eea, #764ba2);
            border-radius: 10px;
        }

        *::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #764ba2, #667eea);
        }

        body {
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            overflow-x: hidden;
            background: #f8fafc;
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
            transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        }

        [dir="rtl"] .sidebar {
            left: auto;
            right: 0;
            box-shadow: -4px 0 20px rgba(0,0,0,0.1);
        }

        .sidebar .brand {
            text-align: center;
            padding: 30px 0 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar .brand img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.2);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }

        .sidebar .brand img:hover {
            transform: scale(1.05);
        }

        .sidebar nav ul {
            list-style: none;
            padding: 10px 0;
            margin: 0;
        }

        .sidebar nav a,
        .sidebar nav button {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all .3s ease;
            border-radius: 0;
            position: relative;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .sidebar nav a::before,
        .sidebar nav button::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #fff;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        [dir="rtl"] .sidebar nav a::before,
        [dir="rtl"] .sidebar nav button::before {
            left: auto;
            right: 0;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active,
        .sidebar nav button:hover {
            background: var(--sidebar-hover);
            padding-left: 28px;
        }

        [dir="rtl"] .sidebar nav a:hover,
        [dir="rtl"] .sidebar nav a.active {
            padding-left: 24px;
            padding-right: 28px;
        }

        .sidebar nav a:hover::before,
        .sidebar nav a.active::before {
            transform: scaleY(1);
        }

        .sidebar nav i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
            text-align: center;
        }

        [dir="rtl"] .sidebar nav i {
            margin-right: 0;
            margin-left: 12px;
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .btn-menu {
            background: transparent;
            border: none;
            color: inherit;
            font-size: 1.4rem;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .btn-menu:hover {
            background: rgba(255,255,255,0.1);
        }

        /* Main Content */
        .content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin .3s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
            will-change: margin;
        }

        [dir="rtl"] .content-wrapper {
            margin-left: 0;
            margin-right: var(--sidebar-width);
        }

        /* Enhanced Card Styles */
        .card {
            border: none;
            border-radius: 16px !important;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-2px);
        }

        .card-header {
            background: var(--primary-gradient);
            border: none;
            padding: 1.25rem 1.5rem;
        }

        /* Enhanced Table Styles */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 16px;
            background: white;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .table thead th {
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: #475569;
            padding: 1rem 1.25rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .table tbody tr:hover {
            background: linear-gradient(90deg, #f8fafc 0%, #f1f5f9 100%);
            transform: scale(1.001);
        }

        .table tbody td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
        }

        /* Enhanced Button Styles */
        .btn {
            border-radius: 10px;
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .btn-danger {
            background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .btn-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        /* Enhanced Form Controls */
        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        /* Enhanced Alert Styles */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
            box-shadow: var(--card-shadow);
        }

        .alert-success {
            background: linear-gradient(135deg, #d4f4dd 0%, #c3f0d3 100%);
            color: #047857;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fde8e8 0%, #fdd8d8 100%);
            color: #dc2626;
        }

        /* Enhanced Badge Styles */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }

        /* Zoomable Images */
        .zoomable-image {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .zoomable-image:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
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
                box-shadow: 8px 0 30px rgba(0,0,0,0.3);
            }

            [dir="rtl"] .sidebar.show {
                box-shadow: -8px 0 30px rgba(0,0,0,0.3);
            }

            .mobile-header {
                display: flex;
            }

            .content-wrapper {
                margin-left: 0;
                padding-top: calc(var(--mobile-header-height) + 15px);
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
                background: linear-gradient(90deg, #667eea, #764ba2);
                border-radius: 4px;
            }

            .table-responsive::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(90deg, #764ba2, #667eea);
            }

            /* Mobile card enhancements */
            .card {
                margin-bottom: 1rem;
            }

            .btn {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }
        }

        /* Loading Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .content-wrapper > * {
            animation: fadeIn 0.5s ease-out;
        }

        /* Custom Utility Classes */
        .rounded-4 {
            border-radius: 16px !important;
        }

        .shadow-soft {
            box-shadow: var(--card-shadow) !important;
        }

        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: var(--hover-shadow);
        }

        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
        <div class="container-fluid px-3 px-md-4 py-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-soft">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fs-4"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-soft">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-3 fs-4"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-soft">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle me-3 fs-4 mt-1"></i>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
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
