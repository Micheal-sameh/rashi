@extends('errors.layout')

@section('title', __('messages.error_500_title'))

@section('content')
<main class="min-h-screen flex items-center justify-center p-gutter">
    <div class="w-full max-w-2xl mx-auto text-center flex flex-col items-center">
        <div class="mb-8 w-64 h-64 md:w-80 md:h-80 relative rounded-xl overflow-hidden shadow-level-1 bg-surface-container-lowest flex items-center justify-center">
            <div class="absolute inset-0 bg-secondary-container/20 rounded-xl"></div>
            <span class="material-symbols-outlined icon-fill text-[100px] text-primary">dns</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="material-symbols-outlined text-[120px] text-error/80 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">warning</span>
            </div>
        </div>

        <h1 class="font-display text-display text-on-surface mb-4">{{ __('messages.error_500_title') }}</h1>
        <h2 class="font-headline-md text-headline-md text-secondary mb-4">{{ __('messages.error_500_subheading') }}</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant max-w-lg mb-8">
            {{ __('messages.error_500_description') }}
        </p>

        <div class="flex flex-col sm:flex-row gap-stack_md w-full justify-center">
            <button type="button" onclick="window.location.reload();"
                class="flex items-center justify-center gap-2 bg-primary text-on-primary rounded-[10px] px-6 py-3 font-title-lg text-title-lg transition-transform hover:scale-[1.02] active:scale-95 shadow-level-1">
                <span class="material-symbols-outlined">refresh</span>
                {{ __('messages.error_try_again') }}
            </button>
            <a href="{{ route('competitions.index') }}"
                class="flex items-center justify-center gap-2 bg-surface-container-low text-on-secondary-fixed-variant rounded-[10px] px-6 py-3 font-title-lg text-title-lg transition-colors hover:bg-surface-container-high">
                <span class="material-symbols-outlined">dashboard</span>
                {{ __('messages.error_back_to_dashboard') }}
            </a>
        </div>
    </div>
</main>
@endsection
