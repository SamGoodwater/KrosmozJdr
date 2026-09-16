<?php

namespace Tests\Feature\Scrapping;

use App\Http\Middleware\RequirePasswordWithInactivity;
use App\Jobs\ProcessScrappingJob;
use App\Models\ScrappingJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ScrappingJobsApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->withoutMiddleware(RequirePasswordWithInactivity::class);
    }

    public function test_create_scrapping_job_dispatches_queue_job(): void
    {
        Queue::fake();

        $response = $this->actingAs($this->admin)->withSession(['auth.password_confirmed_at' => time()])->postJson('/api/dofusdb/jobs', [
            'kind' => 'import_batch',
            'entities' => [
                ['type' => 'class', 'id' => 1],
                ['type' => 'spell', 'id' => 201],
            ],
            'replace_mode' => 'draft_raw_only',
            'include_relations' => true,
        ]);

        $response->assertStatus(202)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.status', 'queued');

        $jobId = (string) $response->json('data.job_id');
        $this->assertNotSame('', $jobId);
        $this->assertDatabaseHas('scrapping_jobs', [
            'id' => $jobId,
            'status' => ScrappingJob::STATUS_QUEUED,
            'kind' => 'import_batch',
        ]);

        Queue::assertPushed(ProcessScrappingJob::class);
    }

    public function test_create_job_complete_mode_uses_update_mode_ignore(): void
    {
        Queue::fake();

        $response = $this->actingAs($this->admin)->withSession(['auth.password_confirmed_at' => time()])->postJson('/api/dofusdb/jobs', [
            'kind' => 'import_batch',
            'entities' => [
                ['type' => 'monster', 'id' => 32],
            ],
            'update_mode' => 'ignore',
            'include_relations' => true,
        ]);

        $response->assertStatus(202);
        $jobId = (string) $response->json('data.job_id');
        $job = ScrappingJob::query()->find($jobId);
        $this->assertNotNull($job);
        $options = $job->payload['options'] ?? [];
        $this->assertSame('never', $options['replace_mode'] ?? null);
        $this->assertTrue((bool) ($options['skip_existing'] ?? false));
        $this->assertTrue((bool) ($options['respect_auto_update'] ?? false));
    }

    public function test_create_job_update_mode_force_disables_respect_auto_update(): void
    {
        Queue::fake();

        $response = $this->actingAs($this->admin)->withSession(['auth.password_confirmed_at' => time()])->postJson('/api/dofusdb/jobs', [
            'kind' => 'import_batch',
            'entities' => [
                ['type' => 'monster', 'id' => 31],
            ],
            'update_mode' => 'force',
        ]);

        $response->assertStatus(202);
        $job = ScrappingJob::query()->find((string) $response->json('data.job_id'));
        $options = $job->payload['options'] ?? [];
        $this->assertSame('always', $options['replace_mode'] ?? null);
        $this->assertFalse((bool) ($options['respect_auto_update'] ?? true));
    }

    public function test_create_job_update_mode_auto_update_respects_flag(): void
    {
        Queue::fake();

        $response = $this->actingAs($this->admin)->withSession(['auth.password_confirmed_at' => time()])->postJson('/api/dofusdb/jobs', [
            'kind' => 'import_batch',
            'entities' => [
                ['type' => 'monster', 'id' => 31],
            ],
            'update_mode' => 'auto_update',
        ]);

        $response->assertStatus(202);
        $job = ScrappingJob::query()->find((string) $response->json('data.job_id'));
        $options = $job->payload['options'] ?? [];
        $this->assertSame('always', $options['replace_mode'] ?? null);
        $this->assertTrue((bool) ($options['respect_auto_update'] ?? false));
    }

    public function test_can_get_job_status(): void
    {
        $job = ScrappingJob::query()->create([
            'kind' => 'import_batch',
            'status' => ScrappingJob::STATUS_RUNNING,
            'run_id' => 'run-test-1',
            'payload' => ['entities' => [['type' => 'class', 'id' => 1]], 'options' => ['run_id' => 'run-test-1']],
            'progress_done' => 1,
            'progress_total' => 3,
            'summary' => ['total' => 3, 'success' => 1, 'errors' => 0],
            'results' => [],
        ]);

        $response = $this->actingAs($this->admin)->withSession(['auth.password_confirmed_at' => time()])->getJson("/api/dofusdb/jobs/{$job->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.job_id', $job->id)
            ->assertJsonPath('data.status', ScrappingJob::STATUS_RUNNING)
            ->assertJsonPath('data.progress.done', 1)
            ->assertJsonPath('data.progress.total', 3);
    }

    public function test_can_cancel_non_terminal_job(): void
    {
        $job = ScrappingJob::query()->create([
            'kind' => 'import_batch',
            'status' => ScrappingJob::STATUS_RUNNING,
            'payload' => ['entities' => [['type' => 'class', 'id' => 1]], 'options' => []],
            'progress_done' => 0,
            'progress_total' => 1,
        ]);

        $response = $this->actingAs($this->admin)->withSession(['auth.password_confirmed_at' => time()])->postJson("/api/dofusdb/jobs/{$job->id}/cancel");
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', ScrappingJob::STATUS_CANCELLED);

        $job->refresh();
        $this->assertSame(ScrappingJob::STATUS_CANCELLED, $job->status);
        $this->assertNotNull($job->cancelled_at);
    }
}
