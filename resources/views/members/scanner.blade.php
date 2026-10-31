<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>In-Browser Camera Scanner - {{ $club->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0F172A; }
        .font-cinzel { font-family: 'Cinzel', Georgia, serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        #video-container { position: relative; width: 100%; max-width: 400px; aspect-ratio: 1; border-radius: 1.5rem; overflow: hidden; background: #000; margin: 0 auto; }
        #video { width: 100%; height: 100%; object-fit: cover; }
        .scanner-laser { position: absolute; top: 0; left: 0; width: 100%; height: 2px; background: #DC2626; box-shadow: 0 0 10px #DC2626; animation: scan 2s infinite ease-in-out; }
        @keyframes scan { 0% { top: 5%; } 50% { top: 95%; } 100% { top: 5%; } }
    </style>
</head>
<body class="min-h-screen text-slate-100 p-4 sm:p-6 flex flex-col items-center justify-between">

    <!-- Top Header -->
    <div class="max-w-md w-full flex items-center justify-between pb-3 border-b border-white/10">
        <a href="{{ route('members.index', ['club_id' => $club->id]) }}" class="text-xs text-rose-200 hover:underline flex items-center space-x-1 font-semibold">
            <span>&larr; Back to Member Roster</span>
        </a>
        <div class="text-right">
            <span class="text-xs font-bold text-white block">{{ $club->name }}</span>
            <span class="text-[10px] text-slate-400 font-mono">{{ date('M d, Y') }}</span>
        </div>
    </div>

    <!-- Main Scanner Interface -->
    <div class="max-w-md w-full space-y-4 my-4">
        
        <!-- Camera Viewfinder -->
        <div id="video-container" class="border-2 border-[#DC2626] shadow-2xl relative">
            <video id="video" autoplay playsinline muted></video>
            <div class="scanner-laser"></div>
            
            <!-- Corner target markers -->
            <div class="absolute inset-4 border-2 border-dashed border-white/40 rounded-2xl pointer-events-none flex items-center justify-center">
                <span class="text-[10px] uppercase tracking-widest text-white font-mono bg-black/60 px-3 py-1 rounded-full border border-white/20">
                    Aim at Student QR or ID
                </span>
            </div>

            <!-- Fallback overlay if camera disabled -->
            <div id="cameraFallback" class="hidden absolute inset-0 bg-slate-900/95 flex flex-col items-center justify-center p-6 text-center space-y-2">
                <svg class="w-10 h-10 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span class="text-xs font-bold text-white">Browser Camera Inactive</span>
                <p class="text-[11px] text-slate-400">Use rapid check-in below or allow camera permissions in browser settings.</p>
            </div>
        </div>

        <!-- Rapid Check-in / Barcode Scanner input box -->
        <div class="bg-white text-slate-900 rounded-3xl p-5 shadow-xl border border-slate-200/80 space-y-2">
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
                Quick Student ID / Name / Barcode Entry
            </label>
            <form onsubmit="handleManualScan(event)" class="flex space-x-2">
                <input type="text" id="manualInput" autofocus placeholder="Scan barcode or type name/ID..." class="flex-1 text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]">
                <button type="submit" class="px-5 py-3 bg-[#DC2626] hover:bg-[#B91C1C] text-white font-bold text-xs rounded-xl transition shadow">
                    Check In
                </button>
            </form>
        </div>

        <!-- Live Scan Feedback Toast -->
        <div id="scanFeedback" class="hidden p-3.5 bg-emerald-600 text-white rounded-2xl shadow-lg flex items-center justify-between text-xs font-bold">
            <span id="feedbackText">Checked in!</span>
            <span id="headcountCount" class="font-mono bg-white/20 px-2.5 py-0.5 rounded-full text-[10px]"></span>
        </div>

        <!-- Member Quick Click List -->
        <div class="bg-white/5 rounded-2xl p-4 border border-white/10 space-y-2 text-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">1-Tap Fast Check-In:</span>
            <div class="flex flex-wrap gap-1.5 max-h-36 overflow-y-auto">
                @foreach($members as $m)
                    <button type="button" data-name="{{ $m->full_name }}" onclick="quickCheckin(this.dataset.name)" class="px-3 py-1.5 bg-white/10 hover:bg-[#DC2626] hover:text-white rounded-xl text-[11px] text-slate-200 transition font-medium border border-white/10">
                        + {{ $m->full_name }}
                    </button>
                @endforeach
            </div>
        </div>

    </div>

    <div class="text-[11px] text-slate-400 text-center pb-2">
        In-Browser HTML5 Camera & Scanner &bull; Zero App Install Required &bull; Africa University Kwang-Lim Chapel
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
