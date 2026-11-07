@props(['requests'])

@if($requests->isEmpty())
    <p class="text-[13px] text-slate-500">No proposals are waiting for a decision.</p>
@else
    <div class="table-wrap -mx-5 -my-5">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col" class="!pl-5">Activity</th>
                    <th scope="col">Date</th>
                    <th scope="col">Flags</th>
                    <th scope="col" class="!pr-5"><span class="sr-only">Action</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $r)
                    @php $days = (int) now()->startOfDay()->diffInDays($r->event_date, false); @endphp
                    <tr>
                        <td class="!pl-5 min-w-[12rem]">
                            <div class="font-semibold text-slate-900 truncate max-w-xs">{{ $r->title }}</div>
                            <div class="text-[11.5px] text-slate-500">{{ $r->club?->name }} · <span class="tabular-nums">{{ $r->reference_no }}</span></div>
                        </td>
                        <td class="whitespace-nowrap">
                            <div class="text-slate-800">{{ $r->event_date->format('j M') }}</div>
                            <div class="text-[11.5px] {{ $days <= 7 ? 'text-brand font-semibold' : 'text-slate-500' }}">{{ $days <= 0 ? 'today' : "in {$days} days" }}</div>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @if($r->vc_go_ahead_needed && $r->vc_go_ahead_status !== 'received')
                                    <span class="badge badge-purple">VC</span>
                                @endif
                                @if($r->is_late_submission)
                                    <span class="badge badge-amber">Late</span>
                                @endif
                                @if((int) $r->expected_attendance >= 300)
                                    <span class="badge badge-sky tabular-nums">{{ (int) $r->expected_attendance }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="!pr-5 text-right">
                            <a href="{{ route('requests.show', $r->id) }}" class="btn btn-primary btn-sm">Review</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
