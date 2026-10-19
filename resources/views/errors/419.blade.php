<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session Expired | Kwang-Lim Chapel - Africa University</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background-color: #F8FAFC; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-center items-center p-4 sm:p-6 bg-[#F8FAFC] text-slate-800 relative">
    
    <div class="max-w-md w-full text-center space-y-6 relative z-10 bg-white border border-slate-200/80 p-8 rounded-3xl shadow-sm">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-[#DC2626] flex items-center justify-center text-white shadow-md shadow-red-200">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>

        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-[#DC2626]">Africa University Chaplaincy</span>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Session Expired</h1>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                For security reasons, your session has timed out. Please sign in again to continue.
            </p>
        </div>

        <div class="pt-2">
            <a href="{{ route('login') }}" class="inline-flex items-center space-x-2 px-6 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider shadow-sm transition active:scale-95">
                <span>Sign In Again</span>
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <div class="text-[11px] text-slate-400 pt-2">
            Redirecting automatically in <span id="countdown" class="font-bold text-slate-700">3</span> seconds...
        </div>
    </div>

    <script>
        let seconds = 3;
        const countdownEl = document.getElementById('countdown');
        const interval = setInterval(() => {
            seconds--;
            if (countdownEl) countdownEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = "{{ route('login') }}";
            }
        }, 1000);
    </script>
</body>
</html>
