@extends('layouts.admin')

@section('title', 'Kelola Mata Kuliah')

@section('content')
<div class="flex flex-col space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-black/60">Atur mata kuliah yang akan muncul di dropdown form pengumpulan tugas.</p>
        <a href="{{ route('admin.subjects.create') }}" class="px-4 py-2 bg-[#4A5D4E] text-white rounded-xl font-medium text-sm hover:bg-[#38473b] transition-all flex items-center space-x-2">
            <i class="ph ph-plus"></i>
            <span>Tambah Matkul</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-black/5 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 text-black/50 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium">Nama Mata Kuliah</th>
                    <th class="px-6 py-4 font-medium">Kode</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse($subjects as $subject)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-black/80">{{ $subject->name }}</td>
                    <td class="px-6 py-4 text-black/60">{{ $subject->code ?? '-' }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.subjects.edit', $subject) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-all"><i class="ph ph-pencil-simple text-lg"></i></a>
                            <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus mata kuliah ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all"><i class="ph ph-trash text-lg"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-8 text-center text-black/40">Data mata kuliah masih kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
