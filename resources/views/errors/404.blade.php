@extends('errors.layout')

@section('title', __('messages.error_404_heading'))

@section('content')
<main class="min-h-screen flex items-center justify-center p-gutter relative overflow-hidden">
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary-container opacity-10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-secondary-container opacity-20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-2xl w-full text-center z-10">
        <div class="mb-stack_lg flex justify-center">
            <div class="w-32 h-32 rounded-3xl bg-surface-container-lowest shadow-level-2 flex items-center justify-center border border-surface-variant relative">
                <span class="material-symbols-outlined icon-fill text-[80px] text-primary">error</span>
                <div class="absolute -top-2 -end-2 w-8 h-8 bg-error-container text-on-error-container rounded-full flex items-center justify-center shadow-sm">
                    <span class="material-symbols-outlined text-sm font-bold">close</span>
                </div>
            </div>
        </div>

        <h1 class="font-display text-display text-on-surface mb-stack_sm">404</h1>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-stack_md">{{ __('messages.error_404_heading') }}</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant max-w-lg mx-auto mb-stack_lg">
            {{ __('messages.error_404_description') }}
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-stack_md">
            <a href="{{ route('competitions.index') }}"
                class="inline-flex items-center justify-center gap-2 bg-primary text-on-primary font-title-lg text-title-lg px-8 py-3 rounded-lg shadow-sm hover:opacity-90 transition-opacity focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 w-full sm:w-auto">
                <span class="material-symbols-outlined">dashboard</span>
                {{ __('messages.error_go_to_dashboard') }}
            </a>
            <a href="mailto:{{ config('mail.from.address') }}"
                class="inline-flex items-center justify-center gap-2 bg-[#f1f5f9] text-on-surface font-title-lg text-title-lg px-8 py-3 rounded-lg shadow-level-1 hover:bg-surface-container-high transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 w-full sm:w-auto">
                <span class="material-symbols-outlined">contact_support</span>
                {{ __('messages.error_contact_support') }}
            </a>
        </div>
    </div>
</main>
@endsection
