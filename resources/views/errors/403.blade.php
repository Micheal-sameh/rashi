@extends('errors.layout')

@section('title', __('messages.error_403_heading'))

@section('content')
<main class="min-h-screen flex items-center justify-center relative overflow-hidden">
    <div class="absolute inset-0 z-0 pointer-events-none flex items-center justify-center opacity-30">
        <div class="w-[800px] h-[800px] rounded-full bg-gradient-to-br from-error-container to-surface blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-2xl px-gutter">
        <div class="bg-surface-container-lowest rounded-2xl shadow-level-2 overflow-hidden border border-surface-variant/50 relative">
            <div class="h-2 w-full bg-error"></div>
            <div class="p-10 md:p-14 text-center flex flex-col items-center">
                <div class="mb-8 relative flex items-center justify-center">
                    <div class="absolute w-24 h-24 bg-error-container rounded-full animate-pulse opacity-50"></div>
                    <div class="w-20 h-20 bg-error-container text-error rounded-full flex items-center justify-center z-10 border-4 border-surface-container-lowest">
                        <span class="material-symbols-outlined icon-fill text-[40px]">lock</span>
                    </div>
                </div>

                <div class="font-label-md text-label-md text-error tracking-widest uppercase mb-3 font-semibold">
                    {{ __('messages.error_403_badge') }}
                </div>

                <h1 class="font-display text-display text-on-surface mb-4">
                    {{ __('messages.error_403_heading') }}
                </h1>

                <p class="font-body-lg text-body-lg text-secondary max-w-md mx-auto mb-10 leading-relaxed">
                    {{ __('messages.error_403_description', ['app' => config('app.name', 'Rashi')]) }}
                </p>

                <div class="flex flex-col sm:flex-row gap-4 w-full justify-center">
                    <a href="{{ route('competitions.index') }}"
                        class="bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high transition-colors duration-200 px-6 py-3 rounded-[10px] font-body-md text-body-md font-semibold flex items-center justify-center gap-2 border border-outline-variant/50 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                        {{ __('messages.error_back_to_dashboard') }}
                    </a>
                    <a href="mailto:{{ config('mail.from.address') }}"
                        class="bg-primary text-on-primary hover:bg-primary-container transition-colors duration-200 px-6 py-3 rounded-[10px] font-body-md text-body-md font-semibold flex items-center justify-center gap-2 shadow-md focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        <span class="material-symbols-outlined text-[20px]">vpn_key</span>
                        {{ __('messages.error_request_access') }}
                    </a>
                </div>
            </div>

            <div class="bg-surface-container/30 px-10 py-5 border-t border-surface-variant flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-secondary text-[24px]">admin_panel_settings</span>
                    <span class="font-label-md text-label-md text-secondary font-semibold tracking-wide uppercase">{{ config('app.name', 'Rashi') }} Admin</span>
                </div>
                <div class="text-xs text-secondary font-body-md">
                    {{ __('messages.error_session_id') }}: <span class="font-mono text-on-surface-variant bg-surface-container px-2 py-1 rounded">{{ strtoupper(substr(session()->getId(), 0, 8)) }}</span>
                </div>
            </div>
        </div>

        <div class="absolute -top-10 -end-10 z-0">
            <span class="material-symbols-outlined text-[120px] text-surface-variant/20 rotate-12">security</span>
        </div>
    </div>
</main>
@endsection
