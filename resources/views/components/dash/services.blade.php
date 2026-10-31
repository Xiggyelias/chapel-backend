@props(['services', 'canEdit' => false])

@forelse($services as $service)
    @php
        $checks = [
            'Preacher materials' => $service->preacher_submitted_materials,
            'Media notified' => $service->media_notified,
            'Sheet finalised' => in_array($service->status, ['ready', 'completed'], true),
        ];
        $done = count(array_filter($checks));
    @endphp
    <div class="py-3 first:pt-0 last:pb-0 border-b last:border-0 border-slate-100">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <a href="{{ route('worship.show', $service->id) }}" class="text-sm font-semibold text-slate-900 hover:text-[#DC2626] truncate block">
                    {{ $service->service_type }} &bull; {{ $service->service_date->format('D, M j') }}
                </a>
                <p class="text-xs text-slate-500 truncate">
                    {{ $service->theme ?: 'Theme not set' }} &bull; {{ $service->preacher ?: 'Preacher not assigned' }}
                </p>
            </div>
            <span class="shrink-0 text-xs font-bold {{ $done === 3 ? 'text-emerald-700' : 'text-amber-700' }}">{{ $done }}/3 ready</span>
        </div>
        <ul class="mt-2 flex flex-wrap gap-1.5">
            @foreach($checks as $label => $ok)
                <li class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $ok ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $ok ? '✓' : '○' }} {{ $label }}
                </li>
            @endforeach
            @if($service->sign_interpreter_required)
                <li class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-sky-50 text-sky-800">Sign interpreter</li>
            @endif
        </ul>
        <div class="mt-2 flex gap-3 text-xs font-semibold">
            <a href="{{ route('worship.slides', $service->id) }}" class="text-[#DC2626] hover:underline">Slides</a>
            <a href="{{ route('worship.bulletin', $service->id) }}" class="text-[#DC2626] hover:underline">Bulletin</a>
            @if($canEdit)
                <a href="{{ route('worship.edit', $service->id) }}" class="text-slate-600 hover:underline">Edit</a>
            @endif
        </div>
    </div>
@empty
    <p class="text-sm text-slate-500">No upcoming services planned yet.</p>
    @if($canEdit)
        <a href="{{ route('worship.create') }}" class="inline-block mt-2 text-xs font-semibold text-[#DC2626] hover:underline">Plan a service &rarr;</a>
    @endif
@endforelse
