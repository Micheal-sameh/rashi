<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name', 'Rashi') }}</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'on-tertiary-fixed-variant': '#3f465c',
                        'on-tertiary': '#ffffff',
                        'secondary-fixed-dim': '#b7c8e1',
                        'surface-container': '#eceef0',
                        'on-error': '#ffffff',
                        'error': '#ba1a1a',
                        'on-primary-fixed-variant': '#3323cc',
                        'on-secondary-container': '#54647a',
                        'tertiary-fixed': '#dae2fd',
                        'primary': '#3525cd',
                        'surface-container-low': '#f2f4f6',
                        'background': '#f7f9fb',
                        'on-primary-container': '#dad7ff',
                        'surface': '#f7f9fb',
                        'surface-variant': '#e0e3e5',
                        'surface-container-lowest': '#ffffff',
                        'secondary-fixed': '#d3e4fe',
                        'surface-dim': '#d8dadc',
                        'inverse-surface': '#2d3133',
                        'on-background': '#191c1e',
                        'surface-container-high': '#e6e8ea',
                        'surface-container-highest': '#e0e3e5',
                        'primary-fixed': '#e2dfff',
                        'secondary-container': '#d0e1fb',
                        'on-primary-fixed': '#0f0069',
                        'on-secondary-fixed-variant': '#38485d',
                        'on-surface': '#191c1e',
                        'inverse-on-surface': '#eff1f3',
                        'outline-variant': '#c7c4d8',
                        'tertiary': '#41485e',
                        'on-tertiary-fixed': '#131b2e',
                        'on-secondary': '#ffffff',
                        'on-error-container': '#93000a',
                        'error-container': '#ffdad6',
                        'surface-bright': '#f7f9fb',
                        'outline': '#777587',
                        'surface-tint': '#4d44e3',
                        'inverse-primary': '#c3c0ff',
                        'on-secondary-fixed': '#0b1c30',
                        'secondary': '#505f76',
                        'tertiary-fixed-dim': '#bec6e0',
                        'on-tertiary-container': '#d4dbf5',
                        'tertiary-container': '#586076',
                        'primary-fixed-dim': '#c3c0ff',
                        'primary-container': '#4f46e5',
                        'on-primary': '#ffffff',
                        'on-surface-variant': '#464555',
                    },
                    borderRadius: {
                        DEFAULT: '0.25rem',
                        lg: '0.5rem',
                        xl: '0.75rem',
                        full: '9999px',
                    },
                    spacing: {
                        gutter: '24px',
                        stack_lg: '24px',
                        sidebar_width: '260px',
                        container_padding: '32px',
                        stack_md: '16px',
                        stack_sm: '8px',
                    },
                    fontFamily: {
                        'headline-lg': ['Inter'],
                        'headline-lg-mobile': ['Inter'],
                        'title-lg': ['Inter'],
                        'headline-md': ['Inter'],
                        'body-lg': ['Inter'],
                        'label-md': ['Inter'],
                        'display': ['Inter'],
                        'body-md': ['Inter'],
                    },
                    fontSize: {
                        'headline-lg': ['32px', { lineHeight: '1.3', letterSpacing: '-0.01em', fontWeight: '700' }],
                        'headline-lg-mobile': ['28px', { lineHeight: '1.3', fontWeight: '700' }],
                        'title-lg': ['20px', { lineHeight: '1.5', fontWeight: '600' }],
                        'headline-md': ['24px', { lineHeight: '1.4', fontWeight: '600' }],
                        'body-lg': ['16px', { lineHeight: '1.6', fontWeight: '400' }],
                        'label-md': ['12px', { lineHeight: '1', letterSpacing: '0.05em', fontWeight: '600' }],
                        'display': ['48px', { lineHeight: '1.2', letterSpacing: '-0.02em', fontWeight: '700' }],
                        'body-md': ['14px', { lineHeight: '1.6', fontWeight: '400' }],
                    },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .icon-fill {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .shadow-level-1 {
            box-shadow: 0px 4px 6px -1px rgba(15, 23, 42, 0.05), 0px 2px 4px -2px rgba(15, 23, 42, 0.05);
        }
        .shadow-level-2 {
            box-shadow: 0px 10px 15px -3px rgba(15, 23, 42, 0.1);
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-background text-on-surface antialiased">
    @yield('content')
</body>
</html>
