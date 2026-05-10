<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Subject;
use App\Models\LabClass;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSubmissions = Submission::count();
        $totalSubjects = Subject::count();
        $totalClasses = LabClass::count();
        $recentSubmissions = Submission::with(['subject', 'labClass'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalSubmissions', 'totalSubjects', 'totalClasses', 'recentSubmissions'));
    }
}
