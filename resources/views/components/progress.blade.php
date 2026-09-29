@props([
    'value' => 0,
    'label' => null,
    'valueText' => null,
    'color' => 'primary', // primary, success, info, warning, danger
    'size' => null, // 'sm' or null
    'striped' => false,
    'animated' => false,
])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    @if($label || $valueText || $slot->isNotEmpty())
        <h4 class="small font-weight-bold d-flex justify-content-between align-items-center mb-1">
            <span>{{ $label ?? $slot }}</span>
            <span>{{ $valueText ?? ($value . '%') }}</span>
        </h4>
    @endif
    <div class="progress {{ $size === 'sm' ? 'progress-sm' : '' }}">
        <div class="progress-bar bg-{{ $color }} {{ $striped ? 'progress-bar-striped' : '' }} {{ $animated ? 'progress-bar-animated' : '' }}"
            role="progressbar"
            style="width: {{ $value }}%"
            aria-valuenow="{{ $value }}"
            aria-valuemin="0"
            aria-valuemax="100"></div>
    </div>
</div>
