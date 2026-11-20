<?php

namespace Tests\Feature;

use App\Models\LabClass;
use App\Models\Module;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function seedAll(): array
    {
        $subject = Subject::create(['name' => 'Pemrograman Web', 'code' => 'IF201']);
        $module = Module::create(['subject_id' => $subject->id, 'name' => 'Modul 1']);
        $class = LabClass::create(['name' => 'TI-1A']);
        $submission = Submission::create([
            'subject_id' => $subject->id,
            'module_id' => $module->id,
            'lab_class_id' => $class->id,
            'student_name' => 'Budi',
            'student_nim' => '12345',
            'file_name' => 'tugas.zip',
        ]);
        return compact('subject', 'module', 'class', 'submission');
    }

    public function test_semua_halaman_render_200(): void
    {
        $d = $this->seedAll();

        $this->get(route('login'))->assertOk('Halaman login gagal render');
        $this->get('/')->assertOk('Halaman home gagal render');

        $this->actingAs(User::factory()->create());

        $pages = [
            'dashboard' => route('admin.dashboard'),
            'subjects' => route('admin.subjects.index'),
            'subjects.create' => route('admin.subjects.create'),
            'subjects.edit' => route('admin.subjects.edit', $d['subject']),
            'subjects.search' => route('admin.subjects.index', ['search' => 'Prog', 'visibility' => 'all']),
            'modules' => route('admin.modules.index'),
            'modules.create' => route('admin.modules.create'),
            'modules.edit' => route('admin.modules.edit', $d['module']),
            'modules.filter' => route('admin.modules.index', ['subject_id' => $d['subject']->id, 'visibility' => 'hidden']),
            'classes' => route('admin.classes.index'),
            'classes.create' => route('admin.classes.create'),
            'classes.edit' => route('admin.classes.edit', $d['class']),
            'submissions' => route('admin.submissions.index'),
            'submissions.filter' => route('admin.submissions.index', ['graded' => '0', 'visibility' => 'all', 'sort' => 'score_desc', 'per_page' => 10]),
            'submissions.grade' => route('admin.submissions.edit', $d['submission']),
            'submissions.export' => route('admin.submissions.export'),
            'api-keys' => route('admin.api-keys.index'),
        ];

        foreach ($pages as $name => $url) {
            $this->get($url)->assertOk("Halaman {$name} gagal render");
        }
    }
}
