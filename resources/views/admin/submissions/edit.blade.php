@extends('layouts.admin')

@section('title', 'Beri Nilai')
@section('breadcrumb_last', 'Beri Nilai')

@section('content')
<div class="max-w-2xl flex flex-col space-y-6">
    <div class="bg-white rounded-3xl border border-black/5 shadow-sm p-6">
        <p class="text-xs font-semibold text-black/40 uppercase tracking-wider mb-2">Praktikan</p>
        <p class="font-display text-xl font-semibold">{{ $submission->student_name }}</p>
        <p class="text-sm text-black/50 font-mono">{{ $submission->student_nim }}</p>
        <div class="mt-3 flex flex-wrap gap-2 text-xs">
            <span class="px-2.5 py-1 rounded-lg bg-[#193CB8]/10 text-[#193CB8] font-semibold">{{ $submission->subject->name ?? '-' }}</span>
            <span class="px-2.5 py-1 rounded-lg bg-black/5 text-black/60 font-semibold">{{ $submission->module->name ?? '-' }}</span>
            <span class="px-2.5 py-1 rounded-lg bg-black/5 text-black/60 font-semibold">{{ $submission->labClass->name ?? '-' }}</span>
        </div>
        @if($submission->google_drive_id)
        <a href="{{ $submission->google_drive_id }}" target="_blank" class="inline-flex items-center space-x-1.5 mt-3 text-sm text-[#193CB8] font-semibold hover:underline">
            <i class="ph ph-eye"></i><span>Lihat file di Drive</span>
        </a>
        @endif
    </div>

    <div class="bg-white rounded-3xl border border-black/5 shadow-sm p-6">
        <h3 class="font-display font-semibold mb-4">Penilaian</h3>
        <label class="flex items-start space-x-3 p-4 rounded-2xl bg-black/[0.03] border border-black/5 cursor-pointer mb-4 hover:bg-black/[0.05] transition-all">
            <input type="checkbox" name="is_hidden" value="1" {{ old('is_hidden', $submission->is_hidden) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded accent-[#193CB8] cursor-pointer">
            <span>
                <span class="block text-sm font-semibold">Sembunyikan dari daftar (arsip)</span>
                <span class="block text-xs text-black/50 mt-0.5">Tugas yang sudah selesai bisa diarsipkan agar daftar utama tetap rapi. Data tidak dihapus dan bisa ditampilkan lagi kapan saja.</span>
            </span>
        </label>
        <form method="POST" action="{{ route('admin.submissions.update', $submission) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Nilai (0–100)</label>
                <input type="number" name="score" min="0" max="100" step="0.01" value="{{ old('score', $submission->score) }}" placeholder="cth: 85" class="w-40 px-4 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10">
                @error('score')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Feedback</label>
                <textarea name="feedback" rows="4" placeholder="Catatan untuk praktikan..." class="w-full px-4 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10">{{ old('feedback', $submission->feedback) }}</textarea>
                @error('feedback')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-[#193CB8] text-white text-sm font-semibold rounded-xl hover:bg-[#FFDF20] hover:text-black transition-all">Simpan Nilai</button>
                <a href="{{ route('admin.submissions.index') }}" class="px-5 py-2.5 bg-gray-100 text-black/60 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-all">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
