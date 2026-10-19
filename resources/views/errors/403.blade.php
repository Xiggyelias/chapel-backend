<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Restricted (403) | Kwang-Lim Chapel - Africa University</title>
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
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>

        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-[#DC2626]">Africa University Chaplaincy</span>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Access Restricted</h1>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                Your currently active role does not have permission to access this internal section. Please use your tailored dashboard navigation.
            </p>
        </div>

        <div class="flex items-center justify-center space-x-2 pt-2">
            <a href="{{ route('calendar.index') }}" class="inline-flex items-center space-x-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                <span>Master Calendar</span>
            </a>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center space-x-1.5 px-5 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider transition shadow-sm active:scale-95">
                <span>Return to Dashboard</span>
            </a>
        </div>
    </div>

</body>
</html>
