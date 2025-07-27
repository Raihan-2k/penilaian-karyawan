@props(['title', 'value', 'icon' => 'user-group', 'color' => 'blue'])

@php
    // Warna ikon dan border
    $textColor = match($color) {
        'green' => 'text-green-600',
        'yellow' => 'text-yellow-500',
        default => 'text-blue-600',
    };

    $bgColor = match($color) {
        'green' => 'bg-green-100',
        'yellow' => 'bg-yellow-100',
        default => 'bg-blue-100',
    };

    $borderColor = match($color) {
        'green' => 'border-green-400',
        'yellow' => 'border-yellow-400',
        default => 'border-blue-400',
    };

@endphp

<div class="flex items-center p-4 bg-white rounded-lg shadow-md border-l-4 {{ $borderColor }}">
    <div class="p-3 rounded-full {{ $bgColor }} {{ $textColor }}">

    </div>
    <div class="ml-4">
        <h4 class="text-lg font-semibold text-gray-700">{{ $title }}</h4>
        <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
    </div>
</div>
