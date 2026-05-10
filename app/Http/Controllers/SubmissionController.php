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
            'subject_id'      => 'required|exists:subjects,id',
            'module_id'       => 'required|exists:modules,id',
            'lab_class_id'    => 'required|exists:lab_classes,id',
            'student_name'    => 'required|string|max:255',
            'student_nim'     => 'required|string|max:50',
            'assignment_file' => 'required|file|max:20480',
        ], [
            'assignment_file.max'      => 'Ukuran file maksimal adalah 20MB.',
            'assignment_file.required' => 'File tugas wajib diunggah.',
        ]);

        $file     = $request->file('assignment_file');
        $subject  = Subject::findOrFail($request->subject_id);
        $module   = \App\Models\Module::findOrFail($request->module_id);
        $labClass = \App\Models\LabClass::findOrFail($request->lab_class_id);

        if ($module->is_expired) {
            return redirect()->back()->withInput()
                ->with('error', "Pengumpulan tugas untuk modul \"{$module->name}\" sudah ditutup karena tenggat waktu telah berakhir.");
        }

        $fileName = $request->student_nim
            . '_' . str_replace(' ', '', $module->name)
            . '_' . str_replace(' ', '', $subject->code ?? $subject->name)
            . '_' . time()
            . '.' . $file->getClientOriginalExtension();

        try {
            // Buat Google Drive client langsung (bypass adapter agar tidak buat folder duplikat)
            $client = new \Google\Client();
            $client->setClientId(config('filesystems.disks.google.clientId'));
            $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
            $client->refreshToken(config('filesystems.disks.google.refreshToken'));

            $service    = new \Google\Service\Drive($client);
            $rootFolder = config('filesystems.disks.google.folder');

            // Navigasi folder: cari dulu, buat hanya jika belum ada
            $subjectFolderId = $this->getOrCreateFolder($service, $rootFolder, $subject->name);
            $moduleFolderId  = $this->getOrCreateFolder($service, $subjectFolderId, $module->name);
            $classFolderId   = $this->getOrCreateFolder($service, $moduleFolderId, $labClass->name);

            // Upload file ke folder yang benar
            $fileMetadata = new \Google\Service\Drive\DriveFile([
                'name'    => $fileName,
                'parents' => [$classFolderId],
            ]);

            $uploaded = $service->files->create($fileMetadata, [
                'data'       => file_get_contents($file->getRealPath()),
                'mimeType'   => $file->getMimeType() ?? 'application/octet-stream',
                'uploadType' => 'multipart',
                'fields'     => 'id, webViewLink',
            ]);

            $fileId          = $uploaded->getId();
            $googleDriveUrl  = $uploaded->getWebViewLink()
                ? str_replace('/view', '/preview', $uploaded->getWebViewLink())
                : "https://drive.google.com/file/d/{$fileId}/preview";

            $folderPath = $subject->name . '/' . $module->name . '/' . $labClass->name . '/' . $fileName;

            Submission::create([
                'subject_id'      => $request->subject_id,
                'module_id'       => $request->module_id,
                'lab_class_id'    => $request->lab_class_id,
                'student_name'    => $request->student_name,
                'student_nim'     => $request->student_nim,
                'file_name'       => $fileName,
                'file_path'       => $folderPath,
                'google_drive_id' => $googleDriveUrl,
                'file_size'       => $file->getSize(),
            ]);

            return redirect()->route('submissions.create')->with('success', 'Tugas berhasil dikirim!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengunggah ke Google Drive: ' . $e->getMessage());
        }
    }

    /**
     * Cari folder berdasarkan nama di parent folder.
     * Jika belum ada, buat baru. Mencegah duplikasi folder.
     */
    private function getOrCreateFolder(\Google\Service\Drive $service, string $parentId, string $folderName): string
    {
        $safeName = str_replace("'", "\\'", $folderName);
        $query    = "name='{$safeName}' and '{$parentId}' in parents "
                  . "and mimeType='application/vnd.google-apps.folder' and trashed=false";

        $results = $service->files->listFiles([
            'q'      => $query,
            'fields' => 'files(id, name)',
            'spaces' => 'drive',
        ]);

        $files = $results->getFiles();
        if (!empty($files)) {
            return $files[0]->getId(); // Gunakan folder yang sudah ada
        }

        // Buat folder baru hanya jika memang belum ada
        $meta   = new \Google\Service\Drive\DriveFile([
            'name'     => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents'  => [$parentId],
        ]);
        $folder = $service->files->create($meta, ['fields' => 'id']);
        return $folder->getId();
    }
}
