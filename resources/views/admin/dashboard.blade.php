@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Stat Cards -->
    <div class="bg-white p-6 rounded-3xl border border-black/5 shadow-sm flex items-center space-x-4">
        <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-2xl">
            <i class="ph-fill ph-file-text"></i>
        </div>
        <div>
            <p class="text-sm text-black/50 font-medium">Total Tugas Masuk</p>
            <h3 class="font-display text-3xl font-bold mt-1">{{ $totalSubmissions }}</h3>
        </div>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-black/5 shadow-sm flex items-center space-x-4">
        <div class="w-14 h-14 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-2xl">
            <i class="ph-fill ph-book-open-text"></i>
        </div>
        <div>
            <p class="text-sm text-black/50 font-medium">Total Mata Kuliah</p>
            <h3 class="font-display text-3xl font-bold mt-1">{{ $totalSubjects }}</h3>
        </div>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-black/5 shadow-sm flex items-center space-x-4">
        <div class="w-14 h-14 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center text-2xl">
            <i class="ph-fill ph-users-three"></i>
        </div>
        <div>
            <p class="text-sm text-black/50 font-medium">Total Kelas Lab</p>
            <h3 class="font-display text-3xl font-bold mt-1">{{ $totalClasses }}</h3>
        </div>
    </div>
</div>

<div class="bg-white rounded-3xl border border-black/5 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-black/5 flex items-center justify-between">
        <h3 class="font-display font-semibold text-lg">Tugas Masuk Terbaru</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 text-black/50 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium">Praktikan</th>
                    <th class="px-6 py-4 font-medium">Mata Kuliah</th>
                    <th class="px-6 py-4 font-medium">Kelas</th>
                    <th class="px-6 py-4 font-medium">Waktu</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse($recentSubmissions as $submission)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-black/80">{{ $submission->student_name }}</p>
                        <p class="text-xs text-black/50">{{ $submission->student_nim }}</p>
                    </td>
                    <td class="px-6 py-4 text-black/70">{{ $submission->subject->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-black/70">{{ $submission->labClass->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-black/70 text-xs">{{ $submission->created_at->diffForHumans() }}</td>
                    <td class="px-6 py-4 text-right">
                        @if($submission->google_drive_id && str_starts_with($submission->google_drive_id, 'http'))
                        @php
                            $previewUrl = str_replace(['/view', '/view?usp=drivesdk', '/view?usp=sharing'], '/preview', $submission->google_drive_id);
                            if (str_contains($previewUrl, 'uc?') || str_contains($previewUrl, 'export=download')) {
                                preg_match('/[?&]id=([^&]+)/', $previewUrl, $m);
                                $previewUrl = !empty($m[1]) ? 'https://drive.google.com/file/d/' . $m[1] . '/preview' : $previewUrl;
                            }
                        @endphp
                        <a href="{{ $previewUrl }}" target="_blank" class="inline-flex items-center space-x-1 px-3 py-1.5 bg-[#4A5D4E]/10 text-[#4A5D4E] rounded-lg text-xs font-semibold hover:bg-[#4A5D4E]/20 transition-all">
                            <i class="ph ph-eye"></i>
                            <span>Preview</span>
                        </a>
                        @else
                        <span class="text-xs text-black/30">–</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-black/40">Belum ada tugas yang masuk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
