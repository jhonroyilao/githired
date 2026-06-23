@props([])

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full border border-neutral-200 bg-neutral-50 px-2.5 py-0.5 text-xs font-semibold text-neutral-600']) }}>
    {{ $slot }}
</span>