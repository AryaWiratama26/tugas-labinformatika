<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabClass;
use Illuminate\Http\Request;

class LabClassController extends Controller
{
    public function index()
    {
        $classes = LabClass::latest()->get();
        return view('admin.classes.index', compact('classes'));
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
