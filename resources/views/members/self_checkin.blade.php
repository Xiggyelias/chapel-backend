<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#F6F3EE">
    <title>Meeting Check-In{{ $matchingClub ? ' - ' . $matchingClub->name : '' }}</title>
    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-paper text-slate-800 antialiased">
<div class="min-h-screen px-4 py-10 sm:py-14 flex flex-col items-center justify-center"
     style="background: radial-gradient(80% 40% at 50% 0%, rgb(217 183 108 / .14), transparent 70%);">

    <div class="max-w-md w-full space-y-7">

        <div class="text-center" data-reveal>
            <span class="inline-grid place-items-center w-14 h-14 rounded-full mb-5"
                  style="background: radial-gradient(circle at 35% 30%, #C52A40, var(--color-brand-deep)); box-shadow: inset 0 0 0 1px rgb(255 255 255 / .14), 0 0 0 3px var(--color-paper), 0 0 0 4px rgb(184 137 43 / .45), 0 12px 24px -12px rgb(94 15 28 / .6);">
                <x-umc-logo class="w-6 h-6" />
            </span>
            <p class="text-[10.5px] uppercase tracking-[0.24em] text-brand font-bold">Africa University Chaplaincy</p>
            <h1 class="mt-2.5 font-display text-[2rem] sm:text-[2.3rem] leading-[1.06] tracking-[-0.025em] text-slate-900 text-balance" data-ink>{{ $matchingClub?->name ?? 'Check-In Code Expired' }}</h1>
            <p class="mt-2 text-[13px] text-slate-500">Fellowship Attendance <span class="text-gold mx-1" aria-hidden="true">&#9670;</span> {{ date('l, F j, Y') }}</p>
        </div>

        @if(!$matchingClub)
        <div class="card card-body text-center sm:p-8" data-reveal>
            <div class="mx-auto mb-4 grid place-items-center w-12 h-12 rounded-full bg-amber-50 text-amber-600 ring-1 ring-amber-600/25" aria-hidden="true">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h2 class="font-display text-[1.25rem] text-slate-900">This check-in code is not valid today</h2>
            <p class="mt-2 text-[13.5px] leading-relaxed text-slate-600">Kiosk codes change every day. Please scan the QR code currently displayed at the meeting entrance, or ask a club leader to record your attendance.</p>
        </div>
        @else
        <div id="checkinCard" class="card p-6 sm:p-8 space-y-6" data-reveal>

            <div class="text-center">
                <h2 class="font-display text-[1.4rem] text-slate-900">Welcome to Fellowship</h2>
                <p class="mt-1.5 text-[13.5px] leading-relaxed text-slate-500">Enter your Student ID or AU Email to record your attendance today.</p>
            </div>

            <form id="checkinForm" onsubmit="submitCheckin(event)" class="space-y-4">
                @csrf

                <div>
                    <label for="student_input" class="field-label">Student ID or AU Email</label>
                    <input type="text" id="student_input" required minlength="3" maxlength="255" autocomplete="email" autofocus placeholder="e.g. 102458 or bmutasa@africau.edu" class="field text-[16px] py-3.5">
                </div>

                <button type="submit" id="submitBtn" class="btn btn-primary btn-lg btn-block">Confirm My Attendance</button>
            </form>

            <p id="errorBox" role="alert" class="hidden flash-error text-[13px] leading-relaxed"></p>

            <div id="resultBox" role="status" class="hidden text-center py-2 modal-panel">
                <span class="mx-auto mb-4 grid place-items-center w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-600/20" style="box-shadow: 0 0 0 6px rgb(5 150 105 / .06);" aria-hidden="true">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 13l4 4L19 7"/></svg>
                </span>
                <div class="font-display text-[1.6rem] leading-tight text-slate-900" id="resultName">Checked In!</div>
                <div class="mt-2 text-[13px] text-emerald-700 font-medium" id="resultMsg">Attendance successfully recorded. Welcome!</div>
            </div>

        </div>
        @endif

        <div class="rule-ornament text-[9px]" aria-hidden="true">&#9670;</div>

        <p class="text-center text-[11.5px] leading-relaxed text-slate-400">
            Kwang-Lim Chapel &bull; Africa University &bull; School of Dreams in the Valley of Hope
        </p>

    </div>
</div>

    <script>
        async function submitCheckin(e) {
            e.preventDefault();
            const input = document.getElementById('student_input').value.trim();
            const btn = document.getElementById('submitBtn');
            const resultBox = document.getElementById('resultBox');
            const errorBox = document.getElementById('errorBox');

            if (!input) return;

            const showError = (message) => {
                errorBox.textContent = message;
                errorBox.classList.remove('hidden');
                btn.disabled = false;
                btn.textContent = 'Confirm My Attendance';
            };

            errorBox.classList.add('hidden');
            btn.disabled = true;
            btn.textContent = 'Recording Attendance...';

            try {
                const res = await fetch(@json(route('members.self_checkin.submit', $token)), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ student_id_or_email: input })
                });

                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success) {
                    document.getElementById('checkinForm').classList.add('hidden');
                    resultBox.classList.remove('hidden');
                    document.getElementById('resultName').textContent = data.member_name;
                    document.getElementById('resultMsg').textContent = `Present (${data.role}) • Total checked in: ${data.total_present}`;
                } else if (res.status === 429) {
                    showError('Too many check-ins from this device. Please wait a minute and try again.');
                } else {
                    showError(data.message || 'Check-in could not be recorded. Please see the club secretary.');
                }
            } catch (err) {
                showError('Network connection error. Please try again.');
            }
        }
    </script>

</body>
</html>
