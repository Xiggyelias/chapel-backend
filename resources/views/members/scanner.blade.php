<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#15121A">
    <title>In-Browser Camera Scanner - {{ $club->name }}</title>
    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        #video-container { position: relative; width: 100%; max-width: 420px; aspect-ratio: 1; border-radius: 1.75rem; overflow: hidden; background: #000; margin: 0 auto; }
        #video { width: 100%; height: 100%; object-fit: cover; }
        .scanner-laser { position: absolute; left: 8%; width: 84%; top: 5%; height: 1.5px; border-radius: 9999px; background: linear-gradient(90deg, transparent, #F1D58F, transparent); box-shadow: 0 0 14px rgb(241 213 143 / .7); animation: scan 2.6s infinite ease-in-out; }
        @keyframes scan { 0% { top: 8%; } 50% { top: 92%; } 100% { top: 8%; } }
        .viewfinder-corner { position: absolute; width: 2.25rem; height: 2.25rem; border-color: #D9B76C; pointer-events: none; }
        .scanner-light { background: radial-gradient(60% 40% at 50% 30%, rgb(217 183 108 / .10), transparent 70%), radial-gradient(50% 40% at 0% 0%, rgb(220 38 38 / .16), transparent 70%); }
    </style>
</head>
<body class="min-h-full bg-ink text-white antialiased">
<div class="relative min-h-screen grain on-dark flex flex-col items-center overflow-hidden px-4 sm:px-6">
    <div class="scanner-light absolute inset-0 pointer-events-none" aria-hidden="true"></div>

    {{-- Top bar --}}
    <header class="relative z-10 max-w-md w-full pt-5 pb-4 flex items-center justify-between gap-4 border-b border-white/[0.08]">
        <a href="{{ route('members.index', ['club_id' => $club->id]) }}" class="inline-flex items-center gap-2 text-[12.5px] font-semibold text-white/60 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.5 19.5L3 12l7.5-7.5M3 12h18"/></svg>
            Back to Member Roster
        </a>
        <div class="text-right min-w-0">
            <span class="font-display text-[15px] text-white block truncate">{{ $club->name }}</span>
            <span class="text-[11px] text-white/45 tabular-nums">{{ date('M d, Y') }}</span>
        </div>
    </header>

    {{-- Scanner --}}
    <main class="relative z-10 max-w-md w-full flex-1 space-y-5 py-6">

        <div class="text-center">
            <p class="text-[11px] font-bold uppercase tracking-[0.26em] text-gold-light/90">Camera scanner</p>
            <h1 class="mt-2 font-display text-[2rem] sm:text-[2.4rem] leading-[1.05] tracking-[-0.025em] text-white" style="font-variation-settings: 'SOFT' 100, 'opsz' 144;">Hold the code to the light</h1>
        </div>

        {{-- Viewfinder --}}
        <div id="video-container" class="relative ring-1 ring-gold-light/30" style="box-shadow: 0 0 0 6px rgb(255 255 255 / .03), 0 40px 80px -30px rgb(0 0 0 / .9);">
            <video id="video" autoplay playsinline muted></video>
            <div class="scanner-laser" aria-hidden="true"></div>

            <span class="viewfinder-corner top-5 left-5 border-t-2 border-l-2 rounded-tl-xl" aria-hidden="true"></span>
            <span class="viewfinder-corner top-5 right-5 border-t-2 border-r-2 rounded-tr-xl" aria-hidden="true"></span>
            <span class="viewfinder-corner bottom-5 left-5 border-b-2 border-l-2 rounded-bl-xl" aria-hidden="true"></span>
            <span class="viewfinder-corner bottom-5 right-5 border-b-2 border-r-2 rounded-br-xl" aria-hidden="true"></span>

            <div class="absolute inset-x-0 bottom-9 flex justify-center pointer-events-none">
                <span class="text-[10.5px] font-semibold uppercase tracking-[0.2em] text-white/85 bg-black/55 backdrop-blur-sm px-3.5 py-1.5 rounded-full ring-1 ring-white/15">
                    Aim at Student QR or ID
                </span>
            </div>

            {{-- Fallback if the camera is unavailable --}}
            <div id="cameraFallback" class="hidden absolute inset-0 bg-ink/95 flex flex-col items-center justify-center p-8 text-center">
                <span class="grid place-items-center w-14 h-14 rounded-full bg-white/[0.05] ring-1 ring-gold-light/30 text-gold-light mb-4" aria-hidden="true">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </span>
                <span class="font-display text-[1.35rem] text-white">Browser Camera Inactive</span>
                <p class="mt-1.5 text-[13px] leading-relaxed text-white/55 max-w-[16rem]">Use rapid check-in below or allow camera permissions in browser settings.</p>
            </div>
        </div>

        {{-- Live scan feedback --}}
        <div id="scanFeedback" role="status" aria-live="polite" class="hidden px-4 py-3.5 rounded-2xl bg-emerald-500/15 ring-1 ring-emerald-300/30 text-emerald-100 items-center justify-between gap-3 text-[14px] font-semibold modal-panel [&:not(.hidden)]:flex">
            <span class="flex items-center gap-2.5 min-w-0">
                <svg class="w-5 h-5 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 13l4 4L19 7"/></svg>
                <span id="feedbackText">Checked in!</span>
            </span>
            <span id="headcountCount" class="tabular-nums bg-white/10 px-2.5 py-0.5 rounded-full text-[11px] shrink-0"></span>
        </div>

        {{-- Rapid entry --}}
        <div class="card p-5">
            <label for="manualInput" class="field-label">Quick Student ID / Name / Barcode Entry</label>
            <form onsubmit="handleManualScan(event)" class="flex gap-2">
                <input type="text" id="manualInput" autofocus placeholder="Scan barcode or type name/ID..." class="field flex-1 min-w-0 text-[14px]">
                <button type="submit" class="btn btn-primary shrink-0">Check In</button>
            </form>
        </div>

        {{-- One-tap list --}}
        <div class="rounded-2xl bg-white/[0.035] ring-1 ring-white/10 p-4">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/50 mb-3">1-Tap Fast Check-In:</p>
            <div class="flex flex-wrap gap-1.5 max-h-40 overflow-y-auto">
                @foreach($members as $m)
                    <button type="button" data-name="{{ $m->full_name }}" onclick="quickCheckin(this.dataset.name)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/[0.06] hover:bg-brand ring-1 ring-white/10 hover:ring-brand text-[12.5px] font-medium text-white/80 hover:text-white transition cursor-pointer">
                        <span class="text-gold-light/80" aria-hidden="true">+</span> {{ $m->full_name }}
                    </button>
                @endforeach
            </div>
        </div>

    </main>

    <footer class="relative z-10 pb-6 text-center text-[11px] text-white/35 max-w-md">
        In-Browser HTML5 Camera &amp; Scanner &bull; Zero App Install Required &bull; Africa University Kwang-Lim Chapel
    </footer>
</div>

    <script>
        const video = document.getElementById('video');
        const fallback = document.getElementById('cameraFallback');

        // Start Camera stream
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => {
                    fallback.classList.remove('hidden');
                });
        } else {
            fallback.classList.remove('hidden');
        }

        async function postAttendance(input) {
            if (!input) return;
            try {
                const res = await fetch("{{ route('api.members.qr_checkin') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        club_id: {{ $club->id }},
                        student_id_or_email: input,
                        meeting_date: '{{ $today }}'
                    })
                });
                const data = await res.json();
                if (data.success) {
                    showFeedback(data.message, data.total_present);
                }
            } catch (e) {
                alert('Connection error');
            }
        }

        function handleManualScan(e) {
            e.preventDefault();
            const input = document.getElementById('manualInput');
            const val = input.value.trim();
            if (val) {
                postAttendance(val);
                input.value = '';
            }
        }

        function quickCheckin(name) {
            postAttendance(name);
        }

        function showFeedback(text, total) {
            const fb = document.getElementById('scanFeedback');
            document.getElementById('feedbackText').textContent = text;
            document.getElementById('headcountCount').textContent = `Total: ${total}`;
            fb.classList.remove('hidden');
            setTimeout(() => {
                fb.classList.add('hidden');
            }, 3000);
        }
    </script>

</body>
</html>
