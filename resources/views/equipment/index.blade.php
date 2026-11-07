@extends('layouts.app')

@section('title', 'Sound and Equipment')

@section('content')
<div class="page-shell space-y-6">

    <x-page-header eyebrow="Kwang-Lim Chapel · Inventory & Logistics" title="Sound and Equipment"
                   description="Centralized register of audiovisual, sound, musical, and equipment requests and dispatch statuses">
        @if(!auth()->user()->isCampusLifeSrc())
            <button type="button" onclick="document.getElementById('equipmentModal').classList.toggle('hidden')" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Request Equipment
            </button>
        @endif
    </x-page-header>

    {{-- Equipment register --}}
    <section class="card overflow-hidden">
        <div class="card-header">
            <div>
                <p class="card-kicker">Register</p>
                <h2 class="card-title mt-0.5">Equipment Dispatch Log</h2>
            </div>
            <span class="text-[12px] text-slate-500 font-medium"><span class="font-display text-[1.05rem] text-slate-900 tabular-nums">{{ $equipmentRequests->total() }}</span> Total Requests</span>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Item & Qty</th>
                        <th>Requesting Club</th>
                        <th>Owner Department</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipmentRequests as $item)
                        <tr>
                            <td>
                                <div class="font-semibold text-slate-900">{{ $item->item_name }}</div>
                                <div class="text-[11.5px] text-slate-500 mt-0.5">Qty: <span class="tabular-nums">{{ $item->quantity }}</span> &bull; By {{ $item->requester?->name ?? 'Chapel Requester' }}</div>
                            </td>

                            <td class="whitespace-nowrap">
                                <span class="badge badge-dot" style="color: {{ $item->club?->color_hex ?? '#A51C30' }}">
                                    <span class="text-slate-700">{{ $item->club?->code }}</span>
                                </span>
                            </td>

                            <td class="text-slate-600">
                                {{ $item->owner }}
                            </td>

                            <td class="whitespace-nowrap">
                                <div class="font-semibold text-slate-800 tabular-nums">{{ $item->needed_date->format('M d, Y') }}</div>
                                <div class="text-[11.5px] text-slate-400 tabular-nums mt-0.5">Return: {{ $item->return_date ? $item->return_date->format('M d, Y') : 'Same Day' }}</div>
                            </td>

                            <td class="whitespace-nowrap">
                                @if($item->status === 'approved')
                                    <span class="badge badge-sky badge-dot">Approved</span>
                                @elseif($item->status === 'issued')
                                    <span class="badge badge-amber badge-dot">Issued / In Use</span>
                                @elseif($item->status === 'returned')
                                    <span class="badge badge-emerald badge-dot">Returned Clean</span>
                                @elseif($item->status === 'rejected')
                                    <span class="badge badge-rose badge-dot">Rejected</span>
                                @else
                                    <span class="badge badge-dot">Requested</span>
                                @endif
                            </td>

                            <td class="text-right whitespace-nowrap">
                                @if(auth()->user()->isOffice())
                                    <form method="POST" action="{{ route('equipment.status', $item->id) }}" class="inline-flex items-center">
                                        @csrf
                                        <label for="eqStatus{{ $item->id }}" class="sr-only">Update status for {{ $item->item_name }}</label>
                                        <select id="eqStatus{{ $item->id }}" name="status" onchange="this.form.submit()" class="field !w-auto !py-1.5 !pl-2.5 !text-[11.5px]">
                                            <option value="requested" {{ $item->status === 'requested' ? 'selected' : '' }}>Requested</option>
                                            <option value="approved" {{ $item->status === 'approved' ? 'selected' : '' }}>Approve</option>
                                            <option value="issued" {{ $item->status === 'issued' ? 'selected' : '' }}>Issue Out</option>
                                            <option value="returned" {{ $item->status === 'returned' ? 'selected' : '' }}>Mark Returned</option>
                                            <option value="rejected" {{ $item->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                        </select>
                                    </form>
                                @else
                                    <span class="text-slate-400 text-[11.5px]">View Only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="!p-0">
                                <div class="empty-state">
                                    <div class="empty-state-mark">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                    </div>
                                    <h3 class="empty-state-title">No Equipment Requests Logged</h3>
                                    <p class="empty-state-text">Request sound systems, wireless microphones, projectors, and instruments for events.</p>
                                    @if(!auth()->user()->isCampusLifeSrc())
                                        <button type="button" onclick="document.getElementById('equipmentModal').classList.toggle('hidden')" class="btn btn-secondary btn-sm mt-4">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Request Equipment
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($equipmentRequests->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $equipmentRequests->links() }}
            </div>
        @endif
    </section>

</div>

{{-- Equipment loan request modal --}}
<div id="equipmentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink/50 backdrop-blur-sm p-4 modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="equipmentModalTitle">
    <div class="bg-white rounded-3xl shadow-lg max-w-md w-full ring-1 ring-black/5 overflow-hidden modal-panel max-h-[calc(100vh-2rem)] overflow-y-auto">
        <div class="px-6 pt-6 pb-4 flex items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Sound &amp; Media Department</p>
                <h3 id="equipmentModalTitle" class="font-display text-xl text-slate-900">Request Equipment Loan</h3>
            </div>
            <button type="button" onclick="document.getElementById('equipmentModal').classList.add('hidden')" class="topbar-action -mr-2 -mt-1 cursor-pointer" aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('equipment.store') }}" class="px-6 pb-6 space-y-4">
            @csrf

            @if(auth()->user()->isOffice())
                <div>
                    <label for="eqClubId" class="field-label">Requesting Club</label>
                    <select id="eqClubId" name="club_id" class="field">
                        @foreach($clubs as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </div>
            @else
                <input type="hidden" name="club_id" value="{{ auth()->user()->club_id }}">
            @endif

            <div>
                <label for="eqItemName" class="field-label">Equipment Item Needed <span class="text-brand">*</span></label>
                <select id="eqItemName" name="item_name" required class="field">
                    <option value="Wireless Shure Microphones (Pair)">Wireless Shure Microphones (Pair)</option>
                    <option value="Yamaha Portable Sound PA System">Yamaha Portable Sound PA System</option>
                    <option value="Epson Multimedia Projector & Screen">Epson Multimedia Projector & Screen</option>
                    <option value="Roland Digital Stage Piano">Roland Digital Stage Piano</option>
                    <option value="Pearl Drum Set & Cymbals">Pearl Drum Set & Cymbals</option>
                    <option value="Collegiate Wooden Chapel Lectern / Podium">Collegiate Wooden Chapel Lectern / Podium</option>
                    <option value="XLR Audio Cables & Microphone Stands">XLR Audio Cables & Microphone Stands</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="eqQuantity" class="field-label">Quantity <span class="text-brand">*</span></label>
                    <input id="eqQuantity" type="number" name="quantity" value="1" min="1" required class="field">
                </div>
                <div>
                    <label for="eqNeededDate" class="field-label">Date Needed <span class="text-brand">*</span></label>
                    <input id="eqNeededDate" type="date" name="needed_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required class="field">
                </div>
            </div>

            <div>
                <label for="eqReturnDate" class="field-label">Expected Return Date</label>
                <input id="eqReturnDate" type="date" name="return_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" class="field">
            </div>

            <div>
                <label for="eqPurpose" class="field-label">Purpose / Service <span class="text-brand">*</span></label>
                <textarea id="eqPurpose" name="purpose" rows="2" required placeholder="Explain where and for what service this item will be utilized..." class="field"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('equipmentModal').classList.add('hidden')" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
