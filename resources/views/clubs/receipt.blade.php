<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#DC2626">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Charter Application Receipt | Kwang-Lim Chapel - Africa University</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-cinzel { font-family: 'Cinzel', Georgia, serif; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between p-4 sm:p-6 bg-[#F8FAFC] text-slate-800 relative selection:bg-[#DC2626] selection:text-white">

    <!-- Ambient Subtle Glow Background -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden select-none">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-red-100/40 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-slate-100 rounded-full blur-3xl"></div>
    </div>

    <!-- Main Receipt Container -->
    <div class="max-w-xl w-full mx-auto my-auto relative z-10 py-8 space-y-6">
        
        <!-- Header & AU Crest -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#DC2626] p-2.5 shadow-md shadow-red-200">
                <svg class="w-full h-full text-white" viewBox="0 0 100 100" fill="currentColor">
                    <rect x="44" y="12" width="12" height="76" rx="3" fill="#FFFFFF" />
                    <rect x="22" y="32" width="56" height="12" rx="3" fill="#FFFFFF" />
                    <circle cx="50" cy="38" r="16" fill="none" stroke="#FFFFFF" stroke-width="2.5" opacity="0.6" stroke-dasharray="5 3"/>
                </svg>
            </div>
            <div>
                <div class="text-xs uppercase tracking-widest text-[#DC2626] font-bold">
                    Africa University Chaplaincy
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mt-1">
                    Charter Application Receipt
                </h1>
            </div>
        </div>

        <!-- Receipt Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 text-slate-800 shadow-2xl border border-white/20 space-y-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-2 bg-[#DC2626]"></div>

            <!-- Success Pill -->
            <div class="flex items-center space-x-3 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900">
                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div class="font-bold text-xs uppercase tracking-wider text-emerald-950">Application Safely Lodged</div>
                    <div class="text-[11px] text-emerald-800">Your charter request has been registered in the Chaplaincy staging vault.</div>
                </div>
            </div>

            <!-- Tracking Code Box -->
            <div class="p-4 rounded-2xl bg-slate-900 text-white text-center space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-widest text-rose-200">Official Tracking Reference</span>
                <div class="text-2xl font-mono font-black tracking-wider text-white select-all">
                    {{ $club->tracking_code ?? session('tracking_code', 'AU-CHTR-' . strtoupper(Str::random(6))) }}
                </div>
                <p class="text-[10px] text-slate-400">Keep this reference code for Chaplaincy Office verification and status inquiries.</p>
            </div>

            <!-- Application Summary -->
            <div class="space-y-3 divide-y divide-slate-100 text-xs">
                <div class="pt-2 flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Fellowship Name</span>
                    <span class="font-bold text-slate-900 text-right">{{ $club->name ?? 'Student Fellowship' }}</span>
                </div>
                <div class="pt-2 flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Acronym / Code</span>
                    <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded">{{ $club->code ?? 'N/A' }}</span>
                </div>
                <div class="pt-2 flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Founding President</span>
                    <span class="font-bold text-slate-900">{{ $club->founding_president_name ?? 'Applicant' }}</span>
                </div>
                <div class="pt-2 flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Student / Staff ID</span>
                    <span class="font-mono text-slate-800">{{ $club->student_id ?? 'Verified' }}</span>
                </div>
                <div class="pt-2 flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Current Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-300">
                        &bull; Pending Chaplaincy Review
                    </span>
                </div>
            </div>

            <!-- Security Quarantine Notice -->
            <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-amber-950 text-xs space-y-1.5">
                <div class="font-bold flex items-center space-x-1.5 text-amber-900">
                    <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Security & Quarantine Policy (Chapel Manual §2.3)</span>
                </div>
                <p class="text-[11px] text-amber-900/90 leading-relaxed">
                    Per Africa University Chaplaincy regulations, this application is in secure quarantine. No administrative portal logins, Master Calendar reservations, or sanctuary access will be granted until the University Chaplain and Chapel Committee review your draft constitution and ratify the charter.
                </p>
            </div>

            <!-- Return Action -->
            <div class="pt-2">
                <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center space-x-2 py-3 px-4 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white font-bold text-xs uppercase tracking-wider transition shadow-sm active:scale-95">
                    <span>Return to Kwang-Lim Portal Sign In</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-[11px] text-slate-400 space-y-1">
            <p>Kwang-Lim Chapel &bull; Fellowship Registry</p>
            <p class="text-[10px]">For questions regarding your charter application, contact the Chaplaincy Office.</p>
        </div>

    </div>

</body>
</html>
