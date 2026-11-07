@props(['services', 'canEdit' => false])

@forelse($services as $service)
    @php
        $checks = [
            'Preacher materials' => $service->preacher_submitted_materials,
            'Media notified' => $service->media_notified,
            'Sheet finalised' => in_array($service->status, ['ready', 'completed'], true),
        ];
        $done = count(array_filter($checks));
        $circ = 2 * M_PI * 15;
    @endphp
    <div class="py-4 first:pt-0 last:pb-0 border-b last:border-0 border-slate-100">
        <div class="flex items-start gap-3.5">
            {{-- Readiness ring --}}
            <div class="relative w-11 h-11 shrink-0" role="img" aria-label="{{ $done }} of 3 ready">
                <svg class="w-11 h-11 -rotate-90" viewBox="0 0 36 36" aria-hidden="true">
                    <circle cx="18" cy="18" r="15" fill="none" stroke="currentColor" stroke-width="3.5" class="text-slate-100"/>
                    <circle cx="18" cy="18" r="15" fill="none" stroke-width="3.5" stroke-linecap="round"
                            stroke="{{ $done === 3 ? '#059669' : 'var(--color-gold)' }}"
                            stroke-dasharray="{{ round($circ * $done / 3, 2) }} {{ round($circ, 2) }}"/>
                </svg>
                <span class="absolute inset-0 grid place-items-center text-[11px] font-bold tabular-nums {{ $done === 3 ? 'text-emerald-700' : 'text-slate-700' }}">{{ $done }}/3</span>
            </div>
            <div class="min-w-0 flex-1">
                <a href="{{ route('worship.show', $service->id) }}" class="text-[14px] font-semibold text-slate-900 hover:text-brand block transition-colors">
                    {{ $service->service_type }}
                </a>
                <p class="text-[12.5px] text-slate-600">{{ $service->service_date->format('l, j M') }}</p>
                <p class="text-[12.5px] text-slate-500 truncate">{{ $service->theme ?: 'Theme not set' }} · {{ $service->preacher ?: 'Preacher not assigned' }}</p>
            </div>
        </div>
        <ul class="mt-3 flex flex-wrap gap-1.5">
            @foreach($checks as $label => $ok)
                <li class="badge {{ $ok ? 'badge-emerald' : '' }}">
                    @if($ok)<x-icon name="check" class="w-3.5 h-3.5" stroke="2.5" />@else<span class="w-2 h-2 rounded-full ring-[1.5px] ring-slate-400" aria-hidden="true"></span>@endif
                    <span class="sr-only">{{ $ok ? 'Done:' : 'Pending:' }}</span>{{ $label }}
                </li>
            @endforeach
            @if($service->sign_interpreter_required)
                <li class="badge badge-sky">Sign interpreter</li>
            @endif
        </ul>
        <div class="mt-3 flex flex-wrap gap-2">
            <a href="{{ route('worship.slides', $service->id) }}" class="btn btn-secondary btn-sm"><x-icon name="presentation" /> Slides</a>
            <a href="{{ route('worship.bulletin', $service->id) }}" class="btn btn-secondary btn-sm"><x-icon name="printer" /> Bulletin</a>
            @if($canEdit)
                <a href="{{ route('worship.edit', $service->id) }}" class="btn btn-ghost btn-sm"><x-icon name="pencil" /> Edit</a>
            @endif
        </div>
    </div>
@empty
    <div class="text-center py-4">
        <p class="text-[14px] text-slate-500">No upcoming services planned yet.</p>
        @if($canEdit)
            <a href="{{ route('worship.create') }}" class="btn btn-soft btn-sm mt-3"><x-icon name="plus" /> Plan a service</a>
        @endif
    </div>
@endforelse
