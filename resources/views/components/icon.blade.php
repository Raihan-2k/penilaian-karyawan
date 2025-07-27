@props(['name', 'class' => 'w-5 h-5'])

@php
    $icons = [
        'users' => 'heroicons-outline/users',
        'clipboard-check' => 'heroicons-outline/clipboard-check',
        'clock' => 'heroicons-outline/clock',
        'chart-bar' => 'heroicons-outline/chart-bar',
        'user-plus' => 'heroicons-outline/user-add',
        'clipboard-list' => 'heroicons-outline/clipboard-list',
        'calendar-days' => 'heroicons-outline/calendar-days',
        'file-text' => 'heroicons-outline/document-text',
        'check-circle' => 'heroicons-outline/check-circle',
    ];
@endphp

@if(isset($icons[$name]))
    <x-dynamic-component :component="$icons[$name]" class="{{ $class }}" />
@else
    <span class="text-red-500">Icon not found</span>
@endif
