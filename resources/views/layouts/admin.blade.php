<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;500;600;700&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Space Grotesk', sans-serif; background-color: #F7F5F0; color: #1C1C1C; }
        .font-display { font-family: 'Bricolage Grotesque', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex text-sm">
    
    <!-- Mobile overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden backdrop-blur-sm" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-white border-r border-black/5 flex flex-col fixed h-full z-30 transition-transform duration-300 -translate-x-full lg:translate-x-0">
        <div class="h-20 flex items-center justify-between px-8 border-b border-black/5">
            <h1 class="font-display text-xl font-bold text-[#4A5D4E]">LabAdmin.</h1>
            <button class="lg:hidden text-black/50" onclick="toggleSidebar()">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.dashboard') ? 'bg-[#4A5D4E] text-white' : 'text-black/60 hover:bg-black/5' }} transition-all">
                <i class="ph ph-squares-four text-xl"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.subjects.*') ? 'bg-[#4A5D4E] text-white' : 'text-black/60 hover:bg-black/5' }} transition-all">
                <i class="ph ph-book-open-text text-xl"></i>
                <span class="font-medium">Mata Kuliah</span>
            </a>
            <a href="{{ route('admin.modules.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.modules.*') ? 'bg-[#4A5D4E] text-white' : 'text-black/60 hover:bg-black/5' }} transition-all">
                <i class="ph ph-files text-xl"></i>
                <span class="font-medium">Modul Praktikum</span>
            </a>
            <a href="{{ route('admin.classes.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.classes.*') ? 'bg-[#4A5D4E] text-white' : 'text-black/60 hover:bg-black/5' }} transition-all">
                <i class="ph ph-users-three text-xl"></i>
                <span class="font-medium">Kelas Lab</span>
            </a>

            <div class="my-1 border-t border-black/5"></div>

            <a href="{{ route('admin.submissions.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.submissions.*') ? 'bg-[#4A5D4E] text-white' : 'text-black/60 hover:bg-black/5' }} transition-all">
                <i class="ph ph-tray text-xl"></i>
                <span class="font-medium">Semua Tugas</span>
            </a>
        </nav>
        <div class="p-4 border-t border-black/5">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-2xl text-red-600 hover:bg-red-50 font-medium transition-all">
                    <i class="ph ph-sign-out text-xl"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 lg:ml-64 flex flex-col min-h-screen transition-all duration-300 w-full">
        <header class="h-20 bg-white/50 backdrop-blur-md border-b border-black/5 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-10">
            <div class="flex items-center space-x-4">
                <button class="lg:hidden p-2 rounded-xl bg-white border border-black/5 shadow-sm text-black/70" onclick="toggleSidebar()">
                    <i class="ph ph-list text-xl"></i>
                </button>
                <h2 class="font-display text-lg lg:text-xl font-semibold">@yield('title', 'Dashboard')</h2>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-gray-200 border-2 border-white shadow-sm flex items-center justify-center hidden sm:flex">
                    <i class="ph ph-user text-gray-500"></i>
                </div>
                <div class="text-right sm:text-left">
                    <p class="font-medium leading-none text-sm">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-black/50 mt-1 hidden sm:block">Administrator</p>
                </div>
            </div>
        </header>

        <div class="p-4 lg:p-8 overflow-x-hidden w-full max-w-full">
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl flex items-center space-x-3">
                <i class="ph-fill ph-check-circle text-xl text-green-500"></i>
                <p>{{ session('success') }}</p>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
