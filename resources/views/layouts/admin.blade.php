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
        body { font-family: 'Space Grotesk', sans-serif; background-color: #FFFFFF; color: #000000; }
        .font-display { font-family: 'Bricolage Grotesque', sans-serif; }
        .swal2-popup { font-family: 'Space Grotesk', sans-serif !important; border-radius: 1.25rem !important; }
        .swal2-confirm, .swal2-cancel { border-radius: 0.75rem !important; font-weight: 600 !important; }
    </style>
</head>
<body class="min-h-screen flex text-sm">
    
    <!-- Mobile overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden backdrop-blur-sm" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-white border-r border-black/5 flex flex-col fixed h-full z-30 transition-transform duration-300 -translate-x-full lg:translate-x-0">
        <div class="h-20 flex items-center justify-between px-6 border-b border-black/5">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo-upb.png') }}" alt="UPB" class="h-8 w-auto">
                <div class="w-px h-6 bg-black/10"></div>
                <img src="{{ asset('images/simbaft.png') }}" alt="SIMBA FT" class="h-7 w-auto">
            </div>
            <button class="lg:hidden text-black/50" onclick="toggleSidebar()">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.dashboard') ? 'bg-[#193CB8] text-white' : 'text-black/60 hover:bg-black/5' }} transition-all">
                <i class="ph ph-squares-four text-xl"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.subjects.*') ? 'bg-[#193CB8] text-white' : 'text-black/60 hover:bg-black/5' }} transition-all">
                <i class="ph ph-book-open-text text-xl"></i>
                <span class="font-medium">Mata Kuliah</span>
            </a>
            <a href="{{ route('admin.modules.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.modules.*') ? 'bg-[#193CB8] text-white' : 'text-black/60 hover:bg-black/5' }} transition-all">
                <i class="ph ph-files text-xl"></i>
                <span class="font-medium">Modul Praktikum</span>
            </a>
            <a href="{{ route('admin.classes.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.classes.*') ? 'bg-[#193CB8] text-white' : 'text-black/60 hover:bg-black/5' }} transition-all">
                <i class="ph ph-users-three text-xl"></i>
                <span class="font-medium">Kelas Lab</span>
            </a>

            <div class="my-1 border-t border-black/5"></div>

            <a href="{{ route('admin.submissions.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.submissions.*') ? 'bg-[#193CB8] text-white' : 'text-black/60 hover:bg-black/5' }} transition-all">
                <i class="ph ph-tray text-xl"></i>
                <span class="font-medium">Semua Tugas</span>
            </a>
            <a href="{{ route('admin.api-keys.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.api-keys.*') ? 'bg-[#193CB8] text-white' : 'text-black/60 hover:bg-black/5' }} transition-all">
                <i class="ph ph-key text-xl"></i>
                <span class="font-medium">API Keys</span>
            </a>
        </nav>
        <div class="p-4 border-t border-black/5">
            <form action="{{ route('logout') }}" method="POST"
                  data-confirm="Keluar dari panel admin?"
                  data-confirm-text="Kamu harus login lagi untuk kembali."
                  data-confirm-button="Ya, keluar">
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
            <div class="flex items-center space-x-4 min-w-0">
                <button class="lg:hidden p-2 rounded-xl bg-white border border-black/5 shadow-sm text-black/70" onclick="toggleSidebar()">
                    <i class="ph ph-list text-xl"></i>
                </button>
                <div class="min-w-0">
                    @php
                        $crumbLabels = ['dashboard' => 'Dashboard', 'subjects' => 'Mata Kuliah', 'modules' => 'Modul Praktikum', 'classes' => 'Kelas Lab', 'submissions' => 'Semua Tugas', 'api-keys' => 'API Keys'];
                        $crumbActions = ['index' => null, 'create' => 'Tambah', 'edit' => 'Ubah', 'show' => 'Detail'];
                        $routeParts = explode('.', request()->route()?->getName() ?? '');
                        $crumbs = [['label' => 'Dashboard', 'url' => route('admin.dashboard')]];
                        if (($routeParts[1] ?? '') !== 'dashboard' && isset($routeParts[1])) {
                            $section = $routeParts[1];
                            $action = $routeParts[2] ?? 'index';
                            $crumbs[] = ['label' => $crumbLabels[$section] ?? ucfirst($section), 'url' => ($action === 'index' ? null : route('admin.'.$section.'.index'))];
                            if (!empty($crumbActions[$action])) $crumbs[] = ['label' => $crumbActions[$action], 'url' => null];
                        }
                    @endphp
                    <nav class="flex items-center space-x-1.5 text-xs text-black/40 mb-0.5" aria-label="Breadcrumb">
                        @foreach($crumbs as $i => $crumb)
                            @if($i > 0)<i class="ph ph-caret-right text-[10px]"></i>@endif
                            @if($crumb['url'])
                                <a href="{{ $crumb['url'] }}" class="hover:text-[#193CB8] transition-colors truncate">{{ $crumb['label'] }}</a>
                            @else
                                <span class="{{ $loop->last ? 'text-black/70 font-medium' : '' }} truncate">@yield('breadcrumb_last', $crumb['label'])</span>
                            @endif
                        @endforeach
                    </nav>
                    <h2 class="font-display text-lg lg:text-xl font-semibold truncate">@yield('title', 'Dashboard')</h2>
                </div>
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
            @yield('content')
        </div>
    </main>

    <script>
        window.__flash = {
            success: @json(session('success')),
            error: @json(session('error')),
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
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
