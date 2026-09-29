@props([
    'type' => 'primary', // primary, success, info, warning, danger
    'title' => null,
    'icon' => null,
    'dismissible' => false,
])

<div {{ $attributes->merge(['class' => "alert alert-{$type} shadow-sm " . ($dismissible ? 'alert-dismissible fade show' : '')]) }} role="alert">
    <div class="d-flex align-items-start">
        @if($icon)
            <div class="mr-2 pt-1">
                <i class="{{ $icon }} fa-lg"></i>
            </div>
        @endif
        <div class="flex-grow-1">
            @if($title)
                <h6 class="alert-heading font-weight-bold mb-1">{{ $title }}</h6>
            @endif
            <div class="small mb-0">
                {{ $slot }}
            </div>
        </div>
        @if($dismissible)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        @endif
    </div>
</div>
