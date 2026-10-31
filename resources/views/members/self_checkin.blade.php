<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meeting Check-In{{ $matchingClub ? ' - ' . $matchingClub->name : '' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
    </style>
</head>
<body class="min-h-screen text-slate-800 p-4 flex flex-col items-center justify-center">

    <div class="max-w-md w-full space-y-6">
        
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#DC2626] p-2 mb-1 shadow-md shadow-red-200">
                <x-umc-logo class="w-full h-full" />
            </div>
            <div class="text-xs uppercase tracking-widest text-[#DC2626] font-bold">Africa University Chaplaincy</div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $matchingClub?->name ?? 'Check-In Code Expired' }}</h1>
            <p class="text-xs text-slate-500">Fellowship Attendance &bull; {{ date('l, F j, Y') }}</p>
        </div>

        @if(!$matchingClub)
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-amber-200 text-center space-y-2">
            <h2 class="text-base font-bold text-amber-900">This check-in code is not valid today</h2>
            <p class="text-xs text-slate-600">Kiosk codes change every day. Please scan the QR code currently displayed at the meeting entrance, or ask a club leader to record your attendance.</p>
        </div>
        @else
        <div id="checkinCard" class="bg-white text-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
            
            <div class="text-center space-y-1">
                <h2 class="text-base font-bold text-slate-800">Welcome to Fellowship 👋</h2>
                <p class="text-xs text-slate-500">Enter your Student ID or AU Email to record your attendance today.</p>
            </div>

            <form id="checkinForm" onsubmit="submitCheckin(event)" class="space-y-4">
                @csrf

                <div>
                    <label for="student_input" class="block text-xs font-semibold text-slate-700 mb-1.5">Student ID or AU Email</label>
                    <input type="text" id="student_input" required minlength="3" maxlength="255" autocomplete="email" autofocus placeholder="e.g. 102458 or bmutasa@africau.edu" class="w-full text-sm p-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626] transition">
                </div>

                <button type="submit" id="submitBtn" class="w-full py-3.5 px-4 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider transition shadow-sm active:scale-98">
                    Confirm My Attendance
                </button>
            </form>

            <p id="errorBox" role="alert" class="hidden text-xs text-rose-800 bg-rose-50 border border-rose-200 rounded-xl p-3"></p>

            <div id="resultBox" role="status" class="hidden p-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-2 animate-fade-in">
                <div class="w-12 h-12 mx-auto rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xl mb-1 shadow-xs">✓</div>
                <div class="text-base font-bold text-emerald-900" id="resultName">Checked In!</div>
                <div class="text-xs text-emerald-700 font-medium" id="resultMsg">Attendance successfully recorded. Welcome!</div>
            </div>

        </div>
        @endif

        <div class="text-center text-xs text-slate-400">
            Kwang-Lim Chapel &bull; Africa University &bull; School of Dreams in the Valley of Hope
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
