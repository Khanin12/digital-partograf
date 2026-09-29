@props([
    'title' => null,
    'icon' => null,
    'accent' => null, // e.g. 'primary', 'success', 'warning', 'danger', 'info' (adds border-left-*)
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
    'noPadding' => false,
])

<div {{ $attributes->merge(['class' => 'card shadow mb-4' . ($accent ? ' border-left-' . $accent : '')]) }}>
    @if($title || isset($headerAction) || isset($actions))
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between {{ $headerClass }}">
            <h6 class="m-0 font-weight-bold text-primary d-flex align-items-center">
                @if($icon)
                    <i class="{{ $icon }} mr-2"></i>
                @endif
                <span>{{ $title }}</span>
            </h6>
            {{ $headerAction ?? $actions ?? '' }}
        </div>
    @endif

    <div class="card-body {{ $noPadding ? 'p-0' : $bodyClass }}">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="card-footer bg-white py-2 {{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</div>

