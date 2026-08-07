@props(['icon' => null, 'title', 'subtitle' => null])

<div class="rs-page-header">
    <div class="rs-page-header-titles">
        @if($icon)
            <div class="rs-page-icon"><i class="fa {{ $icon }}"></i></div>
        @endif
        <div>
            <h1 class="rs-headline-lg mb-0">{{ $title }}</h1>
            @if($subtitle)
                <p class="rs-body-lg mb-0">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    @isset($actions)
        <div class="rs-page-header-actions">
            {{ $actions }}
        </div>
    @endisset
</div>
