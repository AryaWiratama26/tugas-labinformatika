<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubjectRequest;
use App\Http\Resources\V1\ModuleResource;
use App\Http\Resources\V1\SubjectResource;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function index()
    {
        $query = Subject::withCount(['modules', 'submissions'])->latest();
        match (request()->input('visibility', 'visible')) {
            'hidden' => $query->hidden(),
            'all' => null,
            default => $query->visible(),
        };
        return SubjectResource::collection($query->paginate(20));
    }

    public function store(SubjectRequest $request)
    {
        $subject = Subject::create($request->validated());
        return (new SubjectResource($subject))->response()->setStatusCode(201);
    }

    public function show(Subject $subject)
    {
        $subject->loadCount(['modules', 'submissions']);
        return new SubjectResource($subject);
    }

    public function update(SubjectRequest $request, Subject $subject)
    {
        $subject->update($request->validated());
        return new SubjectResource($subject);
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->json(['message' => 'Mata kuliah dihapus.']);
    }

    public function modules(Subject $subject)
    {
        $query = $subject->modules()->with('subject')->latest();
        match (request()->input('visibility', 'visible')) {
            'hidden' => $query->hidden(),
            'all' => null,
            default => $query->visible(),
        };
        return ModuleResource::collection($query->paginate(20));
    }
}
