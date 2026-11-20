<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LabClassRequest;
use App\Http\Resources\V1\LabClassResource;
use App\Models\LabClass;

class LabClassController extends Controller
{
    public function index()
    {
        $query = LabClass::latest();
        if (request()->filled('search')) {
            $query->where('name', 'like', '%'.request('search').'%');
        }
        match (request()->input('visibility', 'visible')) {
            'hidden' => $query->hidden(),
            'all' => null,
            default => $query->visible(),
        };
        return LabClassResource::collection($query->paginate(20)->withQueryString());
    }

    public function store(LabClassRequest $request)
    {
        $class = LabClass::create($request->validated());
        return (new LabClassResource($class))->response()->setStatusCode(201);
    }

    public function show(LabClass $labClass)
    {
        return new LabClassResource($labClass);
    }

    public function update(LabClassRequest $request, LabClass $labClass)
    {
        $labClass->update($request->validated());
        return new LabClassResource($labClass);
    }

    public function destroy(LabClass $labClass)
    {
        $labClass->delete();
        return response()->json(['message' => 'Kelas dihapus.']);
    }
}
