<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;500;600;700&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Grotesk', sans-serif; background-color: #F7F5F0; color: #1C1C1C; }
        .font-display { font-family: 'Bricolage Grotesque', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white/70 backdrop-blur-xl p-8 rounded-3xl shadow-xl shadow-black/5 border border-black/5">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-display font-semibold text-[#4A5D4E]">Admin Login</h1>
            <p class="text-sm text-black/50 mt-2">Masuk untuk mengelola data praktikum.</p>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-black/80 ml-1 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-2xl bg-white/60 border border-black/10 focus:border-[#4A5D4E] focus:outline-none focus:ring-4 focus:ring-[#4A5D4E]/10 transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-black/80 ml-1 mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-2xl bg-white/60 border border-black/10 focus:border-[#4A5D4E] focus:outline-none focus:ring-4 focus:ring-[#4A5D4E]/10 transition-all">
            </div>
            <button type="submit" class="w-full py-4 rounded-2xl bg-[#4A5D4E] text-[#F7F5F0] font-semibold hover:bg-[#38473b] transition-all">
                Masuk
            </button>
        </form>
    </div>
</body>
</html>
