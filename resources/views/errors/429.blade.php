<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 · Terlalu Banyak Permintaan</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;500;600;700&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Grotesk', sans-serif; background-color: #FFFFFF; color: #000000; }
        .font-display { font-family: 'Bricolage Grotesque', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 lg:p-8">
    <div class="max-w-md w-full text-center">
        <div class="flex items-center justify-center space-x-4 mb-8">
            <div class="w-14 h-14 rounded-2xl border border-black/5 flex items-center justify-center bg-white shadow-sm">
                <img src="{{ asset('images/logo-upb.png') }}" alt="UPB" class="w-9 h-9 object-contain">
            </div>
            <div class="w-14 h-14 rounded-2xl border border-black/5 flex items-center justify-center bg-white shadow-sm">
                <img src="{{ asset('images/simbaft.png') }}" alt="SIMBA FT" class="w-9 h-9 object-contain">
            </div>
        </div>

        <h1 class="font-display text-8xl lg:text-9xl font-bold text-[#193CB8] leading-none">429</h1>
        <h2 class="font-display text-2xl font-semibold mt-4">Terlalu Banyak Permintaan</h2>
        <p class="text-black/50 mt-2 leading-relaxed max-w-xs mx-auto">
            Terlalu banyak permintaan dalam waktu singkat. Silakan tunggu beberapa saat.
        </p>

        <a href="{{ url('/') }}"
           class="inline-flex items-center space-x-2 mt-8 px-6 py-3 rounded-2xl font-semibold text-sm transition-all"
           style="background-color: #FFDF20; color: #000000;">
            <i class="ph ph-arrow-left text-base"></i>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</body>
</html>
