<?php

namespace Tests\Feature\Scrapping;

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
        $this->withoutMiddleware(\Illuminate\Auth\Middleware\RequirePassword::class);
    }

    public function test_create_scrapping_job_dispatches_queue_job(): void
    {
        Queue::fake();

        $response = $this->actingAs($this->admin)->postJson('/api/scrapping/jobs', [
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

        $response = $this->actingAs($this->admin)->getJson("/api/scrapping/jobs/{$job->id}");

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

        $response = $this->actingAs($this->admin)->postJson("/api/scrapping/jobs/{$job->id}/cancel");
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', ScrappingJob::STATUS_CANCELLED);

        $job->refresh();
        $this->assertSame(ScrappingJob::STATUS_CANCELLED, $job->status);
        $this->assertNotNull($job->cancelled_at);
    }
}
