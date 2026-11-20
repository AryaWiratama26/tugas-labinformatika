<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\LabClass;
use App\Models\Module;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MasterVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function seedMasters(): array
    {
        $subject = Subject::create(['name' => 'Pemrograman Web', 'code' => 'IF201']);
        $module = Module::create(['subject_id' => $subject->id, 'name' => 'Modul 1']);
        $class = LabClass::create(['name' => 'TI-1A']);
        return compact('subject', 'module', 'class');
    }

    private function apiHeaders(): array
    {
        $gen = ApiKey::generatePlaintext();
        ApiKey::create(['name' => 'TEST', 'key_hash' => $gen['hash'], 'prefix' => $gen['prefix']]);
        return ['X-API-KEY' => $gen['plain']];
    }

    public function test_form_publik_sembunyikan_yang_diarsipkan(): void
    {
        $d = $this->seedMasters();
        $d['module']->forceFill(['is_hidden' => true])->save();
        $d['class']->forceFill(['is_hidden' => true])->save();

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('Pemrograman Web', $html);
        $this->assertStringNotContainsString('TI-1A', $html);

        $this->getJson("/api/subjects/{$d['subject']->id}/modules")
            ->assertOk()->assertJsonCount(0);
    }

    public function test_form_publik_tolak_submit_modul_arsip(): void
    {
        $d = $this->seedMasters();
        $d['module']->forceFill(['is_hidden' => true])->save();

        $this->post('/', [
            'subject_id' => $d['subject']->id,
            'module_id' => $d['module']->id,
            'lab_class_id' => $d['class']->id,
            'student_name' => 'Budi',
            'student_nim' => '12345',
            'assignment_file' => UploadedFile::fake()->create('tugas.pdf', 100),
        ])->assertRedirect()->assertSessionHas('error');
    }

    public function test_admin_toggle_dan_filter_arsip_master(): void
    {
        $this->actingAs(User::factory()->create());
        $d = $this->seedMasters();

        $this->post(route('admin.modules.bulk'), [
            'action' => 'hide', 'ids' => [$d['module']->id],
        ])->assertRedirect();
        $this->assertTrue($d['module']->fresh()->is_hidden);

        $res = $this->get(route('admin.modules.index'));
        $res->assertOk()->assertDontSee('Modul 1');

        $res = $this->get(route('admin.modules.index', ['visibility' => 'hidden']));
        $res->assertOk()->assertSee('Modul 1');

        $this->post(route('admin.modules.bulk'), [
            'action' => 'unhide', 'ids' => [$d['module']->id],
        ])->assertRedirect();
        $this->assertFalse($d['module']->fresh()->is_hidden);
    }

    public function test_api_master_visibility_dan_update(): void
    {
        $h = $this->apiHeaders();
        $d = $this->seedMasters();

        $this->putJson("/api/v1/modules/{$d['module']->id}", ['is_hidden' => true], $h)->assertOk();

        $this->getJson('/api/v1/modules', $h)->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/modules?visibility=all', $h)->assertOk()->assertJsonCount(1, 'data');

        $this->putJson("/api/v1/subjects/{$d['subject']->id}", ['is_hidden' => true], $h)->assertOk();
        $this->getJson('/api/v1/subjects', $h)->assertOk()->assertJsonCount(0, 'data');
    }
}
