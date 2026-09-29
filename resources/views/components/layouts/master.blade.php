@props(['title' => null])
@include('layouts.master', [
    'title' => $title,
    'slot' => $slot,
    'styles' => $styles ?? null,
    'scripts' => $scripts ?? null,
])
