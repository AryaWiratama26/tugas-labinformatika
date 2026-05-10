<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumpulan Tugas Praktikum</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;500;600;700&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Space Grotesk', sans-serif; background-color: #F7F5F0; color: #1C1C1C; }
        .font-display { font-family: 'Bricolage Grotesque', sans-serif; }
        .glass-panel { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .input-field { background-color: rgba(255, 255, 255, 0.6); border: 1px solid rgba(0, 0, 0, 0.1); transition: all 0.3s ease; }
        .input-field:focus { background-color: #fff; border-color: #4A5D4E; outline: none; box-shadow: 0 0 0 4px rgba(74, 93, 78, 0.1); }
        .btn-primary { background-color: #4A5D4E; color: #F7F5F0; transition: all 0.3s ease; }
        .btn-primary:hover { background-color: #38473b; transform: translateY(-2px); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 lg:p-8">

    <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-center">
        
        <!-- Left Column: Branding -->
        <div class="lg:col-span-5 flex flex-col space-y-8 items-center lg:items-start text-center lg:text-left">
            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center border border-black/5">
                <img src="{{ asset('images/logo-upb.png') }}" alt="Logo UPB" class="w-10 h-10 object-contain">
            </div>
            
            <div class="space-y-4">
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-white/60 rounded-full border border-black/5 text-sm font-medium">
                    <span>Universitas Pelita Bangsa</span>
                </div>
                <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-semibold leading-tight tracking-tight">
                    Pengumpulan <br class="hidden lg:block"> <span class="text-[#4A5D4E] italic font-normal">Tugas Praktikum.</span>
                </h1>
                <p class="text-base lg:text-lg text-black/60 max-w-md leading-relaxed pt-2 mx-auto lg:mx-0">
                    Unggah file tugas Anda dengan aman. Pastikan data yang dimasukkan sesuai dengan identitas praktikan yang terdaftar.
                </p>
            </div>

            <div class="flex items-center space-x-4 pt-4">
                <div class="flex -space-x-3">
                    <div class="w-10 h-10 rounded-full border-2 border-[#F7F5F0] bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500"><i class="ph ph-user"></i></div>
                    <div class="w-10 h-10 rounded-full border-2 border-[#F7F5F0] bg-gray-300 flex items-center justify-center text-xs font-bold text-gray-600"><i class="ph ph-user"></i></div>
                    <div class="w-10 h-10 rounded-full border-2 border-[#F7F5F0] bg-gray-400 flex items-center justify-center text-xs font-bold text-white"><i class="ph ph-users"></i></div>
                </div>
                <span class="text-sm font-medium text-black/50">Diakses oleh ratusan praktikan.</span>
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
                        <h4 class="font-semibold font-display text-lg">Berhasil!</h4>
                        <p class="text-sm opacity-90">{{ session('success') }}</p>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-start space-x-3">
                    <i class="ph-fill ph-warning-circle text-2xl text-red-500 mt-0.5"></i>
                    <div>
                        <h4 class="font-semibold font-display text-lg">Gagal</h4>
                        <p class="text-sm opacity-90">{{ session('error') }}</p>
                    </div>
                </div>
                @endif

                <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 relative z-10">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Lengkap -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-black/80 ml-1">Nama Lengkap</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph ph-user text-lg text-black/40"></i>
                                </div>
                                <input type="text" name="student_name" value="{{ old('student_name') }}" required placeholder="John Doe" class="w-full pl-11 pr-4 py-3 rounded-2xl input-field text-sm">
                            </div>
                            @error('student_name') <p class="text-red-500 text-xs ml-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- NIM -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-black/80 ml-1">NIM</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph ph-identification-card text-lg text-black/40"></i>
                                </div>
                                <input type="text" name="student_nim" value="{{ old('student_nim') }}" required placeholder="312010..." class="w-full pl-11 pr-4 py-3 rounded-2xl input-field text-sm">
                            </div>
                            @error('student_nim') <p class="text-red-500 text-xs ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Mata Kuliah -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-black/80 ml-1">Mata Kuliah</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph ph-book-open-text text-lg text-black/40"></i>
                                </div>
                                <select name="subject_id" id="subject-select" required class="w-full pl-11 pr-10 py-3 rounded-2xl input-field text-sm appearance-none cursor-pointer">
                                    <option value="" disabled selected>Pilih Mata Kuliah...</option>
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
                            <label class="block text-sm font-semibold text-black/80 ml-1">Modul</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph ph-files text-lg text-black/40"></i>
                                </div>
                                <select name="module_id" id="module-select" disabled required class="w-full pl-11 pr-10 py-3 rounded-2xl input-field text-sm appearance-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="" disabled selected>Pilih Modul...</option>
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
                            <label class="block text-sm font-semibold text-black/80 ml-1">Kelas</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph ph-users-three text-lg text-black/40"></i>
                                </div>
                                <select name="lab_class_id" required class="w-full pl-11 pr-10 py-3 rounded-2xl input-field text-sm appearance-none cursor-pointer">
                                    <option value="" disabled selected>Pilih Kelas...</option>
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
                                <p class="font-semibold mb-1">Keterangan Tugas</p>
                                <p id="module-description-text" class="text-blue-900/80 leading-relaxed"></p>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="space-y-2 pt-2">
                        <label class="block text-sm font-semibold text-black/80 ml-1">File Tugas</label>
                        <div class="relative group cursor-pointer">
                            <input type="file" name="assignment_file" id="file-upload" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateFileName(this)">
                            <div class="w-full p-8 border-2 border-dashed border-black/15 rounded-3xl bg-white/30 group-hover:bg-white/60 group-hover:border-[#4A5D4E]/50 transition-all text-center flex flex-col items-center justify-center space-y-3">
                                <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center text-[#4A5D4E] group-hover:scale-110 transition-transform">
                                    <i class="ph ph-upload-simple text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-black/80" id="file-name-display">Klik untuk memilih file</p>
                                    <p class="text-xs text-black/40 mt-1">Maks. 20MB (PDF, ZIP, DOCX)</p>
                                </div>
                            </div>
                        </div>
                        @error('assignment_file') <p class="text-red-500 text-xs ml-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 rounded-2xl font-semibold btn-primary flex items-center justify-center space-x-2 text-base">
                            <span>Kirim Tugas Sekarang</span>
                            <i class="ph ph-paper-plane-tilt text-lg"></i>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const display = document.getElementById('file-name-display');
            if (input.files && input.files.length > 0) {
                display.textContent = input.files[0].name;
                display.classList.add('text-[#4A5D4E]');
            } else {
                display.textContent = 'Klik untuk memilih file';
                display.classList.remove('text-[#4A5D4E]');
            }
        }

        // Module AJAX Logic
        const subjectSelect = document.getElementById('subject-select');
        const moduleSelect = document.getElementById('module-select');
        const moduleDescContainer = document.getElementById('module-description-container');
        const moduleDescText = document.getElementById('module-description-text');
        let modulesData = [];

        subjectSelect.addEventListener('change', async function() {
            const subjectId = this.value;
            moduleSelect.innerHTML = '<option value="" disabled selected>Memuat modul...</option>';
            moduleSelect.disabled = true;
            moduleDescContainer.classList.add('hidden');

            try {
                const response = await fetch(`/api/subjects/${subjectId}/modules`);
                modulesData = await response.json();

                moduleSelect.innerHTML = '<option value="" disabled selected>Pilih Modul...</option>';
                if (modulesData.length > 0) {
                    modulesData.forEach(module => {
                        const option = document.createElement('option');
                        option.value = module.id;
                        if (module.is_expired) {
                            option.textContent = module.name + ' (Ditutup)';
                            option.disabled = true;
                        } else {
                            option.textContent = module.name + (module.deadline ? ' — Deadline: ' + module.deadline : '');
                        }
                        moduleSelect.appendChild(option);
                    });
                    moduleSelect.disabled = false;
                } else {
                    moduleSelect.innerHTML = '<option value="" disabled selected>Belum ada modul</option>';
                }
            } catch (error) {
                console.error('Error fetching modules:', error);
                moduleSelect.innerHTML = '<option value="" disabled selected>Gagal memuat modul</option>';
            }
        });

        moduleSelect.addEventListener('change', function() {
            const selectedModule = modulesData.find(m => m.id == this.value);
            moduleDescContainer.classList.add('hidden');

            if (selectedModule) {
                if (selectedModule.is_expired) {
                    moduleDescText.innerHTML = '<span class="text-red-600 font-semibold">⛔ Tenggat waktu telah berakhir! Modul ini sudah ditutup.</span>';
                    moduleDescContainer.classList.remove('hidden');
                    moduleDescContainer.classList.remove('bg-blue-50/50', 'border-blue-100');
                    moduleDescContainer.classList.add('bg-red-50/50', 'border-red-100');
                } else if (selectedModule.description || selectedModule.image_url) {
                    let content = '';
                    if (selectedModule.description) {
                        content += selectedModule.description;
                    }
                    if (selectedModule.deadline) {
                        content += '<br><span class="text-blue-600 text-xs mt-1 inline-block">⏰ Deadline: ' + selectedModule.deadline + '</span>';
                    }
                    if (selectedModule.image_url) {
                        content += '<div class="mt-3 pt-3 border-t border-blue-100"><img src="' + selectedModule.image_url + '" alt="Gambar Tugas" class="rounded-xl max-h-64 w-full object-contain bg-white border border-blue-100"></div>';
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
