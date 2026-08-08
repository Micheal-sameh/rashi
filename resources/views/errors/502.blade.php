@extends('errors.layout')

@section('title', __('messages.error_502_heading'))

@section('content')
<main class="min-h-screen flex items-center justify-center p-gutter">
    <div class="max-w-2xl w-full bg-surface-container-lowest rounded-2xl shadow-level-1 p-8 md:p-12 text-center flex flex-col items-center justify-center relative overflow-hidden">
        <div class="absolute -top-24 -end-24 w-64 h-64 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -start-24 w-64 h-64 bg-error/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative w-48 h-48 mb-stack_lg flex items-center justify-center">
            <div class="absolute inset-0 bg-surface-container-low rounded-full animate-pulse opacity-50"></div>
            <span class="material-symbols-outlined icon-fill text-8xl text-secondary-container relative z-10">cloud_off</span>
            <span class="material-symbols-outlined text-error absolute top-4 end-8 text-2xl animate-bounce">warning</span>
            <span class="material-symbols-outlined text-outline absolute bottom-8 start-4 text-xl">sync_problem</span>
        </div>

        <h1 class="font-display text-display text-primary mb-stack_sm">502</h1>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-stack_md">{{ __('messages.error_502_heading') }}</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant max-w-md mx-auto mb-stack_lg">
            {{ __('messages.error_502_description') }}
        </p>

        <div class="flex flex-col sm:flex-row gap-stack_md w-full sm:w-auto mt-stack_md">
            <button type="button" onclick="window.location.reload();"
                class="bg-primary text-on-primary font-label-md text-label-md px-6 py-3 rounded-lg hover:bg-primary-container transition-colors duration-200 flex items-center justify-center gap-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                <span class="material-symbols-outlined text-lg">refresh</span>
                {{ __('messages.error_try_again') }}
            </button>
            <a href="{{ route('competitions.index') }}"
                class="bg-surface-container-low text-on-surface font-label-md text-label-md px-6 py-3 rounded-lg hover:bg-surface-container-high transition-colors duration-200 flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 border border-outline-variant">
                <span class="material-symbols-outlined text-lg">dashboard</span>
                {{ __('messages.error_back_to_dashboard') }}
            </a>
        </div>

        <div class="mt-12">
            <a href="mailto:{{ config('mail.from.address') }}"
                class="font-body-md text-body-md text-secondary hover:text-primary transition-colors flex items-center justify-center gap-1 group">
                <span class="material-symbols-outlined text-sm group-hover:text-primary transition-colors">support_agent</span>
                {{ __('messages.error_contact_it_support') }}
            </a>
        </div>
    </div>
</main>
@endsection
