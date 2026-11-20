<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleController extends Controller
{
    public function index(Request $request)
    {
        $query = Module::with('subject')->latest();
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }
        match ($request->input('visibility', 'visible')) {
            'hidden' => $query->hidden(),
            'all' => null,
            default => $query->visible(),
        };
        $modules = $query->get();
        $subjects = Subject::orderBy('name')->get();
        $hiddenCount = Module::hidden()->count();
        return view('admin.modules.index', compact('modules', 'subjects', 'hiddenCount'));
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'action' => 'sometimes|in:delete,hide,unhide',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:modules,id',
        ]);

        $action = $request->input('action', 'delete');
        $ids = $request->input('ids');
        $count = count($ids);

        if ($action === 'delete') {
            Module::whereIn('id', $ids)->get()->each(function (Module $module) {
                if ($module->image_path) {
                    Storage::disk('public')->delete($module->image_path);
                }
                $module->delete();
            });
        } else {
            Module::whereIn('id', $ids)->update(['is_hidden' => $action === 'hide']);
        }

        $messages = [
            'hide' => "{$count} modul diarsipkan, tidak tampil di form mahasiswa.",
            'unhide' => "{$count} modul ditampilkan kembali.",
            'delete' => "{$count} modul beserta tugasnya dihapus.",
        ];

        return redirect()->route('admin.modules.index')->with('success', $messages[$action]);
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('admin.modules.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'image' => 'nullable|image|max:5120', // Maks 5MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('module-images', 'public');
        }

        Module::create([
            'subject_id' => $request->subject_id,
            'name' => $request->name,
            'description' => $request->description,
            'deadline' => $request->deadline ?: null,
            'image_path' => $imagePath,
        ]);
        return redirect()->route('admin.modules.index')->with('success', 'Modul berhasil ditambahkan.');
    }

    public function edit(Module $module)
    {
        $subjects = Subject::all();
        return view('admin.modules.edit', compact('module', 'subjects'));
    }

    public function update(Request $request, Module $module)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = [
            'subject_id' => $request->subject_id,
            'name' => $request->name,
            'description' => $request->description,
            'deadline' => $request->deadline ?: null,
        ];

        // Handle image: upload baru ATAU hapus — tidak keduanya
        if ($request->hasFile('image')) {
            // Upload gambar baru → hapus lama jika ada
            if ($module->image_path) {
                Storage::disk('public')->delete($module->image_path);
            }
            $data['image_path'] = $request->file('image')->store('module-images', 'public');
        } elseif ($request->boolean('remove_image') && $module->image_path) {
            // Hapus gambar tanpa gantinya
            Storage::disk('public')->delete($module->image_path);
            $data['image_path'] = null;
        }

        $module->update($data);
        return redirect()->route('admin.modules.index')->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroy(Module $module)
    {
        if ($module->image_path) {
            Storage::disk('public')->delete($module->image_path);
        }
        $module->delete();
        return redirect()->route('admin.modules.index')->with('success', 'Modul berhasil dihapus.');
    }
}
