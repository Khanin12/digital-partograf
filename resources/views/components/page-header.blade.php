@props([
    'title',
    'subtitle' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'd-sm-flex align-items-center justify-content-between mb-4']) }}>
    <div>
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold d-flex align-items-center">
            @if($icon)
                <i class="{{ $icon }} text-primary mr-2"></i>
            @endif
            <span>{{ $title }}</span>
        </h1>
        @if($subtitle)
            <p class="text-muted mb-0 small mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions) || (isset($slot) && trim($slot) !== ''))
        <div class="mt-3 mt-sm-0">
            {{ $actions ?? $slot }}
        </div>
    @endif
</div>

