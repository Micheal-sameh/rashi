<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
@php
    $faviconUrl = Cache::remember('app_logo_url', 3600, function () {
        $logo = \App\Models\Setting::where('name', 'logo')->first();
        return $logo?->getFirstMediaUrl('app_logo') ?? asset('default-logo.png');
    });
    // Ensure absolute URL for social media sharing
    $absoluteLogoUrl = filter_var($faviconUrl, FILTER_VALIDATE_URL) ? $faviconUrl : url($faviconUrl);
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ $faviconUrl }}" type="image/png">

    <!-- Open Graph Meta Tags for Social Media Sharing -->
    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:description" content="@yield('description', config('app.name') . ' - ' . __('messages.app_tagline'))">
    <meta property="og:image" content="{{ $absoluteLogoUrl }}">
    <meta property="og:image:secure_url" content="{{ $absoluteLogoUrl }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', config('app.name'))">
    <meta name="twitter:description" content="@yield('description', config('app.name') . ' - ' . __('messages.app_tagline'))">
    <meta name="twitter:image" content="{{ $absoluteLogoUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        :root {
            /* Override Bootstrap's own primary so .text-primary/.bg-primary/.border-primary/
               .btn-outline-primary/.form-check-input:checked etc. all follow the brand color too */
            --bs-primary: #3525cd;
            --bs-primary-rgb: 53, 37, 205;
            --bs-link-color: #3525cd;
            --bs-link-color-rgb: 53, 37, 205;
            --bs-link-hover-color: #2a1ea3;
            --bs-link-hover-color-rgb: 42, 30, 163;

            /* Rashi Management System design tokens (Electric Indigo / Slate) */
            --color-primary: #3525cd;
            --color-primary-container: #4f46e5;
            --color-on-primary: #ffffff;
            --color-on-primary-container: #dad7ff;
            --color-secondary: #505f76;
            --color-secondary-container: #d0e1fb;
            --color-tertiary: #41485e;
            --color-tertiary-container: #586076;
            --color-error: #ba1a1a;
            --color-on-error: #ffffff;
            --color-error-container: #ffdad6;
            --color-on-error-container: #93000a;
            --color-success: #11998e;
            --color-success-container: #d4f4dd;
            --color-on-success-container: #047857;
            --color-warning: #b45309;
            --color-warning-container: #fef3c7;

            --color-background: #f7f9fb;
            --color-surface: #f7f9fb;
            --color-surface-container-lowest: #ffffff;
            --color-surface-container-low: #f2f4f6;
            --color-surface-container: #eceef0;
            --color-surface-container-high: #e6e8ea;
            --color-on-surface: #191c1e;
            --color-on-surface-variant: #464555;
            --color-outline: #777587;
            --color-outline-variant: #c7c4d8;
            --color-border: #e2e8f0;

            --sidebar-bg: var(--color-surface-container-lowest);
            --sidebar-hover: rgba(53, 37, 205, 0.06);
            --sidebar-text: var(--color-on-surface-variant);
            --sidebar-width: 260px;
            --mobile-header-height: 60px;

            --radius-card: 16px;
            --radius-btn: 10px;
            --radius-input: 8px;
            --radius-badge: 9999px;

            --shadow-1: 0px 4px 6px -1px rgba(15, 23, 42, 0.05), 0px 2px 4px -2px rgba(15, 23, 42, 0.05);
            --shadow-2: 0px 10px 15px -3px rgba(15, 23, 42, 0.1);
            --card-shadow: var(--shadow-1);
            --hover-shadow: var(--shadow-2);
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: var(--color-primary) var(--color-surface-container-low);
        }

        *::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        *::-webkit-scrollbar-track {
            background: var(--color-surface-container-low);
        }

        *::-webkit-scrollbar-thumb {
            background: var(--color-primary);
            border-radius: 10px;
        }

        *::-webkit-scrollbar-thumb:hover {
            background: var(--color-primary-container);
        }

        body {
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            overflow-x: hidden;
            background: var(--color-background);
            color: var(--color-on-surface);
        }

        [dir="rtl"] body {
            line-height: 1.75;
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
            overflow-x: hidden;
            scrollbar-width: none;
            -ms-overflow-style: none;
            z-index: 1040;
            transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1), width .25s ease, box-shadow .25s ease;
            will-change: transform, width;
            border-inline-end: 1px solid var(--color-outline-variant);
            box-shadow: var(--shadow-1);
            display: flex;
            flex-direction: column;
        }

        .sidebar::-webkit-scrollbar {
            display: none;
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .sidebar nav > ul:first-child {
            flex: 1;
        }

        .sidebar .sidebar-footer {
            padding: 10px 12px 16px;
            border-top: 1px solid var(--color-outline-variant);
            margin-top: auto;
        }

        .sidebar .sidebar-footer button.text-danger:hover {
            background: var(--color-error-container) !important;
            color: var(--color-on-error-container) !important;
        }

        [dir="rtl"] .sidebar {
            left: auto;
            right: 0;
        }

        .sidebar .brand {
            padding: 24px 20px 16px;
            border-bottom: 1px solid var(--color-outline-variant);
        }

        .sidebar .brand-inner {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar .brand img,
        .brand-logo-fallback {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: var(--shadow-1);
        }

        .brand-logo-fallback {
            flex-shrink: 0;
            background: var(--color-primary);
            color: var(--color-on-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .sidebar .brand-title {
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--color-primary);
            margin: 0;
            line-height: 1.2;
        }

        .sidebar .brand-subtitle {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--color-on-surface-variant);
            margin: 0;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 10px 12px;
            margin: 0;
        }

        .sidebar nav a,
        .sidebar nav button {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            margin: 2px 0;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all .2s ease;
            border-radius: var(--radius-input);
            position: relative;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .sidebar nav a:hover,
        .sidebar nav button:hover {
            background: var(--color-surface-container-high);
        }

        .sidebar nav a.active {
            background: var(--color-primary);
            color: var(--color-on-primary);
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(53, 37, 205, 0.25);
        }

        .sidebar nav a.active:hover {
            background: var(--color-primary);
        }

        .sidebar nav i {
            width: 20px;
            font-size: 1rem;
            text-align: center;
            color: inherit;
        }

        [dir="rtl"] .sidebar nav i {
            margin: 0;
        }

        /* Menu Section Titles */
        .menu-section-title {
            padding: 18px 12px 6px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--color-outline);
            letter-spacing: 0.05em;
            margin-top: 6px;
        }

        .menu-section-title:first-of-type {
            margin-top: 0;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            position: fixed;
            top: 0;
            inset-inline: 0;
            height: var(--mobile-header-height);
            background: var(--color-surface-container-lowest);
            color: var(--color-on-surface);
            padding: 0 15px;
            align-items: center;
            justify-content: space-between;
            z-index: 1050;
            border-bottom: 1px solid var(--color-outline-variant);
            box-shadow: var(--shadow-1);
        }

        .btn-menu {
            background: transparent;
            border: none;
            color: inherit;
            font-size: 1.4rem;
            padding: 8px 12px;
            border-radius: var(--radius-input);
            transition: background 0.2s ease;
        }

        .btn-menu:hover {
            background: var(--color-surface-container-high);
        }

        /* Top Bar */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            height: 64px;
            padding: 0 24px;
            background: rgba(247, 249, 251, 0.85);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--color-outline-variant);
        }

        .topbar-icon-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: var(--color-on-surface-variant);
            background: transparent;
            transition: background 0.2s ease, color 0.2s ease;
            font-size: 1rem;
            position: relative;
        }

        .topbar-icon-btn:hover {
            background: var(--color-surface-container-high);
            color: var(--color-primary);
        }

        .lang-switch-btn {
            width: auto;
            min-width: 40px;
            border-radius: var(--radius-btn);
            padding: 0 10px;
        }

        .lang-switch-btn .lang-code {
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.03em;
        }

        .topbar .user-menu-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            border: none;
            background: transparent;
            padding: 6px 10px 6px 6px;
            border-radius: 999px;
            transition: background 0.2s ease;
        }

        [dir="rtl"] .topbar .user-menu-toggle {
            padding: 6px 6px 6px 10px;
        }

        .topbar .user-menu-toggle:hover {
            background: var(--color-surface-container-high);
        }

        .topbar .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            background: var(--color-primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .topbar .user-name {
            font-weight: 600;
            font-size: 0.88rem;
            color: var(--color-on-surface);
            line-height: 1.2;
        }

        .topbar .user-role {
            font-size: 0.72rem;
            color: var(--color-on-surface-variant);
        }

        .topbar .user-menu-toggle .fa-chevron-down {
            font-size: 0.7rem;
            color: var(--color-on-surface-variant);
        }

        .topbar .dropdown-menu {
            border: none;
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-2);
            padding: 8px;
            min-width: 220px;
        }

        .topbar .dropdown-menu .dropdown-header {
            font-weight: 700;
            color: var(--color-on-surface);
            font-size: 0.85rem;
            padding: 6px 10px 2px;
        }

        .topbar .dropdown-menu .dropdown-item-text {
            color: var(--color-on-surface-variant);
            font-size: 0.75rem;
            padding: 0 10px 8px;
        }

        .topbar .dropdown-menu .dropdown-item {
            border-radius: var(--radius-input);
            padding: 8px 10px;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar .dropdown-menu .dropdown-item:hover {
            background: var(--color-surface-container-high);
        }

        .topbar .dropdown-menu .dropdown-item.text-danger:hover {
            background: var(--color-error-container);
            color: var(--color-on-error-container) !important;
        }

        .notifications-menu {
            min-width: 320px;
            max-width: 360px;
            padding: 8px;
        }

        .notification-item {
            padding: 10px 10px;
            border-radius: var(--radius-input);
            transition: background 0.2s ease;
        }

        .notification-item:hover {
            background: var(--color-surface-container-high);
        }

        .notification-title {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--color-on-surface);
        }

        .notification-message {
            font-size: 0.78rem;
            color: var(--color-on-surface-variant);
            margin-top: 2px;
            white-space: normal;
        }

        .notification-time {
            font-size: 0.7rem;
            color: var(--color-outline);
            margin-top: 4px;
        }

        @media (max-width: 991px) {
            .topbar {
                top: var(--mobile-header-height);
            }
        }

        /* Main Content */
        .content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin .3s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--color-background);
            will-change: margin;
        }

        [dir="rtl"] .content-wrapper {
            margin-left: 0;
            margin-right: var(--sidebar-width);
        }

        /* Collapsed sidebar that expands on hover (desktop only) */
        @media (min-width: 992px) {
            :root {
                --sidebar-collapsed-width: 80px;
            }

            .sidebar {
                width: var(--sidebar-collapsed-width);
            }

            .sidebar:hover {
                width: var(--sidebar-width);
                box-shadow: var(--shadow-2);
            }

            .content-wrapper {
                margin-left: var(--sidebar-collapsed-width);
            }

            [dir="rtl"] .content-wrapper {
                margin-left: 0;
                margin-right: var(--sidebar-collapsed-width);
            }

            .sidebar .brand {
                padding: 20px 8px;
                display: flex;
                justify-content: center;
                transition: padding .25s ease;
            }

            .sidebar:hover .brand {
                padding: 24px 20px 16px;
                justify-content: flex-start;
            }

            .sidebar .brand-inner {
                overflow: hidden;
            }

            .sidebar .brand img,
            .brand-logo-fallback {
                width: 36px;
                height: 36px;
                flex-shrink: 0;
                transition: width .25s ease, height .25s ease;
            }

            .sidebar:hover .brand img,
            .sidebar:hover .brand-logo-fallback {
                width: 44px;
                height: 44px;
            }

            .sidebar nav a,
            .sidebar nav button,
            .menu-section-title {
                overflow: hidden;
                white-space: nowrap;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: justify-content .2s ease, padding .2s ease;
            }

            .sidebar:hover nav a,
            .sidebar:hover nav button,
            .sidebar:hover .menu-section-title {
                justify-content: flex-start;
            }

            .sidebar .nav-label,
            .sidebar .brand-title,
            .sidebar .brand-subtitle {
                display: inline-block;
                opacity: 0;
                max-width: 0;
                overflow: hidden;
                white-space: nowrap;
                transition: opacity .15s ease, max-width .2s ease;
                vertical-align: middle;
            }

            .sidebar:hover .nav-label,
            .sidebar:hover .brand-title,
            .sidebar:hover .brand-subtitle {
                opacity: 1;
                max-width: 200px;
            }

            .sidebar .brand-title,
            .sidebar .brand-subtitle {
                display: block;
                max-width: 0;
            }

            .sidebar:hover .brand-title,
            .sidebar:hover .brand-subtitle {
                max-width: 180px;
            }
        }

        /* Enhanced Card Styles */
        .card {
            border: none;
            border-radius: var(--radius-card) !important;
            box-shadow: var(--card-shadow);
            transition: box-shadow 0.2s ease;
            overflow: hidden;
            background: var(--color-surface-container-lowest);
        }

        .card:hover {
            box-shadow: var(--hover-shadow);
        }

        .card-header {
            background: var(--color-surface-container-lowest);
            border-bottom: 1px solid var(--color-outline-variant);
            padding: 1.25rem 1.5rem;
            color: var(--color-on-surface);
            font-weight: 700;
        }

        /* Enhanced Table Styles */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: var(--radius-card);
            background: var(--color-surface-container-lowest);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: var(--color-surface-container-low);
        }

        .table thead th {
            border: none;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: var(--color-on-surface-variant);
            padding: 1rem 1.25rem;
        }

        .table tbody tr {
            transition: background 0.2s ease;
            border-bottom: 1px solid var(--color-outline-variant);
        }

        .table tbody tr:hover {
            background: var(--color-surface-container-low);
        }

        .table tbody td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
        }

        /* Enhanced Button Styles */
        .btn {
            border-radius: var(--radius-btn);
            padding: 0.625rem 1.25rem;
            font-weight: 600;
            transition: all 0.2s ease;
            border: none;
        }

        .btn:hover {
            filter: brightness(0.96);
        }

        .btn:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }

        .btn-primary {
            background: var(--color-primary);
            color: var(--color-on-primary);
        }

        .btn-secondary {
            background: var(--color-surface-container-high);
            color: var(--color-on-surface);
        }

        .btn-success {
            background: var(--color-success);
            color: #fff;
        }

        .btn-danger {
            background: var(--color-error);
            color: var(--color-on-error);
        }

        .btn-warning {
            background: var(--color-warning-container);
            color: var(--color-warning);
        }

        .btn-info {
            background: var(--color-secondary-container);
            color: var(--color-secondary);
        }

        /* Enhanced Form Controls */
        .form-control,
        .form-select {
            border-radius: var(--radius-input);
            border: 1px solid var(--color-border);
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            background: var(--color-surface-container-lowest);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(53, 37, 205, 0.12);
        }

        label,
        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--color-on-surface-variant);
        }

        /* Enhanced Alert Styles */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
            box-shadow: var(--card-shadow);
        }

        .alert-success {
            background: var(--color-success-container);
            color: var(--color-on-success-container);
        }

        .alert-danger {
            background: var(--color-error-container);
            color: var(--color-on-error-container);
        }

        /* Enhanced Badge Styles */
        .badge {
            padding: 0.4rem 0.85rem;
            border-radius: var(--radius-badge);
            font-weight: 600;
            font-size: 0.75rem;
        }

        /* Zoomable Images */
        .zoomable-image {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            border-radius: 12px;
            box-shadow: var(--shadow-1);
        }

        .zoomable-image:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-2);
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
                box-shadow: var(--shadow-2);
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
                background: var(--color-surface-container-low);
                border-radius: 4px;
            }

            .table-responsive::-webkit-scrollbar-thumb {
                background: var(--color-primary);
                border-radius: 4px;
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
            animation: fadeIn 0.4s ease-out;
        }

        /* Custom Utility Classes */
        .rounded-4 {
            border-radius: var(--radius-card) !important;
        }

        .shadow-soft {
            box-shadow: var(--card-shadow) !important;
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: var(--hover-shadow);
        }

        .text-gradient {
            color: var(--color-primary);
        }

        /* ---- Type scale (DESIGN.md) ---- */
        .rs-display {
            font-size: 48px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .rs-headline-lg {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.3;
            letter-spacing: -0.01em;
            color: var(--color-on-surface);
        }

        .rs-headline-md {
            font-size: 24px;
            font-weight: 600;
            line-height: 1.4;
            color: var(--color-on-surface);
        }

        .rs-title-lg {
            font-size: 20px;
            font-weight: 600;
            line-height: 1.5;
            color: var(--color-on-surface);
        }

        .rs-body-lg {
            font-size: 16px;
            line-height: 1.6;
            color: var(--color-on-surface-variant);
        }

        .rs-label-md {
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--color-on-surface-variant);
        }

        @media (max-width: 767px) {
            .rs-headline-lg {
                font-size: 28px;
            }
        }

        /* ---- Page Header ---- */
        .rs-page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .rs-page-header .rs-page-header-titles {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .rs-page-header .rs-page-icon {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
            border-radius: var(--radius-input);
            background: rgba(53, 37, 205, 0.08);
            color: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .rs-page-header .rs-page-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ---- KPI / Bento Stat Card ---- */
        .rs-stat-card {
            background: var(--color-surface-container-lowest);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-1);
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 116px;
            position: relative;
            overflow: hidden;
        }

        .rs-stat-card .rs-stat-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .rs-stat-card .rs-stat-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-input);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .rs-stat-card .rs-stat-value {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
            color: var(--color-on-surface);
            margin-top: 8px;
        }

        .rs-stat-card.tone-primary .rs-stat-icon { background: rgba(53, 37, 205, 0.1); color: var(--color-primary); }
        .rs-stat-card.tone-success .rs-stat-icon { background: var(--color-success-container); color: var(--color-on-success-container); }
        .rs-stat-card.tone-error .rs-stat-icon { background: var(--color-error-container); color: var(--color-on-error-container); }
        .rs-stat-card.tone-warning .rs-stat-icon { background: var(--color-warning-container); color: var(--color-warning); }
        .rs-stat-card.tone-secondary .rs-stat-icon { background: var(--color-secondary-container); color: var(--color-secondary); }

        /* ---- Section card body padding per DESIGN.md Bento Cards spec ---- */
        .card-body {
            padding: 24px;
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
            <div class="brand-inner">
                <img src="{{ $faviconUrl }}" alt="App Logo"
                    onerror="this.replaceWith(Object.assign(document.createElement('span'), {className: 'brand-logo-fallback', textContent: '{{ mb_substr(config('app.name', 'Rashi'), 0, 1) }}'}))">
                <div>
                    <p class="brand-title">{{ config('app.name', 'Rashi') }}</p>
                    <p class="brand-subtitle">{{ __('messages.admin_users') }}</p>
                </div>
            </div>
        </div>

        @auth
            <nav>
                <ul>
                    <!-- User Management Section -->
                    <li class="menu-section-title"><i class="fas fa-users-cog me-2"></i><span class="nav-label">{{ __('messages.user_management') }}</span></li>
                    <li><a href="{{ route('users.index') }}" class="{{ $activeRoutes['users'] ? 'active' : '' }}">
                            <i class="fas fa-users"></i><span class="nav-label">{{ __('messages.users') }}</span></a></li>
                    <li><a href="{{ route('users.admins') }}"
                            class="{{ request()->routeIs('users.admins') ? 'active' : '' }}">
                            <i class="fas fa-user-shield"></i><span class="nav-label">{{ __('messages.admin_users') }}</span></a></li>
                    <li><a href="{{ route('families.index') }}"
                            class="{{ request()->routeIs('families.*') ? 'active' : '' }}">
                            <i class="fas fa-house-user"></i><span class="nav-label">{{ __('messages.families') }}</span></a></li>
                    <li><a href="{{ route('user-history.index') }}"
                            class="{{ request()->routeIs('user-history.*') ? 'active' : '' }}">
                            <i class="fas fa-history"></i><span class="nav-label">{{ __('messages.user_history') }}</span></a></li>
                    <li><a href="{{ route('users.leaderboard') }}"
                            class="{{ $activeRoutes['leaderboard'] ? 'active' : '' }}">
                            <i class="fas fa-trophy"></i><span class="nav-label">{{ __('messages.leaderboard') }}</span></a></li>

                    <!-- Competition Section -->
                    <li class="menu-section-title"><i class="fas fa-graduation-cap me-2"></i><span class="nav-label">{{ __('messages.competitions_section') }}</span></li>
                    <li><a href="{{ route('competitions.index') }}"
                            class="{{ $activeRoutes['competitions'] ? 'active' : '' }}">
                            <i class="fas fa-flag"></i><span class="nav-label">{{ __('messages.competitions') }}</span></a></li>
                    <li><a href="{{ route('quizzes.index') }}"
                            class="{{ $activeRoutes['quizzes'] ? 'active' : '' }}">
                            <i class="fas fa-question-circle"></i><span class="nav-label">{{ __('messages.quizzes') }}</span></a></li>
                    <li><a href="{{ route('questions.index') }}"
                            class="{{ $activeRoutes['questions'] ? 'active' : '' }}">
                            <i class="fas fa-edit"></i><span class="nav-label">{{ __('messages.questions') }}</span></a></li>

                    <!-- Points & Rewards Section -->
                    <li class="menu-section-title"><i class="fas fa-coins me-2"></i><span class="nav-label">{{ __('messages.points_rewards') }}</span></li>
                    <li><a href="{{ route('bonus-penalties.index') }}"
                            class="{{ $activeRoutes['bonus-penalties'] ? 'active' : '' }}">
                            <i class="fas fa-balance-scale"></i><span class="nav-label">{{ __('messages.bonus-penalties') }}</span></a></li>
                    @if(Auth::user()->hasRole('admin'))
                        <li><a href="{{ route('bonus-penalties.pending') }}"
                                class="{{ request()->routeIs('bonus-penalties.pending') ? 'active' : '' }}">
                                <i class="fas fa-clock"></i>
                                <span class="nav-label">{{ __('messages.pending_approvals') }}
                                @php
                                    $pendingApprovals = \App\Models\BonusPenalty::where('status', \App\Enums\BonusPenaltyStatus::PENDING_APPROVAL)->count();
                                @endphp
                                @if($pendingApprovals > 0)
                                    <span class="badge bg-danger ms-2">{{ $pendingApprovals }}</span>
                                @endif
                                </span>
                        </a></li>
                    @endif
                    @if(Auth::user()->hasRole('admin'))
                        <li><a href="{{ route('point-transfers.index') }}"
                                class="{{ request()->routeIs('point-transfers.*') ? 'active' : '' }}">
                                <i class="fas fa-exchange-alt"></i><span class="nav-label">{{ __('messages.point_transfers') }}</span></a></li>
                    @endif
                    <li><a href="{{ route('rewards.index') }}"
                            class="{{ $activeRoutes['rewards'] ? 'active' : '' }}">
                            <i class="fas fa-gift"></i><span class="nav-label">{{ __('messages.rewards') }}</span></a></li>
                    <li><a href="{{ route('orders.index') }}"
                            class="{{ $activeRoutes['orders'] ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="nav-label">{{ __('messages.orders') }}
                            @php
                                $pendingOrdersCount = \App\Models\Order::where('status', \App\Enums\OrderStatus::PENDING)->count();
                            @endphp
                            @if($pendingOrdersCount > 0)
                                <span class="badge bg-warning text-dark ms-2">{{ $pendingOrdersCount }}</span>
                            @endif
                            </span>
                    </a></li>

                    <!-- System Section -->
                    <li class="menu-section-title"><i class="fas fa-cogs me-2"></i><span class="nav-label">{{ __('messages.system_settings') }}</span></li>
                    <li><a href="{{ route('settings.index') }}"
                            class="{{ $activeRoutes['settings'] ? 'active' : '' }}">
                            <i class="fas fa-cog"></i><span class="nav-label">{{ __('messages.settings') }}</span></a></li>
                    <li><a href="{{ route('groups.index') }}"
                            class="{{ $activeRoutes['groups'] ? 'active' : '' }}">
                            <i class="fas fa-layer-group"></i><span class="nav-label">{{ __('messages.groups') }}</span></a></li>
                    <li><a href="{{ route('groups.competitions') }}"
                            class="{{ request()->routeIs('groups.competitions*') ? 'active' : '' }}">
                            <i class="fas fa-project-diagram"></i><span class="nav-label">{{ __('messages.groups_competitions') }}</span></a></li>
                    <li><a href="{{ route('notifications.index') }}"
                            class="{{ $activeRoutes['notifications'] ? 'active' : '' }}">
                            <i class="fas fa-bell"></i><span class="nav-label">{{ __('messages.notifications') }}</span></a></li>

                    <!-- Content Management Section -->
                    <li class="menu-section-title"><i class="fas fa-folder me-2"></i><span class="nav-label">{{ __('messages.content_management') }}</span></li>
                    <li><a href="{{ route('about_us.show') }}"
                            class="{{ $activeRoutes['about_us'] ? 'active' : '' }}">
                            <i class="fas fa-info-circle"></i><span class="nav-label">{{ __('messages.about_us') }}</span></a></li>
                    <li><a href="{{ route('terms.show') }}"
                            class="{{ $activeRoutes['terms'] ? 'active' : '' }}">
                            <i class="fas fa-file-contract"></i><span class="nav-label">{{ __('messages.terms') }}</span></a></li>
                    <li><a href="{{ route('social-media.index') }}"
                            class="{{ $activeRoutes['social-media'] ? 'active' : '' }}">
                            <i class="fas fa-share-alt"></i><span class="nav-label">{{ __('messages.social_media') }}</span></a></li>
                    <li><a href="{{ route('info-videos.index') }}"
                            class="{{ $activeRoutes['info-videos'] ? 'active' : '' }}">
                            <i class="fas fa-video"></i><span class="nav-label">{{ __('messages.info_videos') }}</span></a></li>
                </ul>

                <div class="sidebar-footer">
                    <ul class="list-unstyled m-0">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-100 text-danger">
                                    <i class="fas fa-sign-out-alt"></i>
                                    {{ __('messages.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
        @endauth
    </aside>

    <!-- Main Content -->
    <main class="content-wrapper">
        @auth
            @php
                $recentNotifications = \App\Models\Notification::latest()->take(5)->get();
            @endphp
            <div class="topbar">
                <div class="dropdown">
                    <button class="topbar-icon-btn lang-switch-btn" type="button" id="langSwitchToggle" data-bs-toggle="dropdown"
                        aria-expanded="false" title="{{ __('messages.language') }}">
                        <span class="lang-code">{{ strtoupper(app()->getLocale()) }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langSwitchToggle">
                        <li>
                            <a class="dropdown-item {{ app()->isLocale('en') ? 'active' : '' }}" href="{{ route('lang.switch', 'en') }}">
                                <span class="lang-code me-2">EN</span> English
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ app()->isLocale('ar') ? 'active' : '' }}" href="{{ route('lang.switch', 'ar') }}">
                                <span class="lang-code me-2">AR</span> العربية
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="topbar-icon-btn" type="button" id="notificationsToggle" data-bs-toggle="dropdown"
                        aria-expanded="false" title="{{ __('messages.notifications') }}">
                        <i class="fas fa-bell"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end notifications-menu" aria-labelledby="notificationsToggle">
                        <li class="dropdown-header">{{ __('messages.notifications') }}</li>
                        @forelse ($recentNotifications as $notification)
                            <li>
                                <div class="notification-item">
                                    <div class="notification-title">{{ $notification->title }}</div>
                                    <div class="notification-message">{{ Str::limit($notification->message, 80) }}</div>
                                    <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                </div>
                            </li>
                        @empty
                            <li class="dropdown-item-text">{{ __('messages.no_notifications_found') }}</li>
                        @endforelse
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-center justify-content-center" href="{{ route('notifications.index') }}">
                                {{ __('messages.view_all') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="user-menu-toggle" type="button" id="userMenuToggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        @if (auth()->user()->hasMedia('profile_images'))
                            <img src="{{ auth()->user()->getFirstMediaUrl('profile_images') }}"
                                alt="{{ auth()->user()->name }}" class="user-avatar">
                        @else
                            <span class="user-avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                        @endif
                        <span class="d-none d-sm-flex flex-column align-items-start">
                            <span class="user-name">{{ auth()->user()->name }}</span>
                            <span class="user-role">{{ __('messages.admin_users') }}</span>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuToggle">
                        <li class="dropdown-header">{{ auth()->user()->name }}</li>
                        <li class="dropdown-item-text">{{ auth()->user()->email }}</li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('users.show', auth()->id()) }}">
                                <i class="fas fa-user"></i> {{ __('messages.view_profile') }}
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i> {{ __('messages.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        @endauth
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
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
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
                backdrop: true,
                keyboard: true
            });
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            let deleteForm = null;

            document.addEventListener('click', function(e) {
                if (e.target.closest('[data-bs-toggle="delete-modal"]')) {
                    e.preventDefault();
                    const btn = e.target.closest('[data-bs-toggle="delete-modal"]');
                    const message = btn.getAttribute('data-message') || '{{ __('messages.confirm_delete_message') }}';
                    document.getElementById('deleteModalMessage').textContent = message;
                    deleteForm = btn.closest('form');
                    deleteModal.show();
                }
            });

            confirmDeleteBtn.addEventListener('click', function() {
                if (deleteForm) {
                    deleteForm.submit();
                }
                deleteModal.hide();
            });
        });
    </script>

    @stack('scripts')

    <!-- Pusher & Laravel Echo for Real-time Updates -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script>
        // Wrap in try-catch to prevent errors from breaking the page
        try {
            // Enable Pusher logging for debugging (only in development)
            Pusher.logToConsole = {{ config('app.debug') ? 'true' : 'false' }};

            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: '{{ config('broadcasting.connections.pusher.key') }}',
                wsHost: '{{ config('broadcasting.connections.pusher.options.host') }}',
                wsPort: {{ config('broadcasting.connections.pusher.options.port') }},
                wssPort: {{ config('broadcasting.connections.pusher.options.port') }},
                forceTLS: false,
                encrypted: false,
                disableStats: true,
                enabledTransports: ['ws', 'wss'],
                cluster: 'mt1', // Required by Pusher but not used by Soketi
            });

            console.log('🔌 WebSocket Config:', {
                key: '{{ config('broadcasting.connections.pusher.key') }}',
                host: '{{ config('broadcasting.connections.pusher.options.host') }}',
                port: {{ config('broadcasting.connections.pusher.options.port') }},
                scheme: '{{ config('broadcasting.connections.pusher.options.scheme') }}'
            });

            // Debug connection events - wrapped in try-catch
            if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                window.Echo.connector.pusher.connection.bind('connected', function() {
                    console.log('✅ WebSocket Connected Successfully');
                });

                window.Echo.connector.pusher.connection.bind('connecting', function() {
                    console.log('🔄 WebSocket Connecting...');
                });

                window.Echo.connector.pusher.connection.bind('disconnected', function() {
                    console.log('⚠️ WebSocket Disconnected');
                });

                window.Echo.connector.pusher.connection.bind('error', function(err) {
                    console.error('❌ WebSocket Connection Error:', err);
                    // Don't throw - just log
                });

                window.Echo.connector.pusher.connection.bind('state_change', function(states) {
                    console.log('🔄 Connection State Changed:', states.previous, '->', states.current);
                });
            }
        } catch (error) {
            // Log error but don't break the page
            console.error('WebSocket initialization failed:', error);
            console.warn('The application will continue without real-time features');
            // Create a dummy Echo object to prevent "Echo is not defined" errors
            window.Echo = {
                channel: function() { return this; },
                private: function() { return this; },
                listen: function() { return this; },
                notification: function() { return this; },
                connector: null
            };
        }
    </script>
</body>

</html>
