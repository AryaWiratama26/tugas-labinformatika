<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\LabClass;
use App\Models\Module;
use App\Models\Subject;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    private function makeKey(array $overrides = []): array
    {
        $gen = ApiKey::generatePlaintext();
        $key = ApiKey::create(array_merge([
            'name' => 'TEST-CLIENT',
            'key_hash' => $gen['hash'],
            'prefix' => $gen['prefix'],
        ], $overrides));

        return [$key, $gen['plain']];
    }

    private function seedData(): array
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
            'file_name' => '12345_M1_IF201.zip',
        ]);

        return compact('subject', 'module', 'class', 'submission');
    }

    public function test_health_tanpa_key(): void
    {
        $this->getJson('/api/v1/health')->assertOk()->assertJsonPath('status', 'ok');
    }

    public function test_tolak_tanpa_key(): void
    {
        $this->getJson('/api/v1/subjects')->assertUnauthorized();
    }

    public function test_tolak_key_salah(): void
    {
        $this->getJson('/api/v1/subjects', ['X-API-KEY' => 'salah'])
            ->assertUnauthorized();
    }

    public function test_tolak_key_revoked_dan_expired(): void
    {
        [$revoked, $revokedPlain] = $this->makeKey(['revoked_at' => now()]);
        $this->getJson('/api/v1/subjects', ['X-API-KEY' => $revokedPlain])->assertUnauthorized();

        [$expired, $expiredPlain] = $this->makeKey(['expires_at' => now()->subDay()]);
        $this->getJson('/api/v1/subjects', ['X-API-KEY' => $expiredPlain])->assertUnauthorized();
    }

    public function test_crud_subject_module_class(): void
    {
        [, $plain] = $this->makeKey();
        $h = ['X-API-KEY' => $plain];

        $subject = $this->postJson('/api/v1/subjects', ['name' => 'Basis Data', 'code' => 'IF202'], $h)
            ->assertCreated()->json('data');
        $this->getJson("/api/v1/subjects/{$subject['id']}", $h)->assertOk();
        $this->putJson("/api/v1/subjects/{$subject['id']}", ['name' => 'Basis Data Lanjut'], $h)->assertOk();

        $module = $this->postJson('/api/v1/modules', [
            'subject_id' => $subject['id'], 'name' => 'Modul 1',
        ], $h)->assertCreated()->json('data');
        $this->getJson("/api/v1/modules/{$module['id']}", $h)->assertOk();

        $class = $this->postJson('/api/v1/classes', ['name' => 'TI-1B'], $h)
            ->assertCreated()->json('data');
        $this->getJson("/api/v1/classes/{$class['id']}", $h)->assertOk();

        $this->getJson("/api/v1/subjects/{$subject['id']}/modules", $h)
            ->assertOk()->assertJsonCount(1, 'data');

        $this->deleteJson("/api/v1/classes/{$class['id']}", [], $h)->assertOk();
        $this->deleteJson("/api/v1/modules/{$module['id']}", [], $h)->assertOk();
        $this->deleteJson("/api/v1/subjects/{$subject['id']}", [], $h)->assertOk();
    }

    public function test_submissions_list_filter_dan_grading(): void
    {
        [, $plain] = $this->makeKey();
        $h = ['X-API-KEY' => $plain];
        $d = $this->seedData();

        $this->getJson('/api/v1/submissions', $h)->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/submissions?search=12345', $h)->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/submissions?search=tidakada', $h)->assertOk()->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/submissions?subject_id={$d['subject']->id}", $h)->assertOk()->assertJsonCount(1, 'data');

        $this->putJson("/api/v1/submissions/{$d['submission']->id}", [
            'score' => 90, 'feedback' => 'Bagus.',
        ], $h)->assertOk();
        $this->assertEquals(90, $d['submission']->fresh()->score);

        $this->assertDatabaseHas('submissions', [
            'id' => $d['submission']->id, 'score' => 90, 'graded_by' => 'TEST-CLIENT',
        ]);

        $this->putJson("/api/v1/submissions/{$d['submission']->id}", ['score' => 150], $h)
            ->assertStatus(422);

        $this->deleteJson("/api/v1/submissions/{$d['submission']->id}", [], $h)->assertOk();
        $this->assertDatabaseMissing('submissions', ['id' => $d['submission']->id]);
    }

    public function test_bearer_token_juga_bisa(): void
    {
        [, $plain] = $this->makeKey();
        $this->getJson('/api/v1/subjects', ['Authorization' => 'Bearer '.$plain])->assertOk();
    }
}
