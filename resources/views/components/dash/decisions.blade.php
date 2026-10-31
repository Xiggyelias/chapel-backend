@props(['requests'])

@if($requests->isEmpty())
    <p class="text-sm text-slate-500">No proposals are waiting for a decision.</p>
@else
    <div class="overflow-x-auto -mx-5">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="text-[11px] uppercase tracking-wide text-slate-500 border-b border-slate-100">
                    <th scope="col" class="px-5 py-2 font-semibold">Activity</th>
                    <th scope="col" class="px-3 py-2 font-semibold">Date</th>
                    <th scope="col" class="px-3 py-2 font-semibold">Flags</th>
                    <th scope="col" class="px-5 py-2"><span class="sr-only">Action</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($requests as $r)
                    @php $days = (int) now()->startOfDay()->diffInDays($r->event_date, false); @endphp
                    <tr>
                        <td class="px-5 py-2.5 min-w-[12rem]">
                            <div class="font-semibold text-slate-900 truncate max-w-xs">{{ $r->title }}</div>
                            <div class="text-xs text-slate-500">{{ $r->club?->name }} &bull; {{ $r->reference_no }}</div>
                        </td>
                        <td class="px-3 py-2.5 whitespace-nowrap">
                            <div class="text-slate-800">{{ $r->event_date->format('M j') }}</div>
                            <div class="text-xs {{ $days <= 7 ? 'text-rose-700 font-semibold' : 'text-slate-500' }}">{{ $days <= 0 ? 'today' : "in {$days} days" }}</div>
                        </td>
                        <td class="px-3 py-2.5">
                            <div class="flex flex-wrap gap-1">
                                @if($r->vc_go_ahead_needed && $r->vc_go_ahead_status !== 'received')
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">VC</span>
                                @endif
                                @if($r->is_late_submission)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Late</span>
                                @endif
                                @if((int) $r->expected_attendance >= 300)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800">{{ (int) $r->expected_attendance }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-2.5 text-right">
                            <a href="{{ route('requests.show', $r->id) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#DC2626] hover:bg-[#B91C1C] text-white whitespace-nowrap">Review</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
