@extends('layouts.admin')

@section('title', 'Semua Tugas Masuk')

@section('content')
<div class="flex flex-col space-y-6">

    {{-- Filter & Search Bar --}}
    <div class="bg-white rounded-3xl border border-black/5 shadow-sm p-5">
        <form method="GET" action="{{ route('admin.submissions.index') }}" class="flex flex-wrap gap-3 items-end">
            {{-- Search --}}
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Cari NIM / Nama</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-magnifying-glass text-black/40"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik NIM atau Nama..." class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#4A5D4E] focus:outline-none focus:ring-4 focus:ring-[#4A5D4E]/10 transition-all">
                </div>
            </div>

            {{-- Filter Mata Kuliah --}}
            <div class="min-w-[180px]">
                <label class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Mata Kuliah</label>
                <select name="subject_id" class="w-full px-3 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#4A5D4E] focus:outline-none focus:ring-4 focus:ring-[#4A5D4E]/10 transition-all cursor-pointer">
                    <option value="">Semua Matkul</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Modul --}}
            <div class="min-w-[180px]">
                <label class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Modul</label>
                <select name="module_id" class="w-full px-3 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#4A5D4E] focus:outline-none focus:ring-4 focus:ring-[#4A5D4E]/10 transition-all cursor-pointer">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod->id }}" {{ request('module_id') == $mod->id ? 'selected' : '' }}>{{ $mod->subject->name ?? '' }} — {{ $mod->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kelas --}}
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Kelas</label>
                <select name="lab_class_id" class="w-full px-3 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#4A5D4E] focus:outline-none focus:ring-4 focus:ring-[#4A5D4E]/10 transition-all cursor-pointer">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('lab_class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 bg-[#4A5D4E] text-white text-sm font-semibold rounded-xl hover:bg-[#38473b] transition-all flex items-center space-x-1.5">
                    <i class="ph ph-funnel"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.submissions.index') }}" class="px-4 py-2.5 bg-gray-100 text-black/60 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-all">Reset</a>
                <a href="{{ route('admin.submissions.export', request()->query()) }}" class="px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-all flex items-center space-x-1.5">
                    <i class="ph ph-file-csv"></i>
                    <span>Export CSV</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-3xl border border-black/5 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-black/5 flex items-center justify-between">
            <h3 class="font-display font-semibold">Daftar Pengumpulan Tugas</h3>
            <span class="text-sm text-black/40">{{ $submissions->total() }} data ditemukan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-black/50 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">Praktikan</th>
                        <th class="px-6 py-4 font-medium">Mata Kuliah</th>
                        <th class="px-6 py-4 font-medium">Modul</th>
                        <th class="px-6 py-4 font-medium">Kelas</th>
                        <th class="px-6 py-4 font-medium">Waktu</th>
                        <th class="px-6 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse($submissions as $submission)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-black/80 text-sm">{{ $submission->student_name }}</p>
                            <p class="text-xs text-black/40 font-mono">{{ $submission->student_nim }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-black/70">{{ $submission->subject->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-black/70">{{ $submission->module->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-black/70">{{ $submission->labClass->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs text-black/50">{{ $submission->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                @php
                                    $driveId = $submission->google_drive_id;
                                    if ($driveId && str_contains($driveId, '/')) {
                                        $driveId = last(explode('/', $driveId));
                                    }
                                @endphp
                                @if($driveId)
                                <a href="https://drive.google.com/file/d/{{ $driveId }}/view" target="_blank" class="p-2 text-[#4A5D4E] hover:bg-[#4A5D4E]/10 rounded-lg transition-all" title="Buka di Drive">
                                    <i class="ph ph-link text-lg"></i>
                                </a>
                                @endif
                                <form action="{{ route('admin.submissions.destroy', $submission) }}" method="POST" onsubmit="return confirm('Yakin hapus tugas {{ $submission->student_name }}? File di Google Drive juga akan ikut terhapus!');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                        <i class="ph ph-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-black/40">
                            <i class="ph ph-files text-4xl mb-2 block opacity-30"></i>
                            Tidak ada data yang sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($submissions->hasPages())
        <div class="p-5 border-t border-black/5">
            {{ $submissions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
