@extends('layouts.app')

@section('title', 'Sound and Equipment')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-[#DC2626] tracking-wider uppercase">
                <span>Kwang-Lim Chapel</span>
                <span>&bull;</span>
                <span class="text-slate-400 font-medium">Inventory & Logistics</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Sound and Equipment
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Centralized register of audiovisual, sound, musical, and equipment requests and dispatch statuses
            </p>
        </div>

        @if(!auth()->user()->isCampusLifeSrc())
            <button type="button" onclick="document.getElementById('equipmentModal').classList.toggle('hidden')" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider transition shadow-sm self-start sm:self-auto active:scale-95 cursor-pointer">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Request Equipment</span>
            </button>
        @endif
    </div>

    <!-- Equipment Register Table -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Equipment Dispatch Log</h3>
            <span class="text-xs text-slate-500 font-semibold">{{ $equipmentRequests->total() }} Total Requests</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold uppercase text-slate-600 font-cinzel">
                        <th class="p-4">Item & Qty</th>
                        <th class="p-4">Requesting Club</th>
                        <th class="p-4">Owner Department</th>
                        <th class="p-4">Schedule</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($equipmentRequests as $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $item->item_name }}</div>
                                <div class="text-[11px] text-slate-500">Qty: {{ $item->quantity }} &bull; By {{ $item->requester->name ?? 'Chapel Requester' }}</div>
                            </td>

                            <td class="p-4 whitespace-nowrap">
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded text-white" style="background-color: {{ $item->club->color_hex ?? '#DC2626' }}">
                                    {{ $item->club->code }}
                                </span>
                            </td>

                            <td class="p-4 text-slate-600 font-medium">
                                {{ $item->owner }}
                            </td>

                            <td class="p-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $item->needed_date->format('M d, Y') }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">Return: {{ $item->return_date ? $item->return_date->format('M d, Y') : 'Same Day' }}</div>
                            </td>

                            <td class="p-4 whitespace-nowrap">
                                @if($item->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">Approved</span>
                                @elseif($item->status === 'issued')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Issued / In Use</span>
                                @elseif($item->status === 'returned')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Returned Clean</span>
                                @elseif($item->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">Rejected</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">Requested</span>
                                @endif
                            </td>

                            <td class="p-4 text-right whitespace-nowrap">
                                @if(auth()->user()->isOffice())
                                    <form method="POST" action="{{ route('equipment.status', $item->id) }}" class="inline-flex items-center space-x-1">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" class="text-[11px] px-2.5 py-1 rounded-xl border border-slate-300 bg-slate-50/50">
                                            <option value="requested" {{ $item->status === 'requested' ? 'selected' : '' }}>Requested</option>
                                            <option value="approved" {{ $item->status === 'approved' ? 'selected' : '' }}>Approve</option>
                                            <option value="issued" {{ $item->status === 'issued' ? 'selected' : '' }}>Issue Out</option>
                                            <option value="returned" {{ $item->status === 'returned' ? 'selected' : '' }}>Mark Returned</option>
                                            <option value="rejected" {{ $item->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                        </select>
                                    </form>
                                @else
                                    <span class="text-slate-400 text-[11px]">View Only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#DC2626] flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">No Equipment Requests Logged</h4>
                                    <p class="text-xs text-slate-400">Request sound systems, wireless microphones, projectors, and instruments for events.</p>
                                    @if(!auth()->user()->isCampusLifeSrc())
                                        <button type="button" onclick="document.getElementById('equipmentModal').classList.toggle('hidden')" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold shadow-xs cursor-pointer">
                                            <span>+ Request Equipment</span>
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
            <div class="p-4 border-t border-slate-200">
                {{ $equipmentRequests->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Equipment Loan Request Modal -->
<div id="equipmentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-slate-200 overflow-hidden card-elevation">
        <div class="bg-[#DC2626] p-5 text-white flex items-center justify-between border-b border-white/10">
            <div>
                <span class="text-[10px] uppercase font-bold text-rose-200 tracking-wider">Sound & Media Department</span>
                <h3 class="text-base font-bold text-white mt-0.5">Request Equipment Loan</h3>
            </div>
            <button type="button" onclick="document.getElementById('equipmentModal').classList.add('hidden')" class="text-white/80 hover:text-white p-1 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('equipment.store') }}" class="p-6 space-y-4 text-xs">
            @csrf

            @if(auth()->user()->isOffice())
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Requesting Club</label>
                    <select name="club_id" class="w-full text-xs p-3 rounded-xl border border-slate-300 bg-slate-50/50">
                        @foreach($clubs as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </div>
            @else
                <input type="hidden" name="club_id" value="{{ auth()->user()->club_id }}">
            @endif

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Equipment Item Needed <span class="text-rose-600">*</span></label>
                <select name="item_name" required class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
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
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Quantity <span class="text-rose-600">*</span></label>
                    <input type="number" name="quantity" value="1" min="1" required class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Date Needed <span class="text-rose-600">*</span></label>
                    <input type="date" name="needed_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Expected Return Date</label>
                <input type="date" name="return_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Purpose / Service <span class="text-rose-600">*</span></label>
                <textarea name="purpose" rows="2" required placeholder="Explain where and for what service this item will be utilized..." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('equipmentModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold hover:bg-slate-200 transition">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#DC2626] to-[#B91C1C] hover:brightness-110 text-white text-xs font-bold shadow-md active:scale-95">Submit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
