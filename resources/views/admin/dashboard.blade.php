@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="flex flex-col space-y-6">

    {{-- Greeting --}}
    <div>
        <h2 class="font-display text-2xl font-bold">Selamat datang, {{ auth()->user()->name ?? 'Admin' }}</h2>
        <p class="text-black/50 mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Setup checklist (first-time operators) --}}
    @php
        $setupSteps = [
            ['label' => 'Tambah Mata Kuliah', 'desc' => 'Daftar matkul yang dibuka untuk pengumpulan', 'done' => $totalSubjects > 0, 'url' => route('admin.subjects.index')],
            ['label' => 'Tambah Modul + Deadline', 'desc' => 'Tugas per matkul, form menolak jika lewat tenggat', 'done' => $totalModules > 0, 'url' => route('admin.modules.index')],
            ['label' => 'Tambah Kelas Lab', 'desc' => 'Daftar kelas yang muncul di form', 'done' => $totalClasses > 0, 'url' => route('admin.classes.index')],
            ['label' => 'Sebarkan link form', 'desc' => 'Bagikan ke praktikan agar bisa mengumpulkan', 'done' => $totalSubmissions > 0, 'url' => url('/')],
        ];
        $setupDone = collect($setupSteps)->where('done', true)->count();
    @endphp
    @if($setupDone < count($setupSteps))
    <div class="bg-white rounded-2xl border border-[#193CB8]/15 shadow-sm p-5">
        <div class="flex items-center justify-between mb-1">
            <h3 class="font-display font-semibold">Langkah Awal</h3>
            <span class="text-xs font-semibold text-[#193CB8] bg-[#193CB8]/10 px-2.5 py-1 rounded-lg">{{ $setupDone }}/{{ count($setupSteps) }} selesai</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 mt-3">
            @foreach($setupSteps as $i => $step)
            <a href="{{ $step['url'] }}" @if($i === 3) target="_blank" @endif class="flex items-start space-x-3 p-3.5 rounded-xl border transition-all {{ $step['done'] ? 'border-black/5 bg-black/[0.02]' : 'border-[#193CB8]/20 bg-[#193CB8]/[0.03] hover:border-[#193CB8]/40 hover:bg-[#193CB8]/[0.06]' }}">
                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 {{ $step['done'] ? 'bg-green-100' : 'bg-[#193CB8] text-white' }}">
                    @if($step['done'])
                        <i class="ph ph-check text-sm text-green-600"></i>
                    @else
                        <span class="text-xs font-bold">{{ $i + 1 }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold {{ $step['done'] ? 'text-black/40 line-through' : '' }}">{{ $step['label'] }}</p>
                    <p class="text-xs text-black/40 mt-0.5 leading-relaxed">{{ $step['desc'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($ungradedCount > 0)
    <a href="{{ route('admin.submissions.index') }}" class="flex items-center space-x-3 bg-[#FFDF20]/25 border border-[#FFDF20] rounded-2xl px-5 py-4 hover:bg-[#FFDF20]/40 transition-all">
        <i class="ph ph-star text-xl"></i>
        <p class="text-sm font-medium">Ada <span class="font-bold">{{ number_format($ungradedCount) }} tugas</span> yang belum dinilai. Klik untuk menilai sekarang.</p>
        <i class="ph ph-arrow-right ml-auto"></i>
    </a>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-black/5 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-[#193CB8]/10 flex items-center justify-center flex-shrink-0">
                <i class="ph ph-files text-xl text-[#193CB8]"></i>
            </div>
            <div>
                <p class="text-xs text-black/50 font-medium uppercase tracking-wide">Total Tugas</p>
                <p class="font-display text-2xl font-bold mt-0.5">{{ number_format($totalSubmissions) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-black/5 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-[#FFDF20]/20 flex items-center justify-center flex-shrink-0">
                <i class="ph ph-book-open-text text-xl text-[#193CB8]"></i>
            </div>
            <div>
                <p class="text-xs text-black/50 font-medium uppercase tracking-wide">Mata Kuliah</p>
                <p class="font-display text-2xl font-bold mt-0.5">{{ number_format($totalSubjects) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-black/5 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-black/5 flex items-center justify-center flex-shrink-0">
                <i class="ph ph-users-three text-xl text-black/60"></i>
            </div>
            <div>
                <p class="text-xs text-black/50 font-medium uppercase tracking-wide">Kelas Lab</p>
                <p class="font-display text-2xl font-bold mt-0.5">{{ number_format($totalClasses) }}</p>
            </div>
        </div>
    </div>

    {{-- Activity + Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent submissions --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-black/5 flex items-center justify-between">
                <h3 class="font-display font-semibold">Tugas Masuk Terbaru</h3>
                @if($totalSubmissions > 0)
                <a href="{{ route('admin.submissions.index') }}" class="text-xs text-[#193CB8] hover:text-[#FFDF20] font-medium transition-all">Lihat semua</a>
                @endif
            </div>
            <div class="divide-y divide-black/5">
                @forelse($recentSubmissions as $submission)
                <div class="px-5 py-3.5 flex items-center justify-between hover:bg-black/[0.02] transition-all">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-[#193CB8]/10 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-[#193CB8]">{{ strtoupper(substr($submission->student_name, 0, 1)) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-sm truncate">{{ $submission->student_name }}</p>
                            <p class="text-xs text-black/40 truncate">{{ $submission->student_nim }} &middot; {{ $submission->subject->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 flex-shrink-0 ml-3">
                        <span class="text-xs text-black/40 whitespace-nowrap">{{ $submission->created_at->diffForHumans() }}</span>
                        @if($submission->google_drive_id && str_starts_with($submission->google_drive_id, 'http'))
                        @php
                            $previewUrl = str_replace(['/view', '/view?usp=drivesdk', '/view?usp=sharing'], '/preview', $submission->google_drive_id);
                            if (str_contains($previewUrl, 'uc?') || str_contains($previewUrl, 'export=download')) {
                                preg_match('/[?&]id=([^&]+)/', $previewUrl, $m);
                                $previewUrl = !empty($m[1]) ? 'https://drive.google.com/file/d/' . $m[1] . '/preview' : $previewUrl;
                            }
                        @endphp
                        <a href="{{ $previewUrl }}" target="_blank" class="p-1.5 rounded-lg hover:bg-black/5 text-black/30 hover:text-[#193CB8] transition-all" title="Preview">
                            <i class="ph ph-eye text-base"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="px-5 py-12 text-center">
                    <div class="w-12 h-12 rounded-full bg-black/5 flex items-center justify-center mx-auto mb-3">
                        <i class="ph ph-tray text-xl text-black/30"></i>
                    </div>
                    <p class="text-sm text-black/40">Belum ada tugas yang masuk.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Quick actions sidebar --}}
        <div class="flex flex-col space-y-4">
            <div class="bg-white rounded-2xl border border-black/5 shadow-sm p-5">
                <h3 class="font-display font-semibold text-sm mb-3">Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.submissions.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-black/5 transition-all group">
                        <div class="w-9 h-9 rounded-lg bg-[#193CB8]/10 flex items-center justify-center flex-shrink-0 group-hover:bg-[#193CB8]/20 transition-all">
                            <i class="ph ph-tray text-base text-[#193CB8]"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Semua Tugas</p>
                            <p class="text-xs text-black/40">Lihat & kelola submissions</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.modules.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-black/5 transition-all group">
                        <div class="w-9 h-9 rounded-lg bg-[#FFDF20]/20 flex items-center justify-center flex-shrink-0 group-hover:bg-[#FFDF20]/30 transition-all">
                            <i class="ph ph-files text-base text-[#193CB8]"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Modul</p>
                            <p class="text-xs text-black/40">Atur modul praktikum</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.subjects.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-black/5 transition-all group">
                        <div class="w-9 h-9 rounded-lg bg-black/5 flex items-center justify-center flex-shrink-0 group-hover:bg-black/10 transition-all">
                            <i class="ph ph-book-open-text text-base text-black/60"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Mata Kuliah</p>
                            <p class="text-xs text-black/40">Atur mata kuliah</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-black/5 shadow-sm p-5">
                <h3 class="font-display font-semibold text-sm mb-3">Ringkasan</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-black/60">Total tugas</span>
                        <span class="font-semibold text-sm">{{ number_format($totalSubmissions) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-black/60">Matkul aktif</span>
                        <span class="font-semibold text-sm">{{ number_format($totalSubjects) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-black/60">Kelas lab</span>
                        <span class="font-semibold text-sm">{{ number_format($totalClasses) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endSection