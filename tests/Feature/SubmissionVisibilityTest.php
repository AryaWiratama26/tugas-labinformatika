<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\LabClass;
use App\Models\Module;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function seedData(int $count = 3): array
    {
        $subject = Subject::create(['name' => 'Pemrograman Web', 'code' => 'IF201']);
        $module = Module::create(['subject_id' => $subject->id, 'name' => 'Modul 1']);
        $class = LabClass::create(['name' => 'TI-1A']);
        $subs = [];
        for ($i = 1; $i <= $count; $i++) {
            $subs[] = Submission::create([
                'subject_id' => $subject->id,
                'module_id' => $module->id,
                'lab_class_id' => $class->id,
                'student_name' => 'Mhs '.$i,
                'student_nim' => 'NIM'.$i,
                'file_name' => "NIM{$i}_file.zip",
            ]);
        }
        return compact('subject', 'module', 'class', 'subs');
    }

    private function apiHeaders(): array
    {
        $gen = ApiKey::generatePlaintext();
        ApiKey::create(['name' => 'TEST', 'key_hash' => $gen['hash'], 'prefix' => $gen['prefix']]);
        return ['X-API-KEY' => $gen['plain']];
    }

    private function admin()
    {
        return $this->actingAs(User::factory()->create());
    }

    public function test_admin_default_hanya_tampilkan_yang_visible(): void
    {
        $d = $this->seedData();
        $d['subs'][0]->forceFill(['is_hidden' => true])->save();

        $res = $this->admin()->get(route('admin.submissions.index'));
        $res->assertOk();
        $res->assertSee('Mhs 2');
        $res->assertDontSee('Mhs 1');
    }

    public function test_admin_bulk_hide_unhide_grade_delete(): void
    {
        $d = $this->seedData();
        $ids = collect($d['subs'])->pluck('id')->toArray();

        $this->admin()->post(route('admin.submissions.bulk'), [
            'action' => 'hide', 'ids' => [$ids[0], $ids[1]],
        ])->assertRedirect();
        $this->assertEquals(2, Submission::hidden()->count());

        $this->admin()->post(route('admin.submissions.bulk'), [
            'action' => 'unhide', 'ids' => [$ids[0]],
        ])->assertRedirect();
        $this->assertEquals(1, Submission::hidden()->count());

        $this->admin()->post(route('admin.submissions.bulk'), [
            'action' => 'grade', 'ids' => $ids, 'score' => 88, 'feedback' => 'OK',
        ])->assertRedirect();
        $this->assertEquals(3, Submission::where('score', 88)->count());

        $this->admin()->post(route('admin.submissions.bulk'), [
            'action' => 'delete', 'ids' => [$ids[0]],
        ])->assertRedirect();
        $this->assertDatabaseMissing('submissions', ['id' => $ids[0]]);
    }

    public function test_admin_bulk_validasi(): void
    {
        $this->seedData();
        $this->admin()->post(route('admin.submissions.bulk'), [
            'action' => 'grade', 'ids' => [999],
        ])->assertSessionHasErrors('ids.0');

        $sub = Submission::first();
        $this->admin()->post(route('admin.submissions.bulk'), [
            'action' => 'grade', 'ids' => [$sub->id], 'score' => 500,
        ])->assertSessionHasErrors('score');
    }

    public function test_admin_master_bulk_delete_dan_search(): void
    {
        Subject::create(['name' => 'Basis Data']);
        Subject::create(['name' => 'Jaringan']);

        $res = $this->admin()->get(route('admin.subjects.index', ['search' => 'Jaring']));
        $res->assertOk()->assertSee('Jaringan')->assertDontSee('Basis Data');

        $ids = Subject::pluck('id')->toArray();
        $this->admin()->post(route('admin.subjects.bulk'), ['ids' => $ids])->assertRedirect();
        $this->assertEquals(0, Subject::count());
    }

    public function test_api_visibility_filter_dan_bulk(): void
    {
        $h = $this->apiHeaders();
        $d = $this->seedData();
        $ids = collect($d['subs'])->pluck('id')->toArray();

        $this->getJson('/api/v1/submissions', $h)->assertOk()->assertJsonCount(3, 'data');

        $this->putJson("/api/v1/submissions/{$ids[0]}", ['is_hidden' => true], $h)->assertOk();
        $this->getJson('/api/v1/submissions', $h)->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/v1/submissions?visibility=hidden', $h)->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/submissions?visibility=all', $h)->assertOk()->assertJsonCount(3, 'data');

        $this->postJson('/api/v1/submissions/bulk', [
            'ids' => [$ids[1], $ids[2]], 'action' => 'grade', 'score' => 75,
        ], $h)->assertOk()->assertJsonPath('affected', 2);
        $this->assertEquals(2, Submission::where('score', 75)->count());

        $this->postJson('/api/v1/submissions/bulk', [
            'ids' => [$ids[1]], 'action' => 'hide',
        ], $h)->assertOk();
        $this->assertTrue(Submission::find($ids[1])->is_hidden);
    }
}
