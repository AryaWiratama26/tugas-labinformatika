<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;500;600;700&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
            background-color: #FFFFFF;
            color: #000000;
        }
        .font-display { font-family: 'Bricolage Grotesque', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    {{-- Decorative background elements --}}
    <div class="fixed top-0 right-0 w-[500px] h-[500px] rounded-full bg-[#193CB8]/5 blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
    <div class="fixed bottom-0 left-0 w-[400px] h-[400px] rounded-full bg-[#FFDF20]/10 blur-3xl translate-y-1/3 -translate-x-1/4 pointer-events-none"></div>

    <div class="max-w-md w-full relative z-10">
        {{-- Logo area --}}
        <div class="flex items-center justify-center space-x-4 mb-8">
            <div class="w-14 h-14 bg-white rounded-2xl shadow-md flex items-center justify-center border border-black/5">
                <img src="{{ asset('images/logo-upb.png') }}" alt="Logo UPB" class="w-9 h-9 object-contain">
            </div>
            <div class="w-14 h-14 bg-white rounded-2xl shadow-md flex items-center justify-center border border-black/5">
                <img src="{{ asset('images/simbaft.png') }}" alt="Logo SIMBA" class="w-9 h-9 object-contain">
            </div>
        </div>

        {{-- Login card --}}
        <div class="bg-white/80 backdrop-blur-xl p-8 rounded-3xl shadow-xl shadow-black/5 border border-black/5">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-display font-bold text-[#193CB8]">LabAdmin.</h1>
                <p class="text-sm text-black/50 mt-2">Masuk untuk mengelola data praktikum.</p>
            </div>

            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm flex items-center space-x-2">
                <i class="ph-fill ph-warning-circle text-lg text-red-500"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-black/80 ml-1 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-envelope text-lg text-black/40"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@example.com"
                               class="w-full pl-11 pr-4 py-3 rounded-2xl bg-white border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10 transition-all text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-black/80 ml-1 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-lock text-lg text-black/40"></i>
                        </div>
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full pl-11 pr-4 py-3 rounded-2xl bg-white border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10 transition-all text-sm">
                    </div>
                </div>
                <button type="submit" class="w-full py-3.5 rounded-2xl bg-[#193CB8] text-white font-semibold hover:bg-[#FFDF20] hover:text-black transition-all text-sm flex items-center justify-center space-x-2">
                    <span>Masuk</span>
                    <i class="ph ph-sign-in text-lg"></i>
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('submissions.create') }}" class="inline-flex items-center space-x-1.5 text-sm text-black/50 hover:text-[#193CB8] transition-all font-medium">
                    <i class="ph ph-arrow-left text-base"></i>
                    <span>Kembali ke halaman pengumpulan tugas</span>
                </a>
            </div>
        </div>

        <p class="text-center text-xs text-black/40 mt-6">Universitas Pelita Bangsa &mdash; LabAdmin Panel</p>
    </div>
</body>
</html>