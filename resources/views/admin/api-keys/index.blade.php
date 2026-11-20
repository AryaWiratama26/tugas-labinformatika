@extends('layouts.admin')

@section('title', 'API Keys')

@section('content')
<div class="flex flex-col space-y-6">

    @if(session('plain_key'))
    <div class="bg-[#193CB8] text-white rounded-3xl p-5 shadow-sm">
        <p class="text-sm opacity-80 mb-2">Salin key ini sekarang (tidak akan ditampilkan lagi):</p>
        <div class="flex items-stretch gap-2">
            <code class="flex-1 bg-black/20 rounded-xl px-4 py-3 font-mono text-sm break-all select-all">{{ session('plain_key') }}</code>
            <button type="button" data-copy="{{ session('plain_key') }}" class="px-4 rounded-xl bg-[#FFDF20] text-black text-sm font-semibold flex items-center space-x-1.5 hover:brightness-95 transition-all flex-shrink-0">
                <i class="ph ph-copy"></i><span class="hidden sm:inline">Salin</span>
            </button>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-3xl border border-black/5 shadow-sm p-5">
        <h3 class="font-display font-semibold mb-4">Buat API Key Baru</h3>
        <form method="POST" action="{{ route('admin.api-keys.store') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Nama Pemilik</label>
                <input type="text" name="name" required placeholder="cth: SIMBA-FT, HIMA, ..." class="w-full px-4 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10">
            </div>
            <div class="min-w-[180px]">
                <label class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Kedaluwarsa (opsional)</label>
                <input type="date" name="expires_at" class="w-full px-3 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-[#193CB8] text-white text-sm font-semibold rounded-xl hover:bg-[#FFDF20] hover:text-black transition-all">Buat Key</button>
        </form>
    </div>

    <div class="bg-white rounded-3xl border border-black/5 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-black/5 flex items-center justify-between">
            <h3 class="font-display font-semibold">Daftar API Key</h3>
            <span class="text-sm text-black/40">{{ $keys->total() }} key</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-black/50 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">Nama</th>
                        <th class="px-6 py-4 font-medium">Prefix</th>
                        <th class="px-6 py-4 font-medium">Terakhir Dipakai</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse($keys as $key)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 text-sm font-semibold">{{ $key->name }}</td>
                        <td class="px-6 py-4 text-xs font-mono text-black/60">{{ $key->prefix }}…</td>
                        <td class="px-6 py-4 text-xs text-black/50">{{ $key->last_used_at?->format('d M Y, H:i') ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($key->revoked_at)
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-red-100 text-red-600">Dicabut</span>
                            @elseif($key->isExpired())
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-orange-100 text-orange-600">Kedaluwarsa</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-green-100 text-green-700">Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                @if(!$key->revoked_at)
                                <form action="{{ route('admin.api-keys.revoke', $key) }}" method="POST"
                                      data-confirm="Cabut key {{ $key->name }}?"
                                      data-confirm-text="Aplikasi yang memakai key ini langsung terputus."
                                      data-confirm-button="Ya, cabut">
                                    @csrf
                                    <button class="p-2 text-orange-500 hover:bg-orange-50 rounded-lg" title="Cabut"><i class="ph ph-prohibit text-lg"></i></button>
                                </form>
                                @endif
                                <form action="{{ route('admin.api-keys.destroy', $key) }}" method="POST"
                                      data-confirm="Hapus permanen key {{ $key->name }}?"
                                      data-confirm-text="Key tidak bisa dikembalikan. Buat baru jika masih dibutuhkan."
                                      data-confirm-button="Ya, hapus">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-red-400 hover:bg-red-50 rounded-lg" title="Hapus"><i class="ph ph-trash text-lg"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="w-12 h-12 rounded-full bg-[#193CB8]/10 flex items-center justify-center mx-auto mb-3">
                                <i class="ph ph-key text-xl text-[#193CB8]"></i>
                            </div>
                            <p class="text-sm font-medium">Belum ada API key</p>
                            <p class="text-xs text-black/40 mt-1">Buat satu lewat form di atas untuk mulai berbagi data.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($keys->hasPages())
        <div class="p-5 border-t border-black/5">{{ $keys->links() }}</div>
        @endif
    </div>
</div>
@endsection
