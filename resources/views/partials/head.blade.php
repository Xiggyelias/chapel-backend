{{-- Shared <head> essentials: fonts and the pre-paint motion flag.
     Fraunces is loaded with its SOFT / WONK / opsz axes for the ink-settle headings. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght,SOFT,WONK@0,9..144,300..700,0..100,0..1;1,9..144,300..700,0..100,0..1&family=Plus+Jakarta+Sans:wght@400..800&display=swap" rel="stylesheet">
<script>
    (function () {
        var d = document.documentElement;
        if (!window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches) d.classList.add('motion');
        // Never leave content hidden if the motion script fails to arrive
        setTimeout(function () { d.classList.add('motion-ready'); }, 1600);
    })();
</script>
