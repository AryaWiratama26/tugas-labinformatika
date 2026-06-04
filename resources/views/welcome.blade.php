<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('submission.page_title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;500;600;700&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Space Grotesk', sans-serif; background-color: #FFFFFF; color: #000000; }
        .font-display { font-family: 'Bricolage Grotesque', sans-serif; }
        .glass-panel { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .input-field { background-color: rgba(255, 255, 255, 0.6); border: 1px solid rgba(0, 0, 0, 0.1); transition: all 0.3s ease; }
        .input-field:focus { background-color: #fff; border-color: #193CB8; outline: none; box-shadow: 0 0 0 4px rgba(25, 60, 184, 0.1); }
        .btn-primary { background-color: #FFDF20; color: #000000; transition: all 0.3s ease; }
        .btn-primary:hover { background-color: #193CB8; color: #FFFFFF; transform: translateY(-2px); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 lg:p-8">

    <div class="max-w-6xl w-full flex flex-col">

        {{-- Language Toggle --}}
        <div class="flex justify-end mb-4">
            <div class="inline-flex items-center bg-white rounded-full border border-black/5 shadow-sm">
                <form action="{{ route('lang.switch') }}" method="POST" class="contents">
                    @csrf
                    <input type="hidden" name="locale" value="id">
                    <button type="submit"
                       class="px-3 py-1.5 text-xs font-semibold transition-all rounded-l-full cursor-pointer {{ app()->getLocale() === 'id' ? 'bg-[#193CB8] text-white' : 'text-black/40 hover:text-black' }}">
                        ID
                    </button>
                </form>
                <form action="{{ route('lang.switch') }}" method="POST" class="contents">
                    @csrf
                    <input type="hidden" name="locale" value="en">
                    <button type="submit"
                       class="px-3 py-1.5 text-xs font-semibold transition-all rounded-r-full cursor-pointer {{ app()->getLocale() === 'en' ? 'bg-[#193CB8] text-white' : 'text-black/40 hover:text-black' }}">
                        EN
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-center">

        <!-- Left Column: Branding -->
        <div class="lg:col-span-5 flex flex-col space-y-8 items-center lg:items-start text-center lg:text-left">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center border border-black/5">
                    <img src="{{ asset('images/logo-upb.png') }}" alt="Logo UPB" class="w-10 h-10 object-contain">
                </div>
                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center border border-black/5">
                    <img src="{{ asset('images/simbaft.png') }}" alt="Logo SIMBA" class="w-10 h-10 object-contain">
                </div>
            </div>

            <div class="space-y-4">
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-white/60 rounded-full border border-black/5 text-sm font-medium">
                    <span>{{ __('submission.university') }}</span>
                </div>
                <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-semibold leading-tight tracking-tight">
                    {{ __('submission.heading') }} <br class="hidden lg:block"> <span class="text-[#193CB8] italic font-normal">{{ __('submission.heading_highlight') }}</span>
                </h1>
                <p class="text-base lg:text-lg text-black/60 max-w-md leading-relaxed pt-2 mx-auto lg:mx-0">
                    {{ __('submission.description') }}
                </p>
            </div>

            <div class="flex items-center space-x-4 pt-4">
                <div class="flex -space-x-3">
                    <div class="w-10 h-10 rounded-full border-2 border-[#FFFFFF] bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500"><i class="ph ph-user"></i></div>
                    <div class="w-10 h-10 rounded-full border-2 border-[#FFFFFF] bg-gray-300 flex items-center justify-center text-xs font-bold text-gray-600"><i class="ph ph-user"></i></div>
                    <div class="w-10 h-10 rounded-full border-2 border-[#FFFFFF] bg-gray-400 flex items-center justify-center text-xs font-bold text-white"><i class="ph ph-users"></i></div>
                </div>
                <span class="text-sm font-medium text-black/50">{{ __('submission.accessed_by') }}</span>
            </div>
        </div>

        <!-- Right Column: Form -->
        <div class="lg:col-span-7">
            <div class="glass-panel p-6 md:p-8 lg:p-10 rounded-3xl lg:rounded-[2rem] shadow-xl shadow-black/5 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/40 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>

                @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl flex items-start space-x-3">
                    <i class="ph-fill ph-check-circle text-2xl text-green-500 mt-0.5"></i>
                    <div>
                        <h4 class="font-semibold font-display text-lg">{{ __('submission.success_title') }}</h4>
                        <p class="text-sm opacity-90">{{ session('success') }}</p>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-start space-x-3">
                    <i class="ph-fill ph-warning-circle text-2xl text-red-500 mt-0.5"></i>
                    <div>
                        <h4 class="font-semibold font-display text-lg">{{ __('submission.error_title') }}</h4>
                        <p class="text-sm opacity-90">{{ session('error') }}</p>
                    </div>
                </div>
                @endif

                <form id="submission-form" action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 relative z-10">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Lengkap -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-black/80 ml-1">{{ __('submission.label_name') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph ph-user text-lg text-black/40"></i>
                                </div>
                                <input type="text" name="student_name" value="{{ old('student_name') }}" required placeholder="{{ __('submission.placeholder_name') }}" class="w-full pl-11 pr-4 py-3 rounded-2xl input-field text-sm">
                            </div>
                            @error('student_name') <p class="text-red-500 text-xs ml-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- NIM -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-black/80 ml-1">{{ __('submission.label_nim') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph ph-identification-card text-lg text-black/40"></i>
                                </div>
                                <input type="text" name="student_nim" value="{{ old('student_nim') }}" required placeholder="{{ __('submission.placeholder_nim') }}" class="w-full pl-11 pr-4 py-3 rounded-2xl input-field text-sm">
                            </div>
                            @error('student_nim') <p class="text-red-500 text-xs ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Mata Kuliah -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-black/80 ml-1">{{ __('submission.label_subject') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph ph-book-open-text text-lg text-black/40"></i>
                                </div>
                                <select name="subject_id" id="subject-select" required class="w-full pl-11 pr-10 py-3 rounded-2xl input-field text-sm appearance-none cursor-pointer">
                                    <option value="" disabled selected>{{ __('submission.placeholder_subject') }}</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="ph ph-caret-down text-black/40"></i>
                                </div>
                            </div>
                            @error('subject_id') <p class="text-red-500 text-xs ml-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Modul -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-black/80 ml-1">{{ __('submission.label_module') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph ph-files text-lg text-black/40"></i>
                                </div>
                                <select name="module_id" id="module-select" disabled required class="w-full pl-11 pr-10 py-3 rounded-2xl input-field text-sm appearance-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="" disabled selected>{{ __('submission.placeholder_module') }}</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="ph ph-caret-down text-black/40"></i>
                                </div>
                            </div>
                            @error('module_id') <p class="text-red-500 text-xs ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kelas -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-black/80 ml-1">{{ __('submission.label_class') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph ph-users-three text-lg text-black/40"></i>
                                </div>
                                <select name="lab_class_id" required class="w-full pl-11 pr-10 py-3 rounded-2xl input-field text-sm appearance-none cursor-pointer">
                                    <option value="" disabled selected>{{ __('submission.placeholder_class') }}</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('lab_class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="ph ph-caret-down text-black/40"></i>
                                </div>
                            </div>
                            @error('lab_class_id') <p class="text-red-500 text-xs ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Keterangan Modul Container -->
                    <div id="module-description-container" class="hidden p-4 rounded-2xl bg-blue-50/50 border border-blue-100 text-sm">
                        <div class="flex items-start space-x-3 text-blue-800">
                            <i class="ph-fill ph-info text-xl mt-0.5 text-blue-500"></i>
                            <div>
                                <p class="font-semibold mb-1">{{ __('submission.task_info') }}</p>
                                <p id="module-description-text" class="text-blue-900/80 leading-relaxed"></p>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="space-y-2 pt-2">
                        <label class="block text-sm font-semibold text-black/80 ml-1">{{ __('submission.label_file') }}</label>
                        <div class="relative group cursor-pointer" id="upload-zone">
                            <input type="file" name="assignment_file" id="file-upload" required
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                   onchange="updateFileName(this)">

                            {{-- State: Belum ada file --}}
                            <div id="upload-empty"
                                 class="w-full p-8 border-2 border-dashed border-black/15 rounded-3xl bg-white/30
                                        group-hover:bg-white/60 group-hover:border-[#193CB8]/50
                                        transition-all text-center flex flex-col items-center justify-center space-y-3">
                                <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center text-[#193CB8] group-hover:scale-110 transition-transform">
                                    <i class="ph ph-upload-simple text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-black/80">{{ __('submission.upload_click') }}</p>
                                    <p class="text-xs text-black/40 mt-1">{{ __('submission.upload_max') }}</p>
                                </div>
                            </div>

                            {{-- State: File sudah dipilih --}}
                            <div id="upload-ready" class="hidden w-full p-6 border-2 border-[#193CB8]/40 rounded-3xl bg-[#193CB8]/5 transition-all">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 w-14 h-14 rounded-full bg-[#193CB8] flex items-center justify-center shadow-md" id="check-circle">
                                        <svg id="check-svg" class="w-7 h-7 text-white" viewBox="0 0 28 28" fill="none">
                                            <path id="check-path" d="M6 14 L11 20 L22 9"
                                                  stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-dasharray="30" stroke-dashoffset="30"
                                                  style="transition: stroke-dashoffset 0.5s ease 0.1s"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-[#193CB8] text-sm truncate" id="ready-filename">—</p>
                                        <p class="text-xs text-black/50 mt-0.5" id="ready-filesize">—</p>
                                        <div class="flex items-center space-x-1 mt-1.5">
                                            <span class="inline-block w-2 h-2 rounded-full bg-[#193CB8] animate-pulse"></span>
                                            <span class="text-xs text-[#193CB8] font-medium">{{ __('submission.file_ready') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 relative z-20 pointer-events-none">
                                        <span class="text-xs text-black/40 underline underline-offset-2 pointer-events-auto cursor-pointer" onclick="document.getElementById('file-upload').click()">
                                            {{ __('submission.file_replace') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('assignment_file') <p class="text-red-500 text-xs ml-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" id="submit-btn" class="w-full py-4 rounded-2xl font-semibold btn-primary flex items-center justify-center space-x-2 text-base transition-all">
                            <span id="btn-text">{{ __('submission.submit') }}</span>
                            <i class="ph ph-paper-plane-tilt text-lg" id="btn-icon"></i>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

    {{-- Upload Loading Overlay --}}
    <div id="upload-overlay" class="fixed inset-0 z-50 hidden items-center justify-center" style="background: rgba(20,20,20,0.7); backdrop-filter: blur(6px);">
        <div class="bg-white rounded-3xl p-10 mx-4 max-w-sm w-full shadow-2xl text-center">
            <div class="relative w-24 h-24 mx-auto mb-6">
                <svg class="w-24 h-24" id="spinner-svg" viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" style="animation: spin 1.2s linear infinite;">
                    <circle cx="48" cy="48" r="42" stroke="#e5e5e5" stroke-width="6"/>
                    <path d="M48 6 A42 42 0 0 1 90 48" stroke="#193CB8" stroke-width="6" stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="ph ph-cloud-arrow-up text-4xl text-[#193CB8]" id="upload-icon"></i>
                </div>
            </div>
            <h3 class="font-display text-xl font-bold text-black/80 mb-2" id="overlay-title">{{ __('submission.uploading_title') }}</h3>
            <p class="text-sm text-black/50 mb-6" id="overlay-subtitle">{{ __('submission.uploading_subtitle') }}</p>
            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                <div id="progress-bar" class="h-2 bg-[#193CB8] rounded-full transition-all duration-700 ease-out" style="width: 5%"></div>
            </div>
            <p class="text-xs text-black/30 mt-2" id="progress-text">{{ __('submission.uploading_progress') }}</p>
        </div>
    </div>

    <style>
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>

    {{-- Embedded JS Translations --}}
    @php
        $jsLang = [
            'loading_modules' => __('submission.loading_modules'),
            'no_modules' => __('submission.no_modules'),
            'failed_modules' => __('submission.failed_modules'),
            'select_module' => __('submission.select_module'),
            'module_closed' => __('submission.module_closed'),
            'module_deadline' => __('submission.module_deadline'),
            'module_expired_msg' => __('submission.module_expired_msg'),
            'module_img_alt' => __('submission.module_img_alt'),
            'stage_validating' => __('submission.stage_validating'),
            'stage_uploading' => __('submission.stage_uploading'),
            'stage_saving' => __('submission.stage_saving'),
            'stage_almost' => __('submission.stage_almost'),
        ];
    @endphp

    <script>
        var __lang = @json($jsLang);

        function formatBytes(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }

        function updateFileName(input) {
            const emptyZone = document.getElementById('upload-empty');
            const readyZone = document.getElementById('upload-ready');

            if (input.files && input.files.length > 0) {
                const file = input.files[0];

                document.getElementById('ready-filename').textContent = file.name;
                document.getElementById('ready-filesize').textContent = formatBytes(file.size);

                emptyZone.classList.add('hidden');
                readyZone.classList.remove('hidden');

                setTimeout(() => {
                    const path = document.getElementById('check-path');
                    if (path) path.style.strokeDashoffset = '0';
                }, 50);
            } else {
                emptyZone.classList.remove('hidden');
                readyZone.classList.add('hidden');
                const path = document.getElementById('check-path');
                if (path) path.style.strokeDashoffset = '30';
            }
        }

        // ── Upload Loading Overlay ───────────────────────────────────────
        const submissionForm = document.getElementById('submission-form');
        const overlay        = document.getElementById('upload-overlay');
        const progressBar    = document.getElementById('progress-bar');
        const progressText   = document.getElementById('progress-text');
        const overlayTitle   = document.getElementById('overlay-title');
        const overlaySubtitle = document.getElementById('overlay-subtitle');
        const uploadIcon     = document.getElementById('upload-icon');

        if (submissionForm) {
            submissionForm.addEventListener('submit', function(e) {
                const moduleVal   = document.getElementById('module-select').value;
                const fileInput   = document.querySelector('input[name="assignment_file"]');
                const studentName = document.querySelector('input[name="student_name"]').value.trim();
                const studentNim  = document.querySelector('input[name="student_nim"]').value.trim();
                const classVal    = document.querySelector('select[name="lab_class_id"]').value;

                if (!moduleVal || !fileInput.files.length || !studentName || !studentNim || !classVal) {
                    return;
                }

                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
                document.body.style.overflow = 'hidden';

                const stages = [
                    { pct: 20,  delay: 600,  text: __lang.stage_validating },
                    { pct: 45,  delay: 1800, text: __lang.stage_uploading },
                    { pct: 70,  delay: 4000, text: __lang.stage_saving },
                    { pct: 90,  delay: 6500, text: __lang.stage_almost },
                ];

                stages.forEach(stage => {
                    setTimeout(() => {
                        progressBar.style.width = stage.pct + '%';
                        progressText.textContent = stage.pct + '%';
                        overlaySubtitle.textContent = stage.text;
                    }, stage.delay);
                });
            });
        }

        // Module AJAX Logic
        const subjectSelect = document.getElementById('subject-select');
        const moduleSelect = document.getElementById('module-select');
        const moduleDescContainer = document.getElementById('module-description-container');
        const moduleDescText = document.getElementById('module-description-text');
        let modulesData = [];

        async function loadModules(subjectId, preSelectModuleId = null) {
            moduleSelect.innerHTML = '<option value="" disabled selected>' + __lang.loading_modules + '</option>';
            moduleSelect.disabled = true;
            moduleDescContainer.classList.add('hidden');

            try {
                const response = await fetch(`/api/subjects/${subjectId}/modules`);
                modulesData = await response.json();

                moduleSelect.innerHTML = '<option value="" disabled selected>' + __lang.select_module + '</option>';
                if (modulesData.length > 0) {
                    modulesData.forEach(module => {
                        const option = document.createElement('option');
                        option.value = module.id;
                        if (module.is_expired) {
                            option.textContent = module.name + ' ' + __lang.module_closed;
                            option.disabled = true;
                        } else {
                            option.textContent = module.name + (module.deadline ? ' — ' + __lang.module_deadline + ' ' + module.deadline : '');
                        }
                        if (preSelectModuleId && module.id == preSelectModuleId) {
                            option.selected = true;
                        }
                        moduleSelect.appendChild(option);
                    });
                    moduleSelect.disabled = false;

                    if (preSelectModuleId) {
                        moduleSelect.dispatchEvent(new Event('change'));
                    }
                } else {
                    moduleSelect.innerHTML = '<option value="" disabled selected>' + __lang.no_modules + '</option>';
                }
            } catch (error) {
                console.error('Error fetching modules:', error);
                moduleSelect.innerHTML = '<option value="" disabled selected>' + __lang.failed_modules + '</option>';
            }
        }

        subjectSelect.addEventListener('change', function() {
            loadModules(this.value);
        });

        @if(old('subject_id'))
        loadModules({{ old('subject_id') }}, {{ old('module_id', 'null') }});
        @endif

        moduleSelect.addEventListener('change', function() {
            const selectedModule = modulesData.find(m => m.id == this.value);
            moduleDescContainer.classList.add('hidden');

            if (selectedModule) {
                if (selectedModule.is_expired) {
                    moduleDescText.innerHTML = '<span class="text-red-600 font-semibold">' + __lang.module_expired_msg + '</span>';
                    moduleDescContainer.classList.remove('hidden');
                    moduleDescContainer.classList.remove('bg-blue-50/50', 'border-blue-100');
                    moduleDescContainer.classList.add('bg-red-50/50', 'border-red-100');
                } else if (selectedModule.description || selectedModule.image_url) {
                    let content = '';
                    if (selectedModule.description) {
                        content += selectedModule.description;
                    }
                    if (selectedModule.deadline) {
                        content += '<br><span class="text-blue-600 text-xs mt-1 inline-block">⏰ ' + __lang.module_deadline + ' ' + selectedModule.deadline + '</span>';
                    }
                    if (selectedModule.image_url) {
                        content += '<div class="mt-3 pt-3 border-t border-blue-100"><img src="' + selectedModule.image_url + '" alt="' + __lang.module_img_alt + '" class="rounded-xl max-h-64 w-full object-contain bg-white border border-blue-100"></div>';
                    }
                    moduleDescText.innerHTML = content;
                    moduleDescContainer.classList.remove('hidden');
                    moduleDescContainer.classList.remove('bg-red-50/50', 'border-red-100');
                    moduleDescContainer.classList.add('bg-blue-50/50', 'border-blue-100');
                }
            }
        });
    </script>
</body>
</html>