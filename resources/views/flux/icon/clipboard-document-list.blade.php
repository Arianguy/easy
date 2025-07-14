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
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3-7.5H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18v-7.5A2.25 2.25 0 0 1 6 8.25h1.5m9 0a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 16.5 20.25H7.5A2.25 2.25 0 0 1 5.25 18v-7.5A2.25 2.25 0 0 1 7.5 8.25h1.5M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
            @break
        @case ('solid')
            <path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0 1 18 9.375v9.375a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V9.375a3.375 3.375 0 0 1 3.502-3.375ZM14.25 9.75a.75.75 0 0 1 .75.75v.75h.75a.75.75 0 0 1 0 1.5H15v.75a.75.75 0 0 1-1.5 0v-.75h-.75a.75.75 0 0 1 0-1.5h.75v-.75a.75.75 0 0 1 .75-.75ZM6 12a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 0 1.5H6.75A.75.75 0 0 1 6 12Zm0 3a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 0 1.5H6.75A.75.75 0 0 1 6 15Zm0 3a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 0 1.5H6.75A.75.75 0 0 1 6 18Z" clip-rule="evenodd"/>
            <path d="M10.5 2.25a.75.75 0 0 0-1.5 0v.54l1.838-.775a9.465 9.465 0 0 1 6.725 0L18.5 2.79v-.54a.75.75 0 0 0-1.5 0 2.25 2.25 0 0 1-4.5 0Z"/>
            @break
    @endswitch
</svg>
