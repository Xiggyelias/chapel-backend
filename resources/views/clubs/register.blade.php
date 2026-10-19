@extends('layouts.app')

@section('title', 'Register a Fellowship or Chapel Club')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:underline text-[#DC2626] font-semibold">Dashboard</a>
                <span>/</span>
                <a href="{{ route('clubs.index') }}" class="hover:underline text-[#DC2626] font-semibold">Fellowships Registry</a>
                <span>/</span>
                <span class="text-slate-700 font-bold">New Fellowship Charter</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                Register a Fellowship or Chapel Ministry
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Apply for official chartering and portal access under Chaplaincy oversight (Chapel Manual §2.3)
            </p>
        </div>
        <a href="{{ route('clubs.index') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 shadow-xs transition self-start sm:self-auto hover:border-slate-300">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Registry</span>
        </a>
    </div>

    <!-- Institutional Guidelines Protocol Card -->
    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 shadow-xs relative overflow-hidden">
        <div class="flex items-start space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#DC2626] border border-white/20 flex items-center justify-center shrink-0 shadow-sm text-white">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div class="space-y-1.5 flex-1">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-[#DC2626]">
                        Fellowship Chartering Protocol
                    </h2>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-[#DC2626]/10 text-[#DC2626] font-bold border border-[#DC2626]/20">
                        Chapel Manual §2.3
                    </span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    All student religious fellowships, choir groups, and ministry societies must maintain active chartering through the Chaplaincy Office. Newly registered groups receive permanent records vault storage, master calendar booking rights, and official representation on the Chapel Committee.
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-4 text-[11px] text-slate-700">
                    <span class="flex items-center space-x-1.5 font-medium text-emerald-800">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span>Permanent Vault Retention</span>
                    </span>
                    <span class="flex items-center space-x-1.5 font-medium text-emerald-800">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span>Sanctuary & Venue Access</span>
                    </span>
                    <span class="flex items-center space-x-1.5 font-medium text-emerald-800">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span>Chapel Committee Seat</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-xs space-y-1">
            <div class="font-bold flex items-center space-x-1.5 text-rose-950">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-800">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Registration Form -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 overflow-hidden relative border-t-4 border-t-[#DC2626]">
        <form method="POST" action="{{ route('clubs.store') }}" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf

            <!-- Section 1: Fellowship Identity -->
            <div class="space-y-4">
                <div class="border-b border-slate-100 pb-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#DC2626] flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                        <span>1. Fellowship Identity & Categorization</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Official university name, classification, and ministry scope</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2 space-y-1">
                        <label class="block font-bold text-slate-700">Official Fellowship Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Anglican Student Society" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/15 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Acronym / Short Code <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g. ANGLICAN" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/15 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white font-mono uppercase text-xs transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Ministry Classification <span class="text-rose-500">*</span></label>
                        <select name="type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/15 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                            <option value="">-- Select Category --</option>
                            <option value="fellowship" {{ old('type') == 'fellowship' ? 'selected' : '' }}>Campus Fellowship / Denominational Movement</option>
                            <option value="prayer group" {{ old('type') == 'prayer group' ? 'selected' : '' }}>Intercessory Prayer & Revival Group</option>
                            <option value="choir or worship team" {{ old('type') == 'choir or worship team' ? 'selected' : '' }}>Choral Ensemble or Worship Team</option>
                            <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Humanitarian, Volunteer & Service Ministry</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Official Club Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="official_email" value="{{ old('official_email') }}" required placeholder="e.g. anglican@africau.edu" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/15 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Purpose & Statement of Ministry <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="3" required placeholder="Detail the core biblical mission, aims, and anticipated impact of this fellowship within the Kwang-Lim Chapel community..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/15 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Section 2: Meeting Schedule & Venue -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div class="border-b border-slate-100 pb-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#DC2626] flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                        <span>2. Proposed Schedule & Preferred Campus Venue</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Regular campus gathering hours subject to Chapel Assistant coordination</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Proposed Meeting Schedule <span class="text-rose-500">*</span></label>
                        <input type="text" name="meeting_schedule" value="{{ old('meeting_schedule') }}" required placeholder="e.g. Mondays 17:30 - 19:00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/15 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Preferred Campus Venue <span class="text-rose-500">*</span></label>
                        <select name="venue" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/15 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                            <option value="">-- Select Preferred Venue --</option>
                            @foreach($venues as $v)
                                <option value="{{ $v->name }}" {{ old('venue') == $v->name ? 'selected' : '' }}>
                                    {{ $v->name }} (Capacity: {{ $v->capacity }})
                                </option>
                            @endforeach
                            <option value="Old Chapel Room" {{ old('venue') == 'Old Chapel Room' ? 'selected' : '' }}>Old Chapel Room</option>
                            <option value="Prayer Garden" {{ old('venue') == 'Prayer Garden' ? 'selected' : '' }}>Prayer Garden</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Executive Leadership & Governance -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div class="border-b border-slate-100 pb-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#DC2626] flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                        <span>3. Founding Executive Leadership & Constitution</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Contact information for initial portal provision and permanent vault archive</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Founding President Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="founding_president_name" value="{{ old('founding_president_name') }}" required placeholder="e.g. Farai Makoni" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/15 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">President Official Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="founding_president_email" value="{{ old('founding_president_email') }}" required placeholder="e.g. fmakoni@africau.edu" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/15 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">WhatsApp / Phone Contact <span class="text-rose-500">*</span></label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" required placeholder="e.g. +263 77 123 4567" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/15 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">
                        Draft Constitution / Bye-Laws (PDF or DOC)
                    </label>
                    <input type="file" name="constitution" accept=".pdf,.doc,.docx" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/15 focus:border-[#DC2626] text-slate-700 bg-slate-50 text-xs transition">
                    <p class="text-[10px] text-slate-500">Optional during application. An approved constitution is required for complete Vault record compliance.</p>
                </div>
            </div>

            <!-- Submission Actions -->
            <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-[11px] text-slate-500 italic">
                    By submitting, the executive leadership agrees to adhere to the Chapel Manual and ministry guidelines.
                </span>
                <div class="flex items-center space-x-2.5 w-full sm:w-auto">
                    <a href="{{ route('clubs.index') }}" class="w-1/2 sm:w-auto text-center px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition active:scale-95">Cancel</a>
                    <button type="submit" class="w-1/2 sm:w-auto px-6 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] active:scale-95 text-white font-bold tracking-wide transition shadow-sm">
                        Submit Charter Application
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
