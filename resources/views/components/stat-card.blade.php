@props(['label', 'value' => '0', 'icon' => null, 'color' => 'indigo', 'sub' => null])

@php
$bgMap   = ['indigo'=>'bg-indigo-100 dark:bg-indigo-900/40','blue'=>'bg-blue-100 dark:bg-blue-900/40','emerald'=>'bg-emerald-100 dark:bg-emerald-900/40','amber'=>'bg-amber-100 dark:bg-amber-900/40','red'=>'bg-red-100 dark:bg-red-900/40'];
$textMap = ['indigo'=>'text-indigo-700 dark:text-indigo-300','blue'=>'text-blue-700 dark:text-blue-300','emerald'=>'text-emerald-700 dark:text-emerald-300','amber'=>'text-amber-700 dark:text-amber-300','red'=>'text-red-700 dark:text-red-300'];
$bg   = $bgMap[$color]   ?? 'bg-gray-100 dark:bg-gray-800';
$text = $textMap[$color] ?? 'text-gray-700 dark:text-gray-300';
@endphp

<div class="bg-white dark:bg-gray-900 rounded-2xl p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 flex items-center gap-4">
    @if($icon)
    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $bg }} text-2xl">{{ $icon }}</div>
    @endif
    <div>
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">{{ $label }}</p>
        <p class="text-2xl font-black {{ $text }} mt-0.5">{{ $value }}</p>
        @if($sub)
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $sub }}</p>
        @endif
    </div>
</div>
