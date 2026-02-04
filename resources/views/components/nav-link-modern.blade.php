@props(['active', 'icon' => null])

@php
    $classes = ($active ?? false)
        ? 'flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-700 shadow-sm ring-1 ring-gray-200 dark:ring-gray-600 transition-all duration-300 transform scale-105'
        : 'flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-white/50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200 transition-all duration-300 hover:scale-105';

    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
        'clock' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0" />',
    ];
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon && isset($icons[$icon]))
        <svg class="w-4 h-4 {{ $active ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300' }}"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            {!! $icons[$icon] !!}
        </svg>
    @endif

    <span>{{ $slot }}</span>
</a>