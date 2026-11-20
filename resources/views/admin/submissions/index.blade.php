@extends('layouts.admin')

@section('title', 'Semua Tugas Masuk')

@section('content')
<div class="flex flex-col space-y-5">
    @php
        $sorts = \App\Http\Controllers\Admin\SubmissionController::sorts();
        $perPages = \App\Http\Controllers\Admin\SubmissionController::perPages();
        $gradedLabels = ['1' => 'Sudah dinilai', '0' => 'Belum dinilai'];
        $visibilityLabels = ['all' => 'Semua status', 'visible' => 'Ditampilkan', 'hidden' => 'Tersembunyi'];
        $activeChips = [];
        if (request()->filled('search')) $activeChips['search'] = 'Cari: '.request('search');
        if (request()->filled('subject_id') && ($s = $subjects->find(request('subject_id')))) $activeChips['subject_id'] = $s->name;
        if (request()->filled('module_id') && ($m = $modules->find(request('module_id')))) $activeChips['module_id'] = $m->name;
        if (request()->filled('lab_class_id') && ($c = $classes->find(request('lab_class_id')))) $activeChips['lab_class_id'] = $c->name;
        if (request()->filled('graded')) $activeChips['graded'] = $gradedLabels[request('graded')] ?? null;
        if (request()->filled('visibility') && request('visibility') !== 'visible') $activeChips['visibility'] = $visibilityLabels[request('visibility')] ?? null;
        if (request()->filled('sort') && request('sort') !== 'latest') $activeChips['sort'] = $sorts[request('sort')] ?? null;
        $activeChips = array_filter($activeChips);
        $filterOpen = count($activeChips) > 0;
    @endphp

    {{-- Filter card --}}
    <div class="bg-white rounded-3xl border border-black/5 shadow-sm" data-filter-panel data-filter-key="submissions" data-filter-active="{{ count($activeChips) > 0 ? '1' : '0' }}">
        <button type="button" data-filter-toggle aria-expanded="{{ $filterOpen ? 'true' : 'false' }}" class="w-full flex items-center justify-between p-5 text-left hover:bg-black/[0.02] rounded-3xl transition-all">
            <span class="flex items-center gap-2">
                <i class="ph ph-funnel text-[#193CB8]"></i>
                <span class="font-display font-semibold">Filter</span>
                @if(count($activeChips) > 0)
                <span class="text-xs font-bold text-white bg-[#193CB8] rounded-full px-2 py-0.5">{{ count($activeChips) }} aktif</span>
                @endif
            </span>
            <i class="ph ph-caret-down text-black/40 transition-transform {{ $filterOpen ? 'rotate-180' : '' }}" data-filter-chevron></i>
        </button>
        <div data-filter-body class="grid transition-all duration-200 {{ $filterOpen ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0' }}">
            <div class="overflow-hidden">
        <form method="GET" action="{{ route('admin.submissions.index') }}" id="filterForm" class="flex flex-wrap gap-3 items-end px-5 pb-5">
            <div class="flex-1 min-w-[180px]">
                <label for="f-search" class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Cari NIM / Nama</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-magnifying-glass text-black/40"></i>
                    </div>
                    <input id="f-search" type="text" name="search" value="{{ request('search') }}" placeholder="Ketik NIM atau Nama..." class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10 transition-all">
                </div>
            </div>
            <div class="min-w-[170px]">
                <label for="f-subject" class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Mata Kuliah</label>
                <select id="f-subject" name="subject_id" class="w-full px-3 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10 cursor-pointer bg-white">
                    <option value="">Semua Matkul</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[170px]">
                <label for="f-module" class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Modul</label>
                <select id="f-module" name="module_id" class="w-full px-3 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10 cursor-pointer bg-white">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod->id }}" data-subject="{{ $mod->subject_id }}" {{ request('module_id') == $mod->id ? 'selected' : '' }}>{{ $mod->subject->name ?? '' }} · {{ $mod->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label for="f-class" class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Kelas</label>
                <select id="f-class" name="lab_class_id" class="w-full px-3 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10 cursor-pointer bg-white">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('lab_class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[150px]">
                <label for="f-graded" class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Penilaian</label>
                <select id="f-graded" name="graded" class="w-full px-3 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10 cursor-pointer bg-white">
                    <option value="">Semua</option>
                    <option value="1" {{ request('graded') === '1' ? 'selected' : '' }}>Sudah dinilai</option>
                    <option value="0" {{ request('graded') === '0' ? 'selected' : '' }}>Belum dinilai</option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <label for="f-visibility" class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Status Tampil</label>
                <select id="f-visibility" name="visibility" class="w-full px-3 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10 cursor-pointer bg-white">
                    <option value="visible" {{ request('visibility', 'visible') === 'visible' ? 'selected' : '' }}>Ditampilkan</option>
                    <option value="hidden" {{ request('visibility') === 'hidden' ? 'selected' : '' }}>Tersembunyi</option>
                    <option value="all" {{ request('visibility') === 'all' ? 'selected' : '' }}>Semua status</option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <label for="f-sort" class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Urutkan</label>
                <select id="f-sort" name="sort" class="w-full px-3 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10 cursor-pointer bg-white">
                    @foreach($sorts as $key => $label)
                        <option value="{{ $key }}" {{ request('sort', 'latest') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[100px]">
                <label for="f-perpage" class="block text-xs font-semibold text-black/50 mb-1.5 ml-1">Per halaman</label>
                <select id="f-perpage" name="per_page" class="w-full px-3 py-2.5 text-sm rounded-xl border border-black/10 focus:border-[#193CB8] focus:outline-none focus:ring-4 focus:ring-[#193CB8]/10 cursor-pointer bg-white">
                    @foreach($perPages as $n)
                        <option value="{{ $n }}" {{ (int) request('per_page', 20) === $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 bg-[#193CB8] text-white text-sm font-semibold rounded-xl hover:bg-[#FFDF20] hover:text-black transition-all flex items-center space-x-1.5">
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
        </div>

        @if(count($activeChips))
        <div class="flex flex-wrap gap-2 px-5 pb-5 pt-4 border-t border-black/5">
            <span class="text-xs text-black/40 self-center mr-1">Filter aktif:</span>
            @foreach($activeChips as $key => $label)
                <a href="{{ route('admin.submissions.index', request()->except($key)) }}" class="inline-flex items-center space-x-1.5 pl-3 pr-2 py-1 text-xs font-semibold rounded-full bg-[#193CB8]/10 text-[#193CB8] hover:bg-[#193CB8]/20 transition-all" title="Hapus filter ini">
                    <span>{{ $label }}</span>
                    <i class="ph ph-x text-xs"></i>
                </a>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-3xl border border-black/5 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-black/5 flex items-center justify-between gap-3">
            <h3 class="font-display font-semibold">Daftar Pengumpulan Tugas</h3>
            <p class="text-sm text-black/40 whitespace-nowrap">
                {{ $submissions->total() }} data
                @if(request('visibility', 'visible') === 'visible' && $hiddenCount > 0)
                    · <a href="{{ route('admin.submissions.index', array_merge(request()->query(), ['visibility' => 'hidden'])) }}" class="text-[#193CB8] hover:underline">{{ $hiddenCount }} tersembunyi</a>
                @endif
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="bg-gray-50/50 text-black/50 text-xs uppercase tracking-wider">
                        <th class="px-4 py-4 w-10"><input type="checkbox" id="bulkCheckAll" class="w-4 h-4 rounded accent-[#193CB8] cursor-pointer" title="Pilih semua di halaman ini"></th>
                        <th class="px-4 py-4 font-medium">Praktikan</th>
                        <th class="px-4 py-4 font-medium">Mata Kuliah</th>
                        <th class="px-4 py-4 font-medium">Modul</th>
                        <th class="px-4 py-4 font-medium">Kelas</th>
                        <th class="px-4 py-4 font-medium">Nilai</th>
                        <th class="px-4 py-4 font-medium">Waktu</th>
                        <th class="px-4 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse($submissions as $submission)
                    <tr class="hover:bg-gray-50/50 transition-colors {{ $submission->is_hidden ? 'bg-black/[0.02]' : '' }}">
                        <td class="px-4 py-4"><input type="checkbox" class="js-bulk-check w-4 h-4 rounded accent-[#193CB8] cursor-pointer" value="{{ $submission->id }}" title="Pilih {{ $submission->student_name }}"></td>
                        <td class="px-4 py-4">
                            <p class="font-semibold text-black/80 text-sm {{ $submission->is_hidden ? 'text-black/50' : '' }}">{{ $submission->student_name }}</p>
                            <p class="text-xs text-black/40 font-mono">{{ $submission->student_nim }}</p>
                            @if($submission->is_hidden)
                                <span class="inline-block mt-1 px-2 py-0.5 text-[11px] font-semibold rounded-md bg-black/10 text-black/50">Tersembunyi</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-black/70">{{ $submission->subject->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-black/70">{{ $submission->module->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-black/70">{{ $submission->labClass->name ?? '-' }}</td>
                        <td class="px-4 py-4">
                            @if($submission->score !== null)
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-green-100 text-green-700" title="{{ $submission->feedback ?: 'Tanpa feedback' }}">{{ rtrim(rtrim(number_format($submission->score, 2), '0'), '.') }}</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-black/5 text-black/40">Belum dinilai</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-xs text-black/50 whitespace-nowrap">{{ $submission->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end space-x-1">
                                @if($submission->google_drive_id && str_starts_with($submission->google_drive_id, 'http'))
                                @php
                                    $previewUrl = str_replace(['/view', '/view?usp=drivesdk', '/view?usp=sharing'], '/preview', $submission->google_drive_id);
                                    if (str_contains($previewUrl, 'uc?') || str_contains($previewUrl, 'export=download')) {
                                        preg_match('/[?&]id=([^&]+)/', $previewUrl, $m);
                                        $previewUrl = !empty($m[1]) ? 'https://drive.google.com/file/d/' . $m[1] . '/preview' : $previewUrl;
                                    }
                                @endphp
                                <a href="{{ $previewUrl }}" target="_blank" class="p-2 text-[#193CB8] hover:bg-[#193CB8]/10 rounded-lg transition-all" title="Preview di Drive">
                                    <i class="ph ph-eye text-lg"></i>
                                </a>
                                @endif
                                <a href="{{ route('admin.submissions.edit', $submission) }}" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Beri nilai">
                                    <i class="ph ph-star text-lg"></i>
                                </a>
                                <form action="{{ route('admin.submissions.bulk') }}" method="POST" class="inline"
                                      @if(!$submission->is_hidden)
                                      data-confirm="Sembunyikan tugas ini?"
                                      data-confirm-text="Tugas hilang dari daftar sampai kamu tampilkan lagi."
                                      data-confirm-button="Ya, sembunyikan"
                                      data-confirm-icon="question"
                                      @endif>
                                    @csrf
                                    <input type="hidden" name="action" value="{{ $submission->is_hidden ? 'unhide' : 'hide' }}">
                                    <input type="hidden" name="ids[]" value="{{ $submission->id }}">
                                    <button type="submit" class="p-2 text-black/40 hover:bg-black/5 rounded-lg transition-all" title="{{ $submission->is_hidden ? 'Tampilkan lagi di daftar' : 'Sembunyikan dari daftar (arsip)' }}">
                                        <i class="ph {{ $submission->is_hidden ? 'ph-eye' : 'ph-eye-slash' }} text-lg"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.submissions.destroy', $submission) }}" method="POST" class="inline"
                                      data-confirm="Hapus tugas {{ $submission->student_name }}?"
                                      data-confirm-text="File di Google Drive ikut terhapus permanen dan tidak bisa dikembalikan!"
                                      data-confirm-button="Ya, hapus">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition-all" title="Hapus permanen">
                                        <i class="ph ph-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="w-12 h-12 rounded-full bg-[#193CB8]/10 flex items-center justify-center mx-auto mb-3">
                                <i class="ph ph-tray text-xl text-[#193CB8]"></i>
                            </div>
                            @if(count($activeChips))
                                <p class="text-sm font-medium">Tidak ada data yang sesuai filter</p>
                                <p class="text-xs text-black/40 mt-1 mb-4">Coba ubah kata kunci atau reset filter di atas.</p>
                                <a href="{{ route('admin.submissions.index') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 bg-gray-100 text-black/60 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-all">
                                    <i class="ph ph-arrow-counter-clockwise"></i><span>Reset Filter</span>
                                </a>
                            @else
                                <p class="text-sm font-medium">Belum ada tugas masuk</p>
                                <p class="text-xs text-black/40 mt-1">Bagikan link form pengumpulan ke praktikan untuk mulai menerima tugas.</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
        <div class="p-5 border-t border-black/5">
            {{ $submissions->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Bulk action bar --}}
<div data-bulk-bar data-bulk-form="#bulkForm" class="hidden fixed bottom-4 left-4 right-4 sm:left-1/2 sm:right-auto sm:-translate-x-1/2 sm:min-w-[560px] z-40 bg-black text-white rounded-2xl shadow-2xl px-4 py-3 flex flex-wrap items-center gap-2">
    <span class="text-sm font-semibold mr-1"><span data-bulk-count>0</span> dipilih</span>
    <button type="button" data-bulk-grade class="px-3.5 py-2 text-sm font-semibold rounded-xl bg-[#FFDF20] text-black hover:brightness-95 transition-all">Nilai</button>
    @if(request('visibility') === 'hidden')
        <button type="button" data-bulk-go="unhide" class="px-3.5 py-2 text-sm font-semibold rounded-xl bg-white/15 hover:bg-white/25 transition-all">Tampilkan</button>
    @else
        <button type="button" data-bulk-go="hide"
                data-confirm-title="Sembunyikan {n} tugas?"
                data-confirm-text="Tugas hilang dari daftar sampai ditampilkan lagi."
                data-confirm-button="Ya, sembunyikan"
                data-confirm-icon="question"
                class="px-3.5 py-2 text-sm font-semibold rounded-xl bg-white/15 hover:bg-white/25 transition-all">Sembunyikan</button>
    @endif
    <button type="button" data-bulk-export data-export-url="{{ route('admin.submissions.export') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl bg-white/15 hover:bg-white/25 transition-all">Export</button>
    <button type="button" data-bulk-go="delete"
            data-confirm-title="Hapus {n} tugas?"
            data-confirm-text="File di Google Drive ikut terhapus permanen dan tidak bisa dikembalikan!"
            data-confirm-button="Ya, hapus semua"
            class="px-3.5 py-2 text-sm font-semibold rounded-xl bg-red-500 hover:bg-red-600 transition-all">Hapus</button>
    <button type="button" data-bulk-clear class="ml-auto p-2 rounded-xl hover:bg-white/15 transition-all" title="Batalkan pilihan">
        <i class="ph ph-x text-lg"></i>
    </button>
</div>

<form id="bulkForm" method="POST" action="{{ route('admin.submissions.bulk') }}" class="hidden">
    @csrf
    <input type="hidden" name="action" value="">
    <input type="hidden" name="score" value="">
    <input type="hidden" name="feedback" value="">
    <span data-bulk-ids></span>
</form>
@endsection

@push('scripts')
<script>
(function () {
    var subjectSel = document.getElementById('f-subject');
    var moduleSel = document.getElementById('f-module');
    if (!subjectSel || !moduleSel) return;

    var allOptions = Array.prototype.slice.call(moduleSel.querySelectorAll('option[data-subject]')).map(function (o) {
        return { value: o.value, text: o.textContent, subject: o.getAttribute('data-subject'), selected: o.selected };
    });

    function renderModules(subjectId, keepSelected) {
        var current = keepSelected ? moduleSel.value : '';
        moduleSel.innerHTML = '';
        var placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Semua Modul';
        moduleSel.appendChild(placeholder);
        allOptions
            .filter(function (o) { return !subjectId || o.subject === subjectId; })
            .forEach(function (o) {
                var el = document.createElement('option');
                el.value = o.value;
                el.textContent = o.text;
                el.setAttribute('data-subject', o.subject);
                if (o.value === current) el.selected = true;
                moduleSel.appendChild(el);
            });
    }

    subjectSel.addEventListener('change', function () {
        if (!subjectSel.value) {
            renderModules('', false);
            return;
        }
        fetch("{{ url('/api/subjects') }}/" + subjectSel.value + '/modules', { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data) return;
                var current = moduleSel.value;
                moduleSel.innerHTML = '';
                var placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Semua Modul';
                moduleSel.appendChild(placeholder);
                (data.data || data).forEach(function (m) {
                    var el = document.createElement('option');
                    el.value = m.id;
                    el.textContent = m.name;
                    el.setAttribute('data-subject', subjectSel.value);
                    if (String(m.id) === String(current)) el.selected = true;
                    moduleSel.appendChild(el);
                });
            })
            .catch(function () { renderModules(subjectSel.value, true); });
    });

    var gradeBtn = document.querySelector('[data-bulk-grade]');
    var bulkForm = document.getElementById('bulkForm');
    if (gradeBtn && bulkForm) {
        gradeBtn.addEventListener('click', function () {
            var n = document.querySelectorAll('.js-bulk-check:checked').length;
            if (!n) return;
            if (typeof Swal === 'undefined') {
                var s = window.prompt('Nilai (0-100) untuk ' + n + ' tugas:');
                if (s === null) return;
                bulkForm.querySelector('input[name="action"]').value = 'grade';
                bulkForm.querySelector('input[name="score"]').value = s;
                bulkForm.submit();
                return;
            }
            Swal.fire({
                title: 'Nilai ' + n + ' tugas',
                html: '<input id="sw-score" type="number" min="0" max="100" step="0.01" placeholder="Nilai (0-100)" class="swal2-input">' +
                      '<textarea id="sw-feedback" placeholder="Feedback (opsional)" class="swal2-textarea"></textarea>',
                showCancelButton: true,
                confirmButtonColor: '#193CB8',
                confirmButtonText: 'Simpan nilai',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusConfirm: false,
                preConfirm: function () {
                    var score = document.getElementById('sw-score').value;
                    if (score === '' || isNaN(score) || score < 0 || score > 100) {
                        Swal.showValidationMessage('Isi nilai 0 sampai 100 dulu.');
                        return false;
                    }
                    return {
                        score: score,
                        feedback: document.getElementById('sw-feedback').value
                    };
                }
            }).then(function (result) {
                if (!result.isConfirmed) return;
                bulkForm.querySelector('input[name="action"]').value = 'grade';
                bulkForm.querySelector('input[name="score"]').value = result.value.score;
                bulkForm.querySelector('input[name="feedback"]').value = result.value.feedback;
                bulkForm.submit();
            });
        });
    }
})();
</script>
@endpush
