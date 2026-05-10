@extends('layouts.admin')

@section('title', 'Edit Kelas Lab')

@section('content')
<div class="max-w-2xl bg-white rounded-3xl border border-black/5 shadow-sm p-8">
    <form action="{{ route('admin.classes.update', $labClass) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')
        
        <div>
            <label class="block text-sm font-semibold text-black/80 ml-1 mb-2">Nama Kelas</label>
            <input type="text" name="name" value="{{ old('name', $labClass->name) }}" required class="w-full px-4 py-3 rounded-2xl bg-white border border-black/10 focus:border-[#4A5D4E] focus:outline-none focus:ring-4 focus:ring-[#4A5D4E]/10 transition-all">
            @error('name') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center space-x-3 pt-4">
            <button type="submit" class="px-6 py-3 rounded-2xl bg-[#4A5D4E] text-[#F7F5F0] font-semibold hover:bg-[#38473b] transition-all">
                Perbarui
            </button>
            <a href="{{ route('admin.classes.index') }}" class="px-6 py-3 rounded-2xl bg-gray-100 text-black/60 font-semibold hover:bg-gray-200 transition-all">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
