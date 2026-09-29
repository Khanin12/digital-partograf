@props([
    'title',
    'value',
    'icon' => 'fas fa-calendar',
    'color' => 'primary', // primary, success, info, warning, danger
    'progress' => null,
    'progressColor' => null,
    'subtitle' => null,
    'col' => 'col-xl-3 col-md-6 mb-4',
])

<div class="{{ $col }}">
    <div {{ $attributes->merge(['class' => 'card border-left-' . $color . ' shadow h-100 py-2']) }}>
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-{{ $color }} text-uppercase mb-1">
                        {{ $title }}
                    </div>

                    @if($progress !== null)
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $value }}</div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-{{ $progressColor ?? $color }}" role="progressbar"
                                        style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $value }}</div>
                    @endif

                    @if($subtitle)
                        <div class="text-xs text-muted mt-1">{{ $subtitle }}</div>
                    @endif
                </div>
                <div class="col-auto">
                    <i class="{{ $icon }} fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>

