@extends('layouts.admin')

@section('title', 'Edit Modul')

@section('content')
<div class="max-w-2xl bg-white rounded-3xl border border-black/5 shadow-sm p-8">
    <form action="{{ route('admin.modules.update', $module) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')
        
        <div>
            <label class="block text-sm font-semibold text-black/80 ml-1 mb-2">Pilih Mata Kuliah</label>
            <select name="subject_id" required class="w-full px-4 py-3 rounded-2xl bg-white border border-black/10 focus:border-[#4A5D4E] focus:outline-none focus:ring-4 focus:ring-[#4A5D4E]/10 transition-all cursor-pointer">
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ old('subject_id', $module->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                @endforeach
            </select>
            @error('subject_id') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-black/80 ml-1 mb-2">Nama Modul</label>
            <input type="text" name="name" value="{{ old('name', $module->name) }}" required class="w-full px-4 py-3 rounded-2xl bg-white border border-black/10 focus:border-[#4A5D4E] focus:outline-none focus:ring-4 focus:ring-[#4A5D4E]/10 transition-all">
            @error('name') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-black/80 ml-1 mb-2">Keterangan / Instruksi Tugas (Opsional)</label>
            <textarea name="description" rows="4" class="w-full px-4 py-3 rounded-2xl bg-white border border-black/10 focus:border-[#4A5D4E] focus:outline-none focus:ring-4 focus:ring-[#4A5D4E]/10 transition-all">{{ old('description', $module->description) }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-black/80 ml-1 mb-2">Tenggat Waktu / Deadline (Opsional)</label>
            <input type="datetime-local" name="deadline" value="{{ old('deadline', $module->deadline?->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-3 rounded-2xl bg-white border border-black/10 focus:border-[#4A5D4E] focus:outline-none focus:ring-4 focus:ring-[#4A5D4E]/10 transition-all">
            <p class="text-xs text-black/40 mt-1 ml-1">Kosongkan jika tidak ada deadline. Setelah waktu ini, modul akan terkunci otomatis.</p>
            @error('deadline') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-black/80 ml-1 mb-2">Gambar Pendukung (Opsional)</label>

            @if($module->image_path)
            <div class="mb-3 p-3 bg-gray-50 rounded-2xl flex items-center space-x-4">
                <img src="{{ asset('storage/' . $module->image_path) }}" alt="Gambar Saat Ini" class="h-20 w-20 object-cover rounded-xl">
                <div class="flex-1">
                    <p class="text-sm font-medium text-black/70">Gambar saat ini</p>
                    <label class="flex items-center space-x-2 mt-2 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1" class="rounded">
                        <span class="text-xs text-red-500 font-medium">Hapus gambar ini</span>
                    </label>
                </div>
            </div>
            @endif

            <div class="relative border-2 border-dashed border-black/10 rounded-2xl p-6 text-center hover:border-[#4A5D4E]/40 transition-all cursor-pointer">
                <input type="file" name="image" id="image-input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(this)">
                <div id="image-placeholder">
                    <i class="ph ph-image text-4xl text-black/20"></i>
                    <p class="text-sm text-black/40 mt-2">{{ $module->image_path ? 'Ganti dengan gambar baru' : 'Klik atau drag & drop gambar di sini' }}</p>
                    <p class="text-xs text-black/30 mt-1">PNG, JPG, WEBP — Maks. 5MB</p>
                </div>
                <div id="image-preview" class="hidden">
                    <img id="preview-img" src="" alt="Preview" class="max-h-40 mx-auto rounded-xl object-contain">
                    <p class="text-xs text-[#4A5D4E] mt-2 font-medium" id="preview-name"></p>
                </div>
            </div>
            @error('image') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center space-x-3 pt-4">
            <button type="submit" class="px-6 py-3 rounded-2xl bg-[#4A5D4E] text-[#F7F5F0] font-semibold hover:bg-[#38473b] transition-all">
                Perbarui
            </button>
            <a href="{{ route('admin.modules.index') }}" class="px-6 py-3 rounded-2xl bg-gray-100 text-black/60 font-semibold hover:bg-gray-200 transition-all">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-name').textContent = input.files[0].name;
            document.getElementById('image-placeholder').classList.add('hidden');
            document.getElementById('image-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
