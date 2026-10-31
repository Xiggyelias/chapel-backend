@props(['title', 'link' => null, 'linkLabel' => 'View all'])

<section {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200 shadow-2xs']) }}>
    <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-bold text-slate-900">{{ $title }}</h2>
        @if($link)
            <a href="{{ $link }}" class="text-xs font-semibold text-[#DC2626] hover:underline shrink-0">{{ $linkLabel }} &rarr;</a>
        @endif
    </div>
    <div class="p-5">
        {{ $slot }}
    </div>
</section>
