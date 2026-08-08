@extends('errors.layout')

@section('title', __('messages.database_error_heading'))

@section('content')
<main class="min-h-screen flex flex-col justify-center items-center relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-1/4 -end-1/4 w-[800px] h-[800px] bg-primary/5 rounded-full blur-3xl opacity-60 mix-blend-multiply"></div>
        <div class="absolute -bottom-1/4 -start-1/4 w-[600px] h-[600px] bg-error/5 rounded-full blur-3xl opacity-60 mix-blend-multiply"></div>
    </div>

    <div class="z-10 w-full max-w-lg px-gutter py-container_padding flex flex-col items-center justify-center text-center">
        <div class="bg-surface-container-lowest rounded-xl shadow-level-2 w-full overflow-hidden border border-surface-container-highest/50 flex flex-col">
            <div class="bg-surface-container-low py-stack_lg px-stack_lg flex flex-col items-center justify-center border-b border-surface-container-highest/50 relative overflow-hidden">
                <div class="relative w-24 h-24 mb-stack_md flex items-center justify-center rounded-full bg-error-container text-on-error-container shadow-sm border border-error/20 z-10">
                    <span class="material-symbols-outlined icon-fill text-[48px]">dns</span>
                    <div class="absolute -bottom-1 -end-1 w-10 h-10 rounded-full bg-surface-container-lowest flex items-center justify-center shadow-md">
                        <div class="w-8 h-8 rounded-full bg-error flex items-center justify-center text-on-error">
                            <span class="material-symbols-outlined icon-fill text-[20px]">warning</span>
                        </div>
                    </div>
                </div>
                <h1 class="font-headline-lg text-headline-lg text-on-surface z-10">{{ __('messages.database_error_heading') }}</h1>
                <div class="inline-flex items-center gap-2 mt-stack_sm px-3 py-1 bg-surface-container-highest rounded-full text-secondary font-label-md text-label-md z-10">
                    <span class="w-2 h-2 rounded-full bg-error animate-pulse"></span>
                    {{ __('messages.database_error_connection_refused') }}
                </div>
            </div>

            <div class="p-stack_lg flex flex-col items-center gap-stack_lg bg-surface-container-lowest">
                <p class="font-body-lg text-body-lg text-on-surface-variant max-w-sm text-center">
                    {{ __('messages.database_error_description') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-stack_sm w-full mt-stack_md">
                    <button type="button" onclick="window.location.reload();"
                        class="flex-1 inline-flex items-center justify-center gap-2 bg-primary text-on-primary font-label-md text-label-md py-3 px-6 rounded-lg shadow-sm hover:bg-primary/90 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        <span class="material-symbols-outlined text-[18px]">refresh</span>
                        {{ __('messages.error_try_again') }}
                    </button>
                    <a href="mailto:{{ config('mail.from.address') }}"
                        class="flex-1 inline-flex items-center justify-center gap-2 bg-surface-container-low text-secondary font-label-md text-label-md py-3 px-6 rounded-lg hover:bg-surface-container-high transition-colors duration-200 border border-outline-variant/30 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        <span class="material-symbols-outlined text-[18px]">support_agent</span>
                        {{ __('messages.error_contact_support') }}
                    </a>
                </div>
            </div>

            <div class="bg-surface-container-low py-4 px-6 border-t border-surface-container-highest flex justify-between items-center text-secondary font-label-md text-label-md">
                <span>{{ config('app.name', 'Rashi') }}</span>
                <span class="opacity-70">ERR_DB_CONN</span>
            </div>
        </div>
    </div>

    <footer class="absolute bottom-4 w-full flex justify-center px-gutter py-stack_md z-20">
        <p class="font-label-md text-label-md text-secondary">&copy; {{ date('Y') }} {{ config('app.name', 'Rashi') }}. {{ __('messages.all_rights_reserved') }}</p>
    </footer>
</main>
@endsection
