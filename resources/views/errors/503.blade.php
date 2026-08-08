@extends('errors.layout')

@section('title', __('messages.maintenance_heading'))

@section('content')
<main class="min-h-screen flex flex-col justify-center items-center p-container_padding">
    <div class="w-full max-w-2xl bg-surface-container-lowest rounded-xl shadow-level-2 p-12 text-center flex flex-col items-center gap-stack_lg">
        <div class="flex flex-col items-center gap-4">
            <span class="material-symbols-outlined text-6xl text-primary">admin_panel_settings</span>
            <h1 class="font-display text-display text-primary">{{ config('app.name', 'Rashi') }}</h1>
        </div>

        <div class="relative w-48 h-48 my-8 flex items-center justify-center">
            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 100">
                <circle class="text-surface-container-highest" cx="50" cy="50" fill="transparent" r="45" stroke="currentColor" stroke-width="8"></circle>
            </svg>
            <svg class="absolute inset-0 w-full h-full progress-ring" viewBox="0 0 100 100" style="transform: rotate(-90deg);">
                <circle class="text-primary" cx="50" cy="50" fill="transparent" r="45" stroke="currentColor" stroke-dasharray="283" stroke-dashoffset="140" stroke-width="8"></circle>
            </svg>
            <span class="material-symbols-outlined text-5xl text-primary absolute">build</span>
        </div>

        <div class="flex flex-col gap-stack_sm items-center max-w-lg">
            <h2 class="font-headline-md text-headline-md text-on-surface">{{ __('messages.maintenance_heading') }}</h2>
            <p class="font-body-lg text-body-lg text-secondary text-center">
                @if (isset($exception) && $exception->getMessage() && $exception->getMessage() !== 'Service Unavailable')
                    {{ $exception->getMessage() }}
                @else
                    {{ __('messages.maintenance_description', ['app' => config('app.name', 'Rashi')]) }}
                @endif
            </p>
            <div class="mt-4 px-6 py-3 bg-secondary-container rounded-full border border-outline-variant">
                <p class="font-title-lg text-title-lg text-on-secondary-container">
                    {{ __('messages.maintenance_check_back_soon') }}
                </p>
            </div>
        </div>

        <div class="mt-8 flex flex-col sm:flex-row gap-stack_md w-full justify-center">
            <button type="button" onclick="window.location.reload();"
                class="flex items-center justify-center gap-2 px-6 py-3 bg-surface-container-high hover:bg-surface-variant text-on-surface rounded-lg transition-colors duration-200">
                <span class="material-symbols-outlined text-secondary">refresh</span>
                <span class="font-label-md text-label-md">{{ __('messages.error_try_again') }}</span>
            </button>
            <a href="mailto:{{ config('mail.from.address') }}"
                class="flex items-center justify-center gap-2 px-6 py-3 bg-surface-container-high hover:bg-surface-variant text-on-surface rounded-lg transition-colors duration-200">
                <span class="material-symbols-outlined text-secondary">help</span>
                <span class="font-label-md text-label-md">{{ __('messages.error_support_center') }}</span>
            </a>
        </div>
    </div>

    <footer class="mt-8 w-full flex justify-center px-gutter py-stack_md">
        <p class="font-label-md text-label-md text-secondary">&copy; {{ date('Y') }} {{ config('app.name', 'Rashi') }}. {{ __('messages.all_rights_reserved') }}</p>
    </footer>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const circle = document.querySelector('.progress-ring circle');
        if (!circle) return;
        const radius = circle.r.baseVal.value;
        const circumference = radius * 2 * Math.PI;
        circle.style.strokeDasharray = `${circumference} ${circumference}`;
        let offset = circumference;
        setInterval(() => {
            offset -= 5;
            if (offset < 0) offset = circumference;
            circle.style.strokeDashoffset = offset;
        }, 100);
    });
</script>
@endsection
