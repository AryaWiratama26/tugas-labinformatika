<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabClass;
use Illuminate\Http\Request;

class LabClassController extends Controller
{
    public function index(Request $request)
    {
        $query = LabClass::latest();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        match ($request->input('visibility', 'visible')) {
            'hidden' => $query->hidden(),
            'all' => null,
            default => $query->visible(),
        };
        $classes = $query->get();
        $hiddenCount = LabClass::hidden()->count();
        return view('admin.classes.index', compact('classes', 'hiddenCount'));
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'action' => 'sometimes|in:delete,hide,unhide',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:lab_classes,id',
        ]);

        $action = $request->input('action', 'delete');
        $ids = $request->input('ids');
        $count = count($ids);

        match ($action) {
            'hide' => LabClass::whereIn('id', $ids)->update(['is_hidden' => true]),
            'unhide' => LabClass::whereIn('id', $ids)->update(['is_hidden' => false]),
            default => LabClass::whereIn('id', $ids)->delete(),
        };

        $messages = [
            'hide' => "{$count} kelas diarsipkan, tidak tampil di form mahasiswa.",
            'unhide' => "{$count} kelas ditampilkan kembali.",
            'delete' => "{$count} kelas beserta tugasnya dihapus.",
        ];

        return redirect()->route('admin.classes.index')->with('success', $messages[$action]);
    }

    public function create()
    {
        return view('admin.classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        LabClass::create($request->all());
        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(LabClass $labClass)
    {
        return view('admin.classes.edit', compact('labClass'));
    }

    public function update(Request $request, LabClass $labClass)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $labClass->update($request->all());
        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(LabClass $labClass)
    {
        $labClass->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
