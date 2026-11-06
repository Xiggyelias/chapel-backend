<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found | Kwang-Lim Chapel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,600&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --color-brand: #A51C30; --color-ink: #12131A; --color-paper: #F7F6F3; }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: var(--color-paper); }
        .font-display { font-family: 'Fraunces', Georgia, serif; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6 antialiased text-slate-800 selection:bg-brand selection:text-white">

    <div class="max-w-md w-full text-center space-y-8">

        {{-- Error code --}}
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-brand mb-2">Africa University · Kwang-Lim Chapel</p>
            <div class="text-8xl font-display font-light text-slate-900 leading-none">404</div>
            <h1 class="text-xl font-semibold text-slate-700 mt-2">Page not found</h1>
            <p class="text-sm text-slate-400 mt-2 leading-relaxed">
                The page you were looking for doesn't exist or has been moved.
            </p>
        </div>

        {{-- Quick links --}}
        <div class="grid grid-cols-2 gap-2 text-left">
            @foreach([
                [route('dashboard'),       'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'Dashboard'],
                [route('calendar.index'),  'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',                                                              'Calendar'],
                [route('requests.index'),  'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',               'Proposals'],
                [route('hymns.index'),     'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3', 'Hymnary'],
            ] as [$url, $icon, $label])
                <a href="{{ $url }}" class="flex items-center gap-2.5 p-3.5 bg-white rounded-xl border border-slate-200/80 hover:border-brand/30 hover:shadow-sm transition group">
                    <div class="w-8 h-8 rounded-lg bg-brand/8 group-hover:bg-brand/15 flex items-center justify-center transition">
                        <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">{{ $label }}</span>
                </a>
            @endforeach
        </div>

        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand hover:bg-brand-dark text-white text-sm font-bold rounded-xl shadow-sm transition">
            Back to dashboard
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>
    </div>

</body>
</html>
