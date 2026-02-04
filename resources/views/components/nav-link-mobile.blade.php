@props(['active', 'icon' => null])

@php
    $classes = ($active ?? false)
        ? 'flex flex-col items-center justify-center w-full h-full text-blue-600 dark:text-blue-400 font-bold transition-transform duration-300 scale-105'
        : 'flex flex-col items-center justify-center w-full h-full text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-300';

    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
        'clock' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0" />',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'
    ];
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon && isset($icons[$icon]))
        <svg class="w-5 h-5 mb-0.5 {{ $active ? 'fill-current' : 'fill-none stroke-current' }}" viewBox="0 0 24 24"
            stroke-width="2">
            {!! $icons[$icon] !!}
        </svg>
    @endif
    <span class="text-[9px] uppercase tracking-wide leading-none">{{ $slot }}</span>
</a>