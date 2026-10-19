<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#DC2626">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Apply for Fellowship Charter | Africa University Kwang-Lim Chapel</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background-color: #F8FAFC; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between p-4 sm:p-6 lg:p-8 bg-[#F8FAFC] text-slate-800 relative selection:bg-[#DC2626] selection:text-white">

    <!-- Main Container -->
    <div class="max-w-3xl w-full mx-auto my-auto relative z-10 py-6 space-y-6">
        
        <!-- Header & Crest -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-5">
            <div class="flex items-center space-x-3.5">
                <div class="w-12 h-12 rounded-2xl bg-[#DC2626] flex items-center justify-center p-2.5 shadow-md shadow-red-200 shrink-0">
                    <svg class="w-full h-full text-white" viewBox="0 0 100 100" fill="currentColor">
                        <rect x="44" y="12" width="12" height="76" rx="3" fill="#FFFFFF" />
                        <rect x="22" y="32" width="56" height="12" rx="3" fill="#FFFFFF" />
                        <circle cx="50" cy="38" r="16" fill="none" stroke="#FFFFFF" stroke-width="2.5" opacity="0.6" stroke-dasharray="5 3"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-[#DC2626] font-bold">
                        Chaplaincy Office
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900">
                        Fellowship Charter Application
                    </h1>
                </div>
            </div>
            <a href="{{ route('login') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200 transition shadow-xs self-start sm:self-auto">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Sign In</span>
            </a>
        </div>

        <!-- Friendly Welcome & Review Notice -->
        <div class="p-4 sm:p-5 rounded-2xl bg-red-50/70 border border-red-200/80 text-slate-700 text-xs flex items-start space-x-3 shadow-xs">
            <div class="w-8 h-8 rounded-xl bg-[#DC2626] flex items-center justify-center text-white shrink-0 mt-0.5 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="space-y-1">
                <div class="font-bold text-slate-900 text-xs">
                    Welcome to the Fellowship Registry 👋
                </div>
                <p class="text-slate-600 leading-relaxed text-xs">
                    Please fill out the details below to register your student fellowship or prayer group. Once submitted, your application will be reviewed by the Chaplaincy Office, and you will receive guidance on meeting spaces, equipment, and charter status.
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                <div class="font-bold flex items-center space-x-1.5 text-rose-900">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Please correct the following fields:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Secure Registration Form Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 text-slate-800 shadow-sm border border-slate-200/80 overflow-hidden relative">
            <form method="POST" action="{{ route('clubs.store') }}" enctype="multipart/form-data" class="space-y-6 text-xs">
                @csrf

                <!-- Section 1: Student Leadership & Contact -->
                <div class="space-y-4">
                    <div class="border-b border-slate-100 pb-2.5">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-[#DC2626] flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                            <span>1. Student Leadership &amp; Contact Details</span>
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Applicant must be a currently enrolled Africa University student</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">Founding President Full Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="founding_president_name" value="{{ old('founding_president_name') }}" required placeholder="e.g. Farai Makoni" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">Student / Staff ID Number <span class="text-rose-500">*</span></label>
                            <input type="text" name="student_id" value="{{ old('student_id') }}" required placeholder="e.g. AU-2024-8921" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs font-mono uppercase transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">AU Student Email Address <span class="text-rose-500">*</span></label>
                            <input type="email" name="founding_president_email" value="{{ old('founding_president_email') }}" required placeholder="e.g. fmakoni@africau.edu" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">WhatsApp / Phone Number <span class="text-rose-500">*</span></label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" required placeholder="e.g. +263 77 123 4567" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">Faculty / Staff Advisor Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="advisor_name" value="{{ old('advisor_name') }}" required placeholder="e.g. Dr. Tendai Mutasa" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">Faculty Advisor AU Email <span class="text-rose-500">*</span></label>
                            <input type="email" name="advisor_email" value="{{ old('advisor_email') }}" required placeholder="e.g. tmutasa@africau.edu" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Fellowship Identity & Mission -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div class="border-b border-slate-100 pb-2.5">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-[#DC2626] flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                            <span>2. Fellowship Identity &amp; Purpose</span>
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Name and category under Africa University Chapel</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2 space-y-1">
                            <label class="block font-semibold text-slate-700">Fellowship Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Anglican Student Society" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">Short Code / Acronym <span class="text-rose-500">*</span></label>
                            <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g. ANGLICAN" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white font-mono uppercase text-xs transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">Fellowship Category <span class="text-rose-500">*</span></label>
                            <select name="type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                                <option value="">-- Select Category --</option>
                                <option value="fellowship" {{ old('type') == 'fellowship' ? 'selected' : '' }}>Campus Fellowship / Student Movement</option>
                                <option value="prayer group" {{ old('type') == 'prayer group' ? 'selected' : '' }}>Intercessory Prayer &amp; Revival Group</option>
                                <option value="choir or worship team" {{ old('type') == 'choir or worship team' ? 'selected' : '' }}>Choir or Worship Team</option>
                                <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Community Service &amp; Volunteer Ministry</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">Proposed Club Email <span class="text-rose-500">*</span></label>
                            <input type="email" name="official_email" value="{{ old('official_email') }}" required placeholder="e.g. anglican@africau.edu" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block font-semibold text-slate-700">Purpose &amp; Mission Statement <span class="text-rose-500">*</span></label>
                        <textarea name="description" rows="3" required placeholder="Describe the mission, activities, and positive impact your fellowship aims to bring to Africa University..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">{{ old('description') }}</textarea>
                    </div>
                </div>

                <!-- Section 3: Schedule & Venue -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div class="border-b border-slate-100 pb-2.5">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-[#DC2626] flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                            <span>3. Preferred Schedule &amp; Meeting Space</span>
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Regular gathering days and preferred venue</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">Preferred Meeting Schedule <span class="text-rose-500">*</span></label>
                            <input type="text" name="meeting_schedule" value="{{ old('meeting_schedule') }}" required placeholder="e.g. Fridays 18:00 - 20:00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">Preferred Campus Venue <span class="text-rose-500">*</span></label>
                            <select name="venue" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-slate-50/50 focus:bg-white text-xs transition">
                                <option value="">-- Select Preferred Venue --</option>
                                @foreach($venues as $v)
                                    <option value="{{ $v->name }}" {{ old('venue') == $v->name ? 'selected' : '' }}>
                                        {{ $v->name }} (Capacity: {{ $v->capacity }})
                                    </option>
                                @endforeach
                                <option value="Old Chapel Room" {{ old('venue') == 'Old Chapel Room' ? 'selected' : '' }}>Old Chapel Room</option>
                                <option value="Prayer Garden" {{ old('venue') == 'Prayer Garden' ? 'selected' : '' }}>Prayer Garden</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Constitution & Campus Covenant -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div class="border-b border-slate-100 pb-2.5">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-[#DC2626] flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                            <span>4. Constitution &amp; Campus Guidelines Covenant</span>
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Please attach your group constitution or draft rules</p>
                    </div>

                    <div class="space-y-1">
                        <label class="block font-semibold text-slate-700">
                            Draft Constitution / Guidelines (PDF or Word Document)
                        </label>
                        <input type="file" name="constitution" accept=".pdf,.doc,.docx" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-700 bg-slate-50 text-xs transition">
                        <p class="text-[10px] text-slate-500">Your constitution will be kept on file in the Chaplaincy Records Vault.</p>
                    </div>

                    <!-- Campus Covenant Checkbox -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input type="checkbox" name="covenant_accepted" value="1" required class="mt-0.5 rounded text-[#DC2626] focus:ring-[#DC2626]">
                            <span class="text-xs text-slate-700 leading-relaxed">
                                We affirm that our fellowship will abide by the Africa University Student Code of Conduct, Kwang-Lim Chapel policies, and the university's spirit of mutual respect and Christian community.
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Submission Actions -->
                <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <a href="{{ route('login') }}" class="text-slate-500 hover:text-slate-800 text-xs font-semibold">
                        &larr; Cancel and return to sign in
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] active:scale-95 text-white font-bold tracking-wider text-xs uppercase shadow-sm transition">
                        Submit Fellowship Application
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center text-xs text-slate-400 space-y-1">
            <p>Kwang-Lim Chapel &bull; Fellowship Registry</p>
            <p class="text-[11px]">School of Dreams in the Valley of Hope</p>
        </div>

    </div>

</body>
</html>
