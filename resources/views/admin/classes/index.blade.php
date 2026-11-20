@extends('layouts.admin')

@section('title', 'Kelola Kelas Lab')

@section('content')
<div class="flex flex-col space-y-5">
    <div class="flex flex-wrap justify-between items-center gap-3">
        <p class="text-black/60">Atur kelas lab yang muncul di dropdown form pengumpulan tugas.</p>
        <a href="{{ route('admin.classes.create') }}" class="px-4 py-2 bg-[#193CB8] text-white rounded-xl font-medium text-sm hover:bg-[#FFDF20] hover:text-black transition-all flex items-center space-x-2">
            <i class="ph ph-plus"></i>
            <span>Tambah Kelas</span>
        </a>
    </div>

    @php
        $filterCount = (request()->filled('search') ? 1 : 0)
            + ((request()->filled('visibility') && request('visibility') !== 'visible') ? 1 : 0);
        $filterOpen = $filterCount > 0;
    @endphp
    <div class="bg-white rounded-3xl border border-black/5 shadow-sm" data-filter-panel data-filter-key="classes" data-filter-active="{{ $filterCount > 0 ? '1' : '0' }}">
        <button type="button" data-filter-toggle aria-expanded="{{ $filterOpen ? 'true' : 'false' }}" class="w-full flex items-center justify-between p-5 text-left hover:bg-black/[0.02] rounded-3xl transition-all">
            <span class="flex items-center gap-2">
                <i class="ph ph-funnel text-[#193CB8]"></i>
                <span class="font-display font-semibold">Filter</span>
                @if($filterCount > 0)
                <span class="text-xs font-bold text-white bg-[#193CB8] rounded-full px-2 py-0.5">{{ $filterCount }} aktif</span>
                @endif
            </span>
            <i class="ph ph-caret-down text-black/40 transition-transform {{ $filterOpen ? 'rotate-180' : '' }}" data-filter-chevron></i>
        </button>
        <div data-filter-body class="grid transition-all duration-200 {{ $filterOpen ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0' }}">
            <div class="overflow-hidden">
        <form method="GET" action="{{ route('admin.classes.index') }}" class="flex flex-wrap gap-3 items-end px-5 pb-5">
            <div class="flex-1 min-w-[220px]">
                <label for="f-search" class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Cari Kelas</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-magnifying-glass text-black/40"></i>
                    </div>
                    <input id="f-search" type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama kelas..." class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10 transition-all">
                </div>
            </div>
            <div class="min-w-[150px]">
                <label for="f-visibility" class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Status Tampil</label>
                <select id="f-visibility" name="visibility" class="w-full px-3 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10 cursor-pointer bg-white">
                    <option value="visible" {{ request('visibility', 'visible') === 'visible' ? 'selected' : '' }}>Ditampilkan</option>
                    <option value="hidden" {{ request('visibility') === 'hidden' ? 'selected' : '' }}>Diarsipkan</option>
                    <option value="all" {{ request('visibility') === 'all' ? 'selected' : '' }}>Semua status</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-[#193CB8] text-white text-sm font-semibold rounded-xl hover:bg-[#FFDF20] hover:text-black transition-all">Cari</button>
            @if(request()->filled('search') || (request()->filled('visibility') && request('visibility') !== 'visible'))
            <a href="{{ route('admin.classes.index') }}" class="px-4 py-2.5 bg-gray-100 text-black/60 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-all">Reset</a>
            @endif
        </form>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-black/5 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-black/5 flex items-center justify-between gap-3">
            <h3 class="font-display font-semibold">Daftar Kelas Lab</h3>
            <p class="text-sm text-black/40 whitespace-nowrap">
                {{ $classes->count() }} data
                @if(request('visibility', 'visible') === 'visible' && $hiddenCount > 0)
                    · <a href="{{ route('admin.classes.index', array_merge(request()->query(), ['visibility' => 'hidden'])) }}" class="text-[#193CB8] hover:underline">{{ $hiddenCount }} diarsipkan</a>
                @endif
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[560px]">
                <thead>
                    <tr class="bg-gray-50/50 text-black/50 text-xs uppercase tracking-wider">
                        <th class="px-4 py-4 w-10"><input type="checkbox" id="bulkCheckAll" class="w-4 h-4 rounded accent-[#193CB8] cursor-pointer" title="Pilih semua"></th>
                        <th class="px-4 py-4 font-medium">Nama Kelas</th>
                        <th class="px-4 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse($classes as $labClass)
                    <tr class="hover:bg-gray-50/50 transition-colors {{ $labClass->is_hidden ? 'bg-black/[0.02]' : '' }}">
                        <td class="px-4 py-4"><input type="checkbox" class="js-bulk-check w-4 h-4 rounded accent-[#193CB8] cursor-pointer" value="{{ $labClass->id }}" title="Pilih {{ $labClass->name }}"></td>
                        <td class="px-4 py-4 font-semibold {{ $labClass->is_hidden ? 'text-black/50' : 'text-black/80' }}">
                            {{ $labClass->name }}
                            @if($labClass->is_hidden)
                                <span class="ml-1 px-2 py-0.5 text-[11px] font-semibold rounded-md bg-black/10 text-black/50 align-middle">Diarsipkan</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <a href="{{ route('admin.classes.edit', $labClass) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-all" title="Ubah"><i class="ph ph-pencil-simple text-lg"></i></a>
                                <form action="{{ route('admin.classes.bulk') }}" method="POST" class="inline"
                                      @if(!$labClass->is_hidden)
                                      data-confirm="Arsipkan kelas ini?"
                                      data-confirm-text="Kelas hilang dari form mahasiswa sampai kamu tampilkan lagi."
                                      data-confirm-button="Ya, arsipkan"
                                      data-confirm-icon="question"
                                      @endif>
                                    @csrf
                                    <input type="hidden" name="action" value="{{ $labClass->is_hidden ? 'unhide' : 'hide' }}">
                                    <input type="hidden" name="ids[]" value="{{ $labClass->id }}">
                                    <button type="submit" class="p-2 text-black/40 hover:bg-black/5 rounded-lg transition-all" title="{{ $labClass->is_hidden ? 'Tampilkan lagi di form mahasiswa' : 'Arsipkan, sembunyikan dari form mahasiswa' }}">
                                        <i class="ph {{ $labClass->is_hidden ? 'ph-eye' : 'ph-eye-slash' }} text-lg"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.classes.destroy', $labClass) }}" method="POST" class="inline"
                                      data-confirm="Hapus kelas ini?"
                                      data-confirm-text="Data pengumpulan tugas pada kelas ini ikut terhapus permanen!"
                                      data-confirm-button="Ya, hapus">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Hapus"><i class="ph ph-trash text-lg"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="w-12 h-12 rounded-full bg-[#193CB8]/10 flex items-center justify-center mx-auto mb-3">
                                <i class="ph ph-users-three text-xl text-[#193CB8]"></i>
                            </div>
                            @if(request()->filled('search'))
                                <p class="text-sm font-medium">Tidak ketemu "{{ request('search') }}"</p>
                                <p class="text-xs text-black/40 mt-1 mb-4">Coba kata kunci lain.</p>
                                <a href="{{ route('admin.classes.index') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 bg-gray-100 text-black/60 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-all">
                                    <i class="ph ph-arrow-counter-clockwise"></i><span>Reset Pencarian</span>
                                </a>
                            @else
                                <p class="text-sm font-medium">Belum ada kelas</p>
                                <p class="text-xs text-black/40 mt-1 mb-4">Tambahkan kelas lab agar muncul di form pengumpulan.</p>
                                <a href="{{ route('admin.classes.create') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 bg-[#193CB8] text-white text-sm font-semibold rounded-xl hover:bg-[#FFDF20] hover:text-black transition-all">
                                    <i class="ph ph-plus"></i><span>Tambah Kelas</span>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div data-bulk-bar data-bulk-form="#bulkForm" class="hidden fixed bottom-4 left-4 right-4 sm:left-1/2 sm:right-auto sm:-translate-x-1/2 z-40 bg-black text-white rounded-2xl shadow-2xl px-4 py-3 flex flex-wrap items-center gap-2">
    <span class="text-sm font-semibold mr-1"><span data-bulk-count>0</span> dipilih</span>
    @if(request('visibility') === 'hidden')
        <button type="button" data-bulk-go="unhide" class="px-3.5 py-2 text-sm font-semibold rounded-xl bg-white/15 hover:bg-white/25 transition-all">Tampilkan</button>
    @else
        <button type="button" data-bulk-go="hide"
                data-confirm-title="Arsipkan {n} kelas?"
                data-confirm-text="Kelas hilang dari form mahasiswa sampai ditampilkan lagi."
                data-confirm-button="Ya, arsipkan"
                data-confirm-icon="question"
                class="px-3.5 py-2 text-sm font-semibold rounded-xl bg-white/15 hover:bg-white/25 transition-all">Arsipkan</button>
    @endif
    <button type="button" data-bulk-go="delete"
            data-confirm-title="Hapus {n} kelas?"
            data-confirm-text="Data pengumpulan tugas pada kelas ini ikut terhapus permanen!"
            data-confirm-button="Ya, hapus semua"
            class="px-3.5 py-2 text-sm font-semibold rounded-xl bg-red-500 hover:bg-red-600 transition-all">Hapus</button>
    <button type="button" data-bulk-clear class="ml-auto p-2 rounded-xl hover:bg-white/15 transition-all" title="Batalkan pilihan">
        <i class="ph ph-x text-lg"></i>
    </button>
</div>

<form id="bulkForm" method="POST" action="{{ route('admin.classes.bulk') }}" class="hidden">
    @csrf
    <input type="hidden" name="action" value="delete">
    <span data-bulk-ids></span>
</form>
@endsection
