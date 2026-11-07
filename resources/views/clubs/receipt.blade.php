<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#DC2626">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Charter Application Receipt | Kwang-Lim Chapel - Africa University</title>

    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            @page { margin: 14mm; }
            html, body { background: #fff !important; }
            .receipt-card { box-shadow: none !important; border-color: #D5CEC3 !important; }
            .receipt-ink { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .receipt-shell { padding-top: 0 !important; }
        }
    </style>
</head>
<body class="min-h-full bg-paper text-slate-800 antialiased selection:bg-brand selection:text-white">

    <!-- Main Receipt Container -->
    <main class="receipt-shell max-w-xl w-full mx-auto px-4 sm:px-6 py-10 sm:py-14 space-y-7">

        <!-- Header & Crest -->
        <header class="text-center">
            <span class="inline-grid place-items-center w-14 h-14 rounded-full"
                  style="background: radial-gradient(circle at 35% 30%, #EF4444, var(--color-brand-deep)); box-shadow: inset 0 0 0 1px rgb(255 255 255 / .14), 0 0 0 3px var(--color-paper), 0 0 0 4px rgb(184 137 43 / .5), 0 10px 22px -8px rgb(220 38 38 / .6);">
                <x-umc-logo class="w-[1.4rem] h-[1.4rem]" />
            </span>
            <p class="mt-5 text-[10.5px] font-bold uppercase tracking-[0.22em] text-brand">Africa University Chaplaincy</p>
            <h1 class="page-title mt-2 !text-[2rem] sm:!text-[2.4rem]">Charter Application Receipt</h1>
        </header>

        <!-- Receipt Card -->
        <section class="receipt-card card overflow-hidden">
            <div class="h-1 bg-gradient-to-r from-brand via-brand to-gold" aria-hidden="true"></div>

            <div class="p-6 sm:p-8 space-y-6">
                <!-- Lodged confirmation -->
                <div class="flex items-start gap-3.5">
                    <span class="w-9 h-9 rounded-full grid place-items-center shrink-0 bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20" aria-hidden="true">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <div>
                        <p class="text-[13.5px] font-semibold text-slate-900">Application Safely Lodged</p>
                        <p class="mt-0.5 text-[12.5px] text-slate-500">Your charter request has been registered in the Chaplaincy staging vault.</p>
                    </div>
                </div>

                <!-- Tracking Code Box -->
                <div class="receipt-ink relative grain on-dark rounded-2xl bg-ink text-white text-center px-5 py-6 overflow-hidden"
                     style="background-image: radial-gradient(90% 120% at 50% 0%, rgb(184 137 43 / 0.16), transparent 65%);">
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-gold-light">Official Tracking Reference</p>
                    <p class="mt-2 font-mono text-[1.6rem] sm:text-[1.85rem] font-semibold tracking-[0.08em] text-white select-all break-all">{{ $club->tracking_code ?? session('tracking_code', 'AU-CHTR-' . strtoupper(Str::random(6))) }}</p>
                    <p class="mt-2 text-[11.5px] text-white/50">Keep this reference code for Chaplaincy Office verification and status inquiries.</p>
                </div>

                <!-- Application Summary -->
                <dl class="divide-y divide-slate-100 text-[13px]">
                    <div class="py-2.5 flex justify-between items-center gap-4">
                        <dt class="text-slate-500">Fellowship Name</dt>
                        <dd class="font-display text-[15px] text-slate-900 text-right">{{ $club->name ?? 'Student Fellowship' }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between items-center gap-4">
                        <dt class="text-slate-500">Acronym / Code</dt>
                        <dd class="font-mono text-[12px] font-semibold text-slate-800">{{ $club->code ?? 'N/A' }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between items-center gap-4">
                        <dt class="text-slate-500">Founding President</dt>
                        <dd class="font-semibold text-slate-900 text-right">{{ $club->founding_president_name ?? 'Applicant' }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between items-center gap-4">
                        <dt class="text-slate-500">Student / Staff ID</dt>
                        <dd class="font-mono text-[12px] text-slate-800">{{ $club->student_id ?? 'Verified' }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between items-center gap-4">
                        <dt class="text-slate-500">Current Status</dt>
                        <dd><span class="badge badge-amber badge-dot">Pending Chaplaincy Review</span></dd>
                    </div>
                </dl>

                <!-- Security Quarantine Notice -->
                <div class="rounded-2xl bg-paper ring-1 ring-black/5 p-4 sm:p-5">
                    <p class="flex items-center gap-2 text-[12.5px] font-semibold text-slate-900">
                        <svg class="w-4 h-4 text-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Security &amp; Quarantine Policy (Chapel Manual §2.3)
                    </p>
                    <p class="mt-2 text-[12px] text-slate-600 leading-relaxed">
                        Per Africa University Chaplaincy regulations, this application is in secure quarantine. No administrative portal logins, Master Calendar reservations, or sanctuary access will be granted until the University Chaplain and Chapel Committee review your draft constitution and ratify the charter.
                    </p>
                </div>

                <!-- Return Action -->
                <div class="no-print flex flex-col sm:flex-row gap-2 pt-1">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg flex-1">
                        Return to Kwang-Lim Portal Sign In
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <button type="button" onclick="window.print()" class="btn btn-secondary btn-lg">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print
                    </button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="text-center space-y-1.5">
            <p class="text-[12px] font-medium text-slate-500">Kwang-Lim Chapel &bull; Fellowship Registry</p>
            <p class="text-[11.5px] text-slate-400">For questions regarding your charter application, contact the Chaplaincy Office.</p>
        </footer>

    </main>

</body>
</html>
