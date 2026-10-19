<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found (404) | Kwang-Lim Chapel - Africa University</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background-color: #F8FAFC; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-center items-center p-4 sm:p-6 bg-[#F8FAFC] text-slate-800 relative">
    
    <div class="max-w-lg w-full text-center space-y-6 relative z-10 bg-white border border-slate-200/80 p-8 rounded-3xl shadow-sm">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-[#DC2626] flex items-center justify-center text-white shadow-md shadow-red-200">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>

        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-[#DC2626]">Africa University Chaplaincy</span>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">404 &bull; Page Not Found</h1>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                The page or link you were looking for doesn't exist or has moved. Choose a destination below to continue.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-2 text-xs text-left pt-2">
            <a href="{{ route('dashboard') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/70 flex items-center space-x-2 transition">
                <span>📊</span>
                <span class="font-semibold text-slate-900">Dashboard</span>
            </a>
            <a href="{{ route('calendar.index') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/70 flex items-center space-x-2 transition">
                <span>📅</span>
                <span class="font-semibold text-slate-900">Master Calendar</span>
            </a>
            <a href="{{ route('requests.index') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/70 flex items-center space-x-2 transition">
                <span>📝</span>
                <span class="font-semibold text-slate-900">Proposals</span>
            </a>
            <a href="{{ route('guidelines.index') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/70 flex items-center space-x-2 transition">
                <span>📖</span>
                <span class="font-semibold text-slate-900">Guidelines</span>
            </a>
        </div>

        <div class="pt-2">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center space-x-2 px-6 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider shadow-sm transition active:scale-95">
                <span>Return to Dashboard</span>
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>

</body>
</html>
