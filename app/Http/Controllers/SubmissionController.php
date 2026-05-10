<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\LabClass;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function create()
    {
        $subjects = Subject::all();
        $classes = LabClass::all();
        return view('welcome', compact('subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'module_id' => 'required|exists:modules,id',
            'lab_class_id' => 'required|exists:lab_classes,id',
            'student_name' => 'required|string|max:255',
            'student_nim' => 'required|string|max:50',
            'assignment_file' => 'required|file|max:20480', // 20 MB max
        ], [
            'assignment_file.max' => 'Ukuran file maksimal adalah 20MB.',
            'assignment_file.required' => 'File tugas wajib diunggah.'
        ]);

        $file = $request->file('assignment_file');
        
        $subject = Subject::find($request->subject_id);
        $module = \App\Models\Module::find($request->module_id);
        $labClass = \App\Models\LabClass::find($request->lab_class_id);

        
        if ($module->is_expired) {
            return redirect()->back()->withInput()
                ->with('error', "Pengumpulan tugas untuk modul \"{$module->name}\" sudah ditutup karena tenggat waktu telah berakhir.");
        }
        
        // Format: NIM_Module_Subject_Time.ext
        $fileName = $request->student_nim . '_' . str_replace(' ', '', $module->name) . '_' . str_replace(' ', '', $subject->code ?? $subject->name) . '_' . time() . '.' . $file->getClientOriginalExtension();

        try {
            
            $folderPath = trim($subject->name) . '/' . trim($module->name) . '/' . trim($labClass->name);
            $path = Storage::disk('google')->putFileAs($folderPath, $file, $fileName);

            $googleDriveId = null;
            try {
                $metadata = Storage::disk('google')->getMetadata($path);
                $googleDriveId = $metadata['path'] ?? null;
            } catch (\Exception $e) {
                $googleDriveId = $path;
            }
            
            Submission::create([
                'subject_id' => $request->subject_id,
                'module_id' => $request->module_id,
                'lab_class_id' => $request->lab_class_id,
                'student_name' => $request->student_name,
                'student_nim' => $request->student_nim,
                'file_name' => $fileName,
                'file_path' => $path,
                'google_drive_id' => $googleDriveId,
                'file_size' => $file->getSize(),
            ]);

            return redirect()->route('submissions.create')->with('success', 'Tugas berhasil dikirim!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengunggah ke Google Drive: Pastikan konfigurasi .env sudah benar. ' . $e->getMessage());
        }
    }
}
