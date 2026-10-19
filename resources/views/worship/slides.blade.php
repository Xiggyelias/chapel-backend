<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Media Projection Slides - {{ $sheet->theme ?: 'Chapel Worship' }} - Africa University</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-cinzel { font-family: 'Cinzel', Georgia, serif; }
        .slide { display: none; opacity: 0; transition: opacity 0.3s ease-in-out; }
        .slide.active { display: flex; opacity: 1; }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-[#120306] via-[#1E050B] to-[#2D0911] text-white flex flex-col justify-between overflow-hidden select-none">

    <!-- Top Projector Bar -->
    <header class="p-4 sm:p-6 flex items-center justify-between border-b border-white/10 z-20 backdrop-blur-md bg-black/20">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#DC2626] to-[#991B1B] border border-white/20 flex items-center justify-center p-1.5 shadow-md">
                <x-umc-logo class="w-full h-full" />
            </div>
            <div>
                <span class="font-cinzel text-xs uppercase tracking-widest text-rose-200 font-bold block">Africa University Chaplaincy</span>
                <span class="text-xs text-slate-300 font-mono">Kwang-Lim Chapel &bull; Liturgical Projection Console</span>
            </div>
        </div>

        <div class="flex items-center space-x-3 text-xs">
            <button onclick="toggleFullscreen()" title="Toggle Fullscreen (F)" class="px-3.5 py-1.5 bg-white/10 hover:bg-white/20 rounded-xl text-slate-200 border border-white/20 font-mono flex items-center space-x-1.5 transition">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                <span class="hidden sm:inline">Fullscreen [F]</span>
            </button>
            <a href="{{ route('worship.show', $sheet->id) }}" class="px-3.5 py-1.5 bg-rose-900/70 hover:bg-rose-900 text-rose-200 rounded-xl font-bold border border-rose-700/50 transition">
                Exit Slides &times;
            </a>
        </div>
    </header>

    <!-- Slides Container -->
    <main id="slideDeck" class="flex-1 relative flex items-center justify-center p-6 sm:p-12 overflow-hidden">
        
        <!-- SLIDE 1: Title & Theme -->
        <div class="slide active flex-col items-center justify-center text-center max-w-4xl w-full mx-auto space-y-6">
            <span class="px-5 py-2 rounded-full bg-[#DC2626]/60 border border-white/20 text-rose-200 font-cinzel text-xs sm:text-sm uppercase tracking-widest font-bold shadow-lg">
                {{ $sheet->service_type }} &bull; {{ $sheet->service_date->format('F d, Y') }}
            </span>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black font-cinzel text-white leading-tight tracking-tight drop-shadow-xl">
                {{ $sheet->theme ?: 'Worship & Fellowship' }}
            </h1>
            <div class="w-24 h-1.5 bg-[#DC2626] mx-auto rounded-full"></div>
            @if($sheet->preacher)
                <div class="space-y-1">
                    <span class="text-xs uppercase tracking-widest text-slate-400 font-bold">Preacher of the Word</span>
                    <div class="text-2xl sm:text-3xl font-bold font-cinzel text-white">{{ $sheet->preacher }}</div>
                </div>
            @endif
        </div>

        <!-- SLIDE 2: Scripture Reading -->
        <div class="slide flex-col items-center justify-center text-center max-w-4xl w-full mx-auto space-y-6">
            <span class="px-5 py-2 rounded-full bg-blue-500/20 border border-blue-400/40 text-blue-200 font-mono text-xs uppercase tracking-widest font-bold">
                Holy Scripture Reading
            </span>
            <h2 class="text-3xl sm:text-5xl font-black font-cinzel text-white leading-snug">
                {{ $sheet->scripture ?: 'Scripture to be announced' }}
            </h2>
            <div class="w-20 h-1 bg-[#DC2626] mx-auto rounded-full"></div>
            <div class="text-base sm:text-xl text-slate-200 max-w-2xl leading-relaxed italic">
                "Your word is a lamp to my feet and a light to my path." &mdash; Psalm 119:105
            </div>
            @if($sheet->bible_reader)
                <div class="text-sm text-slate-300">
                    Reader: <strong class="text-white">{{ $sheet->bible_reader }}</strong>
                </div>
            @endif
        </div>

        <!-- SLIDE 3: Liturgical Servants & Accessibility -->
        <div class="slide flex-col items-center justify-center text-center max-w-4xl w-full mx-auto space-y-6">
            <span class="px-5 py-2 rounded-full bg-purple-500/20 border border-purple-400/40 text-purple-200 font-mono text-xs uppercase tracking-widest font-bold">
                Ministers of the Service
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full max-w-2xl text-left bg-black/40 border border-white/10 p-6 sm:p-8 rounded-3xl backdrop-blur-md shadow-2xl">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Liturgist</span>
                    <span class="text-base sm:text-lg font-bold text-white">{{ $sheet->liturgist ?: 'Chaplaincy Team' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Choir / Praise</span>
                    <span class="text-base sm:text-lg font-bold text-white">{{ $sheet->choir ?: ($sheet->praise_worship_team ?: 'Praise & Worship Team') }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Bible Reader</span>
                    <span class="text-base sm:text-lg font-bold text-white">{{ $sheet->bible_reader ?: 'Student Representative' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Sign-Language Interpreter</span>
                    @if($sheet->sign_interpreter_required)
                        <span class="text-base sm:text-lg font-bold text-emerald-400 flex items-center space-x-1.5">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $sheet->sign_interpreter_name ?: 'Interpreter Present' }}</span>
                        </span>
                    @else
                        <span class="text-sm text-slate-400">Available upon request</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- SLIDES 4+: Hymns -->
        @forelse($hymnsList as $index => $hymn)
            <div class="slide flex-col items-center justify-center text-center max-w-4xl w-full mx-auto space-y-6">
                <span class="px-5 py-2 rounded-full bg-emerald-500/20 border border-emerald-400/50 text-emerald-200 font-cinzel text-xs uppercase tracking-widest font-bold">
                    Hymn of Praise {{ $index + 1 }}
                </span>
                <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black font-cinzel text-white leading-tight">
                    {{ $hymn }}
                </h2>
                <div class="w-24 h-1.5 bg-[#DC2626] mx-auto rounded-full"></div>
                <div class="p-6 bg-black/30 border border-white/10 rounded-2xl text-slate-200 text-base sm:text-xl font-medium max-w-xl mx-auto leading-relaxed">
                    Let all people praise the Lord with joyful songs and thanksgiving.
                </div>
                <div class="text-xs text-slate-400 font-mono tracking-widest uppercase">
                    &bull; Congregation Please Stand as Led &bull;
                </div>
            </div>
        @empty
            <div class="slide flex-col items-center justify-center text-center max-w-3xl w-full mx-auto space-y-4">
                <span class="text-xs font-bold uppercase text-slate-400">Worship Hymns</span>
                <h2 class="text-3xl font-bold font-cinzel text-white">Hymns of Praise</h2>
                <p class="text-slate-300">Hymns will be announced from the pulpit by the Liturgist.</p>
            </div>
        @endforelse

        <!-- SLIDES: Hymn & Praise Lyrics Projection -->
        @if($sheet->lyrics)
            @php
                $rawChunks = preg_split('/(\r\n\r\n|\n\n)+/', trim($sheet->lyrics));
                $lyricsSlides = array_values(array_filter(array_map('trim', $rawChunks)));
            @endphp
            @foreach($lyricsSlides as $lIdx => $lSection)
                <div class="slide flex-col items-center justify-center text-center max-w-4xl w-full mx-auto space-y-6">
                    <span class="px-5 py-2 rounded-full bg-rose-500/20 border border-rose-400/50 text-rose-200 font-cinzel text-xs uppercase tracking-widest font-bold">
                        Worship Lyrics &bull; Part {{ $lIdx + 1 }} of {{ count($lyricsSlides) }}
                    </span>
                    <div class="p-8 sm:p-10 bg-black/50 border border-white/10 rounded-3xl backdrop-blur-md max-w-3xl w-full mx-auto shadow-2xl">
                        <p class="text-xl sm:text-2xl lg:text-3xl font-semibold text-white leading-relaxed whitespace-pre-line font-serif">
                            {{ $lSection }}
                        </p>
                    </div>
                    <div class="text-xs text-slate-400 font-mono tracking-widest uppercase">
                        &bull; Kwang-Lim Chapel Media Desk &bull;
                    </div>
                </div>
            @endforeach
        @endif

        <!-- FINAL SLIDE: Benediction & Peace -->
        <div class="slide flex-col items-center justify-center text-center max-w-4xl w-full mx-auto space-y-6">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-[#DC2626] to-[#991B1B] border border-white/20 flex items-center justify-center p-2.5 shadow-xl">
                <x-umc-logo class="w-full h-full" />
            </div>
            <span class="text-xs sm:text-sm font-bold uppercase tracking-widest text-rose-200 font-cinzel">The Benediction</span>
            <h2 class="text-2xl sm:text-4xl font-extrabold font-cinzel text-white leading-relaxed max-w-2xl">
                "The Lord bless you and keep you; the Lord make his face shine on you and be gracious to you."
            </h2>
            <div class="text-xs text-slate-400 font-mono">Numbers 6:24-25</div>
            <div class="text-xs text-slate-300 pt-2 font-medium">
                Africa University Kwang-Lim Chapel &bull; Go forth in Peace to Love and Serve the Lord.
            </div>
        </div>

    </main>

    <!-- Bottom Projector Control Bar -->
    <footer class="p-4 sm:p-6 bg-black/40 border-t border-white/10 flex items-center justify-between text-xs z-20 backdrop-blur-md">
        <div class="flex items-center space-x-2">
            <button onclick="prevSlide()" class="px-4 py-2 bg-white/10 hover:bg-white/20 hover:text-white rounded-xl font-bold border border-white/20 transition flex items-center space-x-1">
                <span>&larr; Previous</span>
            </button>
            <button onclick="nextSlide()" class="px-4 py-2 bg-[#DC2626] hover:bg-[#B91C1C] text-white rounded-xl font-bold transition flex items-center space-x-1 shadow-sm">
                <span>Next &rarr;</span>
            </button>
        </div>

        <div class="font-mono text-slate-300 font-bold" id="slideCounter">
            Slide 1
        </div>

        <div class="text-[11px] text-slate-400 hidden sm:block">
            Controls: <kbd class="px-2 py-0.5 bg-white/10 rounded text-slate-200 font-mono">Space</kbd> / <kbd class="px-2 py-0.5 bg-white/10 rounded text-slate-200 font-mono">&larr;</kbd> <kbd class="px-2 py-0.5 bg-white/10 rounded text-slate-200 font-mono">&rarr;</kbd>
        </div>
    </footer>

    <script>
        const slides = document.querySelectorAll('.slide');
        let currentSlide = 0;

        function updateSlide() {
            slides.forEach((s, idx) => {
                s.classList.toggle('active', idx === currentSlide);
            });
            document.getElementById('slideCounter').textContent = `Slide ${currentSlide + 1} of ${slides.length}`;
        }

        function nextSlide() {
            if (currentSlide < slides.length - 1) {
                currentSlide++;
                updateSlide();
            }
        }

        function prevSlide() {
            if (currentSlide > 0) {
                currentSlide--;
                updateSlide();
            }
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        window.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight' || e.key === ' ' || e.key === 'PageDown') {
                nextSlide();
            } else if (e.key === 'ArrowLeft' || e.key === 'PageUp' || e.key === 'Backspace') {
                prevSlide();
            } else if (e.key === 'f' || e.key === 'F') {
                toggleFullscreen();
            }
        });

        updateSlide();
    </script>

</body>
</html>
