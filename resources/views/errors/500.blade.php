<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error (500) | Kwang-Lim Chapel - Africa University</title>
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
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>

        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-[#DC2626]">Africa University Chaplaincy</span>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Something Went Wrong</h1>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                An unexpected error occurred while processing your request. Please return to the dashboard or try again in a few moments.
            </p>
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
