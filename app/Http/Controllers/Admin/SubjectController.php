<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::latest();
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('code', 'like', '%'.$request->search.'%');
            });
        }
        match ($request->input('visibility', 'visible')) {
            'hidden' => $query->hidden(),
            'all' => null,
            default => $query->visible(),
        };
        $subjects = $query->get();
        $hiddenCount = Subject::hidden()->count();
        return view('admin.subjects.index', compact('subjects', 'hiddenCount'));
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'action' => 'sometimes|in:delete,hide,unhide',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:subjects,id',
        ]);

        $action = $request->input('action', 'delete');
        $ids = $request->input('ids');
        $count = count($ids);

        match ($action) {
            'hide' => Subject::whereIn('id', $ids)->update(['is_hidden' => true]),
            'unhide' => Subject::whereIn('id', $ids)->update(['is_hidden' => false]),
            default => Subject::whereIn('id', $ids)->delete(),
        };

        $messages = [
            'hide' => "{$count} mata kuliah diarsipkan, tidak tampil di form mahasiswa.",
            'unhide' => "{$count} mata kuliah ditampilkan kembali.",
            'delete' => "{$count} mata kuliah beserta modul dan tugasnya dihapus.",
        ];

        return redirect()->route('admin.subjects.index')->with('success', $messages[$action]);
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
        ]);

        Subject::create($request->all());
        return redirect()->route('admin.subjects.index')->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
        ]);

        $subject->update($request->all());
        return redirect()->route('admin.subjects.index')->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('admin.subjects.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
