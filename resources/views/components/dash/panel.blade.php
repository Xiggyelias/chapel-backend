@props(['title', 'link' => null, 'linkLabel' => 'View all'])

<section {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200/80 shadow-sm']) }}>
    <div class="flex items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-100">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">{{ $title }}</h2>
        @if($link)
            <a href="{{ $link }}" class="text-xs font-semibold text-brand hover:underline shrink-0">{{ $linkLabel }} &rarr;</a>
        @endif
    </div>
    <div class="p-5">
        {{ $slot }}
    </div>
</section>
