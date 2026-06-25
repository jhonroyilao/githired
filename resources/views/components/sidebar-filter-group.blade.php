@props(['label', 'items' => []])

<p class="mt-5 text-sm font-black text-neutral-900">{{ $label }}</p>

<ul class="mt-2 space-y-1.5">
    @foreach ($items as $item)
        <li class="flex items-center justify-between">
            <label class="flex cursor-pointer items-center gap-2 text-sm text-neutral-700 hover:text-neutral-950">
                <input type="checkbox"
                       class="h-4 w-4 rounded border-neutral-300 accent-primarygreen"/>
                {{ $item }}
            </label>
            <span class="text-xs text-neutral-400">10</span>
        </li>
    @endforeach
</ul>