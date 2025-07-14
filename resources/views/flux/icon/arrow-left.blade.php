@props([
    'variant' => 'outline',
])

<svg {{ $attributes->class([
    'shrink-0',
    match ($variant) {
        'outline' => 'stroke-2',
        'solid' => 'fill-current',
        'mini' => 'stroke-2',
        'micro' => 'fill-current stroke-2',
    }
])->except('variant') }} data-flux-icon viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    @switch ($variant)
        @case ('outline')
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
            @break
        @case ('solid')
            <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 1 1 1.06 1.06L9.31 12l6.97 6.97a.75.75 0 1 1-1.06 1.06l-7.5-7.5Z" clip-rule="evenodd"/>
            @break
    @endswitch
</svg>
