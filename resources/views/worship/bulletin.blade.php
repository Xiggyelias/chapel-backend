<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Worship Bulletin - {{ $sheet->service_date->format('M d, Y') }} | Kwang-Lim Chapel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=EB+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'EB Garamond', Georgia, serif;
            color: #0f172a;
            background: #f8fafc;
            margin: 0;
            padding: 40px;
        }
        .container {
            max-width: 740px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #DC2626;
            border-radius: 12px;
            padding: 48px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
        }
        .font-cinzel { font-family: 'Cinzel', Georgia, serif; }
        .font-sans { font-family: 'Inter', sans-serif; }
        .text-center { text-align: center; }
        .header-rule {
            border-top: 3px solid #DC2626;
            border-bottom: 1.5px solid #0B0F17;
            height: 4px;
            margin: 16px 0 24px 0;
        }
        .section-title {
            font-family: 'Cinzel', Georgia, serif;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #DC2626;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 4px;
            margin-top: 24px;
            margin-bottom: 12px;
            font-weight: 700;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .liturgy-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 15px;
        }
        .liturgy-item strong {
            font-family: 'Cinzel', Georgia, serif;
            color: #DC2626;
            font-size: 14px;
        }
        .no-print {
            max-width: 740px;
            margin: 0 auto 20px auto;
            text-align: right;
        }
        .print-btn {
            font-family: 'Inter', sans-serif;
            background: #DC2626;
            color: #ffffff;
            padding: 10px 20px;
            border: 1px solid transparent;
            border-radius: 10px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(139,21,43,0.25);
            transition: all 0.2s ease;
        }
        .print-btn:hover {
            background: #B91C1C;
        }
        @media print {
            body { padding: 0; background: #ffffff; }
            .container { border: none; box-shadow: none; padding: 20px; border-radius: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="print-btn" onclick="window.print()">Print or Save as PDF</button>
    </div>

    <div class="container">
        <!-- University & Chapel Header -->
        <div class="text-center">
            <div style="display: flex; justify-content: center; margin-bottom: 8px;">
                <div style="width: 26px; height: 46px;">
                    <x-umc-logo class="w-full h-full" variant="official" />
                </div>
            </div>
            <div class="font-cinzel" style="font-size: 13px; letter-spacing: 3px; text-transform: uppercase; color: #DC2626; font-weight: 800;">
                Chaplaincy Office
            </div>
            <h1 class="font-cinzel" style="font-size: 26px; color: #DC2626; margin: 6px 0; font-weight: 800; letter-spacing: 1px;">
                KWANG-LIM CHAPEL
            </h1>
            <div style="font-size: 13px; font-style: italic; color: #64748b;">
                School of Dreams in the Valley of Hope
            </div>
            <div class="header-rule"></div>
            <div class="font-cinzel" style="font-size: 17px; font-weight: bold; color: #DC2626;">
                {{ $sheet->service_type }}
            </div>
            <div style="font-size: 14px; color: #475569; margin-top: 4px;">
                {{ $sheet->service_date->format('l, F j, Y') }}
            </div>
        </div>

        <!-- Theme & Scripture -->
        <div style="background: #faf8f5; border: 1px solid #e7e5e4; border-left: 4px solid #DC2626; border-radius: 6px; padding: 18px; margin: 24px 0; text-align: center;">
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #DC2626; font-family: 'Cinzel', serif; font-weight: 700;">Sermon Theme</div>
            <div style="font-size: 22px; font-weight: bold; color: #DC2626; margin: 6px 0; font-family: 'Cinzel', serif;">
                "{{ $sheet->theme ?: 'Worship Service' }}"
            </div>
            <div style="font-size: 14px; font-style: italic; color: #475569;">
                Scripture Lesson: {{ $sheet->scripture ?: 'As Announced' }}
            </div>
        </div>

        <!-- Ministers of Worship -->
        <div class="section-title">Ministers of Worship</div>
        <div class="grid-2" style="font-size: 14px;">
            <div><strong>Preacher:</strong> {{ $sheet->preacher ?: 'University Chaplain' }}</div>
            <div><strong>Liturgist:</strong> {{ $sheet->liturgist ?: 'Assistant Chaplain' }}</div>
            <div><strong>Bible Reader:</strong> {{ $sheet->bible_reader ?: 'Student Representative' }}</div>
            <div><strong>Choir:</strong> {{ $sheet->choir ?: 'Praise the Lord Choir' }}</div>
            <div><strong>Praise & Worship:</strong> {{ $sheet->praise_worship_team ?: 'Chapel Band' }}</div>
            <div><strong>Ushering:</strong> {{ $sheet->ushers ?: 'Chapel Ushering Squad' }}</div>
        </div>

        <!-- Hymns -->
        <div class="section-title">Hymns of Adoration & Praise</div>
        <div style="font-size: 14px; line-height: 1.6; color: #1e293b; background: #fafafa; padding: 14px; border: 1px solid #f1f5f9; border-radius: 6px;">
            {{ $sheet->hymns ?: 'UMH 57: O For a Thousand Tongues to Sing; UMH 368: My Hope Is Built; Shona Hymn 11' }}
        </div>

        @if($sheet->lyrics)
            <div class="section-title">Hymn & Praise Lyrics</div>
            <div style="font-size: 13px; line-height: 1.6; color: #334155; background: #fafafa; padding: 14px; border: 1px solid #f1f5f9; border-radius: 6px; white-space: pre-wrap; font-family: monospace;">{{ $sheet->lyrics }}</div>
        @endif

        <!-- Order of Service -->
        <div class="section-title">Order of Service</div>
        <div style="font-size: 14px; line-height: 1.85;">
            <div class="liturgy-item"><span>Choral Prelude & Processional</span><em>Organ / Praise Team</em></div>
            <div class="liturgy-item"><span>Call to Worship & Invocation</span><em>Liturgist</em></div>
            <div class="liturgy-item"><span>Opening Hymn of Praise</span><em>Congregation</em></div>
            <div class="liturgy-item"><span>Affirmation of Faith & Gloria Patri</span><em>Congregation</em></div>
            <div class="liturgy-item"><span>Pastoral & Intercessory Prayer</span><em>Liturgist</em></div>
            <div class="liturgy-item"><span>Scripture Lesson</span><em>Bible Reader</em></div>
            <div class="liturgy-item"><span>Choral Anthem / Ministry in Song</span><em>Choir</em></div>
            <div class="liturgy-item"><span>The Spoken Word (Sermon)</span><em>Preacher</em></div>
            <div class="liturgy-item"><span>Offertory & Dedication of Gifts</span><em>Ushers</em></div>
            <div class="liturgy-item"><span>Closing Hymn & Benediction</span><em>Preacher</em></div>
        </div>

        @if($sheet->order_of_service_notes)
            <div class="section-title">Call to Services</div>
            <div style="font-size: 13px; font-style: italic; color: #475569; line-height: 1.6;">
                {{ $sheet->order_of_service_notes }}
            </div>
        @endif

        <div style="margin-top: 36px; border-top: 1px solid #e2e8f0; padding-top: 14px; font-size: 11px; text-align: center; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif;">
            Kwang-Lim Chapel &bull; chaplain@africau.edu
        </div>
    </div>

</body>
</html>
