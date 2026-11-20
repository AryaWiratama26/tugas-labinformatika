<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ModuleRequest;
use App\Http\Resources\V1\ModuleResource;
use App\Models\Module;

class ModuleController extends Controller
{
    public function index()
    {
        $query = Module::with('subject')->latest();

        if (request()->filled('subject_id')) {
            $query->where('subject_id', request('subject_id'));
        }
        if (request()->filled('search')) {
            $query->where('name', 'like', '%'.request('search').'%');
        }
        match (request()->input('visibility', 'visible')) {
            'hidden' => $query->hidden(),
            'all' => null,
            default => $query->visible(),
        };

        return ModuleResource::collection($query->paginate(20)->withQueryString());
    }

    public function store(ModuleRequest $request)
    {
        $module = Module::create($request->validated());
        $module->load('subject');
        return (new ModuleResource($module))->response()->setStatusCode(201);
    }

    public function show(Module $module)
    {
        $module->load('subject');
        return new ModuleResource($module);
    }

    public function update(ModuleRequest $request, Module $module)
    {
        $module->update($request->validated());
        $module->load('subject');
        return new ModuleResource($module);
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return response()->json(['message' => 'Modul dihapus.']);
    }
}
