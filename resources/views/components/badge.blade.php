@props([
    'color' => 'primary', // primary, success, info, warning, danger, secondary, dark
    'icon' => null,
    'pill' => false,
])

@php
    $textClass = in_array($color, ['warning', 'light']) ? 'text-dark' : '';
    $pillClass = $pill ? 'badge-pill' : '';
@endphp

<span {{ $attributes->merge(['class' => "badge badge-{$color} px-2 py-1 {$textClass} {$pillClass}"]) }}>
    @if($icon)
        <i class="{{ $icon }} mr-1"></i>
    @endif
    {{ $slot }}
</span>
