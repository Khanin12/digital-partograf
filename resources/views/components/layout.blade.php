@props(['title' => null])

<x-layouts.master :title="$title" {{ $attributes }}>
    {{ $slot }}
</x-layouts.master>
