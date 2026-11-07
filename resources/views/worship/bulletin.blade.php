@php
    $hymnsText = $sheet->hymns ?: 'UMH 57: O For a Thousand Tongues to Sing; UMH 368: My Hope Is Built; Shona Hymn 11';
    $hymnList = array_values(array_filter(array_map('trim', explode(';', $hymnsText))));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Worship Bulletin - {{ $sheet->service_date->format('M d, Y') }} | Kwang-Lim Chapel</title>
    @include('partials.head')
    <style>
        :root {
            --crimson: #A51C30;
            --crimson-deep: #5E0F1C;
            --gold: #B8892B;
            --ink: #1C1815;
            --muted: #5D564E;
            --faint: #A59D92;
            --hair: #E7E2DA;
            --paper: #F6F3EE;
            --serif: 'Fraunces', 'Iowan Old Style', 'Palatino Linotype', Georgia, serif;
            --sans: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; }
        html { -webkit-font-smoothing: antialiased; text-rendering: optimizeLegibility; }
        body {
            margin: 0;
            padding: 40px 20px 64px;
            background: var(--paper);
            color: var(--ink);
            font-family: var(--serif);
            font-optical-sizing: auto;
            font-variation-settings: 'SOFT' 50, 'WONK' 0;
            font-size: 15px;
            line-height: 1.55;
        }
        h1, h2, h3, p, ol, ul, dl, dd { margin: 0; padding: 0; }
        ol, ul { list-style: none; }

        /* The printed leaf */
        .sheet {
            position: relative;
            max-width: 720px;
            margin: 0 auto;
            background: #fff;
            padding: 64px 72px 52px;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgb(28 24 21 / 0.05), 0 24px 48px -20px rgb(28 24 21 / 0.22);
        }
        /* A fine double frame, the way a printer would set it */
        .sheet::before {
            content: '';
            position: absolute;
            inset: 18px;
            border: 1px solid rgb(184 137 43 / 0.45);
            outline: 1px solid rgb(184 137 43 / 0.2);
            outline-offset: 3px;
            pointer-events: none;
        }

        .sc {
            font-variant-caps: all-small-caps;
            letter-spacing: 0.14em;
            font-weight: 600;
        }
        .center { text-align: center; }

        /* Masthead */
        .crest { width: 26px; height: 46px; margin: 0 auto 14px; }
        .office { font-size: 14.5px; color: var(--crimson); }
        .chapel {
            margin-top: 6px;
            font-size: 34px;
            line-height: 1.05;
            font-weight: 500;
            letter-spacing: -0.01em;
            font-variation-settings: 'SOFT' 60, 'WONK' 0, 'opsz' 144;
            color: var(--ink);
        }
        .motto { margin-top: 6px; font-size: 14px; font-style: italic; color: var(--muted); }

        /* hairline ◆ hairline */
        .ornament {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px auto;
            max-width: 300px;
            color: var(--gold);
            font-size: 9px;
            line-height: 1;
        }
        .ornament::before, .ornament::after { content: ''; flex: 1; height: 1px; background: currentColor; opacity: 0.55; }
        .ornament.wide { max-width: none; }

        .service-type { font-size: 17px; color: var(--crimson); }
        .service-date { margin-top: 2px; font-size: 15px; font-style: italic; color: var(--muted); }

        /* Theme */
        .theme { margin: 30px 0 6px; text-align: center; }
        .theme .label { font-size: 13.5px; color: var(--gold); }
        .theme .title {
            margin: 8px auto 0;
            max-width: 30ch;
            font-size: 29px;
            line-height: 1.18;
            font-weight: 400;
            font-style: italic;
            font-variation-settings: 'SOFT' 100, 'WONK' 1, 'opsz' 144;
            color: var(--crimson-deep);
            text-wrap: balance;
        }
        .theme .scripture { margin-top: 10px; font-size: 14.5px; color: var(--muted); }
        .theme .scripture strong { font-weight: 600; color: var(--ink); }

        /* Section headings: small caps, centred, with a short gold rule beneath */
        .section { margin-top: 34px; break-inside: avoid; page-break-inside: avoid; }
        .section-title {
            text-align: center;
            font-size: 16px;
            color: var(--crimson);
        }
        .section-title::after {
            content: '';
            display: block;
            width: 28px;
            height: 1px;
            margin: 8px auto 16px;
            background: var(--gold);
        }

        /* Ministers */
        .ministers { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 40px; }
        .ministers div { display: flex; flex-direction: column; text-align: center; }
        .ministers dt { font-size: 13.5px; color: var(--faint); font-weight: 600; font-variant-caps: all-small-caps; letter-spacing: 0.14em; }
        .ministers dd { font-size: 15.5px; }

        /* Hymns */
        .hymns { counter-reset: hymn; max-width: 480px; margin: 0 auto; }
        .hymns li { display: flex; gap: 12px; align-items: baseline; padding: 3px 0; font-size: 16px; }
        .hymns li::before {
            counter-increment: hymn;
            content: counter(hymn, upper-roman) '.';
            min-width: 2.2em;
            text-align: right;
            font-size: 12.5px;
            color: var(--gold);
            font-variant-numeric: lining-nums;
        }

        .lyrics {
            max-width: 480px;
            margin: 0 auto;
            font-size: 14.5px;
            line-height: 1.7;
            white-space: pre-wrap;
            color: #2C2723;
        }

        /* Order of service, with dot leaders */
        .order { max-width: 540px; margin: 0 auto; }
        .liturgy-item { display: flex; align-items: baseline; gap: 8px; padding: 4px 0; font-size: 15.5px; }
        .liturgy-item .leader { flex: 1; min-width: 16px; border-bottom: 1px dotted rgb(165 157 146 / 0.9); transform: translateY(-4px); }
        .liturgy-item em { font-size: 14px; color: var(--muted); white-space: nowrap; }

        .notes { max-width: 520px; margin: 0 auto; font-size: 14.5px; font-style: italic; color: var(--muted); text-align: center; white-space: pre-line; }

        .colophon { margin-top: 40px; text-align: center; font-size: 13.5px; color: var(--faint); }
        .colophon .sc { color: var(--muted); }

        /* Screen-only toolbar */
        .no-print {
            max-width: 720px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            font-family: var(--sans);
        }
        .print-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: var(--sans);
            font-size: 13px;
            font-weight: 600;
            line-height: 20px;
            color: #fff;
            background-color: var(--crimson);
            background-image: linear-gradient(180deg, rgb(255 255 255 / 0.12), rgb(255 255 255 / 0) 60%);
            padding: 9px 16px;
            border: 1px solid #861527;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.16), 0 1px 2px rgb(94 15 28 / 0.25), 0 6px 14px -6px rgb(165 28 48 / 0.5);
            transition: background-color 0.18s;
        }
        .print-btn:hover { background-color: #861527; }
        .print-btn:focus-visible { outline: 2px solid var(--crimson); outline-offset: 2px; }
        .print-btn svg { width: 16px; height: 16px; }

        @media (max-width: 640px) {
            body { padding: 16px 12px 40px; }
            .sheet { padding: 44px 30px 36px; }
            .sheet::before { inset: 10px; }
            .chapel { font-size: 27px; }
            .theme .title { font-size: 24px; }
            .ministers { grid-template-columns: 1fr; }
        }

        @page { size: A4; margin: 14mm 14mm 16mm; }
        @media print {
            body { padding: 0; background: #fff; font-size: 13.5px; }
            .sheet { max-width: none; box-shadow: none; border-radius: 0; padding: 26px 34px 20px; }
            .sheet::before { inset: 0; }
            .no-print { display: none; }
            .section { margin-top: 24px; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="print-btn" onclick="window.print()">
            <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print or Save as PDF
        </button>
    </div>

    <main class="sheet">
        {{-- Masthead --}}
        <header class="center">
            <div class="crest">
                <x-umc-logo class="w-full h-full" variant="official" />
            </div>
            <p class="office sc">Chaplaincy Office</p>
            <h1 class="chapel">Kwang-Lim Chapel</h1>
            <p class="motto">School of Dreams in the Valley of Hope</p>
            <div class="ornament" aria-hidden="true">&#9670;</div>
            <p class="service-type sc">{{ $sheet->service_type }}</p>
            <p class="service-date">{{ $sheet->service_date->format('l, F j, Y') }}</p>
        </header>

        {{-- Theme & scripture --}}
        <section class="theme">
            <p class="label sc">Sermon Theme</p>
            <h2 class="title">&ldquo;{{ $sheet->theme ?: 'Worship Service' }}&rdquo;</h2>
            <p class="scripture">Scripture Lesson: <strong>{{ $sheet->scripture ?: 'As Announced' }}</strong></p>
        </section>

        {{-- Order of service --}}
        <section class="section">
            <h3 class="section-title sc">Order of Service</h3>
            <div class="order">
                @foreach([
                    ['Choral Prelude & Processional', 'Organ / Praise Team'],
                    ['Call to Worship & Invocation', 'Liturgist'],
                    ['Opening Hymn of Praise', 'Congregation'],
                    ['Affirmation of Faith & Gloria Patri', 'Congregation'],
                    ['Pastoral & Intercessory Prayer', 'Liturgist'],
                    ['Scripture Lesson', 'Bible Reader'],
                    ['Choral Anthem / Ministry in Song', 'Choir'],
                    ['The Spoken Word (Sermon)', 'Preacher'],
                    ['Offertory & Dedication of Gifts', 'Ushers'],
                    ['Closing Hymn & Benediction', 'Preacher'],
                ] as [$act, $by])
                    <div class="liturgy-item"><span>{{ $act }}</span><span class="leader" aria-hidden="true"></span><em>{{ $by }}</em></div>
                @endforeach
            </div>
        </section>

        {{-- Hymns --}}
        <section class="section">
            <h3 class="section-title sc">Hymns of Adoration & Praise</h3>
            <ol class="hymns">
                @foreach($hymnList as $hymnLine)
                    <li>{{ $hymnLine }}</li>
                @endforeach
            </ol>
        </section>

        @if($sheet->lyrics)
            <section class="section">
                <h3 class="section-title sc">Hymn & Praise Lyrics</h3>
                <div class="lyrics">{{ $sheet->lyrics }}</div>
            </section>
        @endif

        {{-- Ministers --}}
        <section class="section">
            <h3 class="section-title sc">Ministers of Worship</h3>
            <dl class="ministers">
                <div><dt>Preacher</dt><dd>{{ $sheet->preacher ?: 'University Chaplain' }}</dd></div>
                <div><dt>Liturgist</dt><dd>{{ $sheet->liturgist ?: 'Assistant Chaplain' }}</dd></div>
                <div><dt>Bible Reader</dt><dd>{{ $sheet->bible_reader ?: 'Student Representative' }}</dd></div>
                <div><dt>Choir</dt><dd>{{ $sheet->choir ?: 'Praise the Lord Choir' }}</dd></div>
                <div><dt>Praise & Worship</dt><dd>{{ $sheet->praise_worship_team ?: 'Chapel Band' }}</dd></div>
                <div><dt>Ushering</dt><dd>{{ $sheet->ushers ?: 'Chapel Ushering Squad' }}</dd></div>
            </dl>
        </section>

        @if($sheet->order_of_service_notes)
            <section class="section">
                <h3 class="section-title sc">Call to Services</h3>
                <p class="notes">{{ $sheet->order_of_service_notes }}</p>
            </section>
        @endif

        <footer class="colophon">
            <div class="ornament wide" aria-hidden="true">&#9670;</div>
            <p><span class="sc">Kwang-Lim Chapel</span> &middot; chaplain@africau.edu</p>
        </footer>
    </main>

</body>
</html>
