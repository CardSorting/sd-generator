@props([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'py-1 bg-white',
    'trigger'
])

@php
switch ($align) {
    case 'left':
        $alignmentClasses = 'left-0 origin-top-left';
        break;
    case 'top':
        $alignmentClasses = 'bottom-full origin-bottom-right mb-1';
        break;
    case 'right':
    default:
        $alignmentClasses = 'right-0 origin-top-right';
        break;
}

switch ($width) {
    case '48':
        $width = 'w-48';
        break;
    case '80':
        $width = 'w-80';
        break;
}
@endphp

<div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
    <div @click="open = !open" @keydown.escape="open = false">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}"
         @click="open = false"
         x-cloak>
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $slot }}
        </div>
    </div>
</div>
