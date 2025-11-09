@props(['color' => 'gray'])

<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white bg-{{ $color }}-600">
    {{ $slot }}
</span>
