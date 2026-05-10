@extends('layouts.admin')

@section('title', 'Kelola Modul')

@section('content')
<div class="flex flex-col space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-black/60">Atur modul praktikum beserta deskripsi/keterangan tugasnya.</p>
        <a href="{{ route('admin.modules.create') }}" class="px-4 py-2 bg-[#4A5D4E] text-white rounded-xl font-medium text-sm hover:bg-[#38473b] transition-all flex items-center space-x-2">
            <i class="ph ph-plus"></i>
            <span>Tambah Modul</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-black/5 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 text-black/50 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium">Mata Kuliah</th>
                    <th class="px-6 py-4 font-medium">Nama Modul</th>
                    <th class="px-6 py-4 font-medium">Keterangan</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse($modules as $module)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 text-black/70">{{ $module->subject->name ?? '-' }}</td>
                    <td class="px-6 py-4 font-semibold text-black/80">{{ $module->name }}</td>
                    <td class="px-6 py-4 text-black/60 text-xs w-1/3">{{ Str::limit($module->description, 50) ?: '-' }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.modules.edit', $module) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-all"><i class="ph ph-pencil-simple text-lg"></i></a>
                            <form action="{{ route('admin.modules.destroy', $module) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus modul ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all"><i class="ph ph-trash text-lg"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-black/40">Data modul masih kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
