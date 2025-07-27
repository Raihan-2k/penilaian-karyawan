@props([
    'title',
    'href' => '#',
    'icon' => 'plus',
    'color' => 'blue',
    'description' => null
])

@php
    $bgClass = match($color) {
        'green' => 'bg-green-100 text-green-600 hover:bg-green-200',
        'yellow' => 'bg-yellow-100 text-yellow-600 hover:bg-yellow-200',
        'red' => 'bg-red-100 text-red-600 hover:bg-red-200',
        default => 'bg-blue-100 text-blue-600 hover:bg-blue-200',
    };

    // Load ikon SVG dari public/vendor/blade-heroicons/outline
    $iconPath = public_path("vendor/blade-ui-kit/blade-heroicons/resource/svg/{$icon}.svg");
    $iconContent = file_exists($iconPath)
        ? str_replace('<svg', '<svg class="w-6 h-6"', file_get_contents($iconPath))
        : '';
@endphp

<a href="{{ $href }}" class="flex items-center space-x-4 p-4 rounded-lg shadow-sm bg-white border hover:shadow-md transition group">
    <div class="p-3  {{ $bgClass }}">
        {!! $iconContent !!}
    </div>
    <div>
        <h3 class="text-base font-semibold text-gray-800 group-hover:text-{{ $color }}-700">
            {{ $title }}
        </h3>
        @if ($description)
            <p class="text-sm text-gray-500">{{ $description }}</p>
        @endif
    </div>
</a>
