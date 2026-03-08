<?php

namespace Tests\Feature\UserPrivacy;

use App\Jobs\GenerateUserDataExportJob;
use App\Models\DataSubjectRequest;
use App\Models\PrivacyExport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ExportDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_request_requires_recent_password_confirmation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user.privacy.export'));

        $response->assertRedirect(route('password.confirm'));
    }

    public function test_export_request_creates_records_and_dispatches_job(): void
    {
        config(['privacy.export_sync' => false]);
        Queue::fake();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('user.privacy.export'));

        $response->assertRedirect();
        $this->assertDatabaseHas('data_subject_requests', [
            'user_id' => $user->id,
            'type' => DataSubjectRequest::TYPE_EXPORT,
            'status' => DataSubjectRequest::STATUS_PENDING,
        ]);
        $this->assertDatabaseHas('privacy_exports', [
            'user_id' => $user->id,
            'status' => PrivacyExport::STATUS_PENDING,
        ]);

        Queue::assertPushed(GenerateUserDataExportJob::class);
    }

    public function test_export_sync_produces_ready_export_immediately(): void
    {
        config(['privacy.export_sync' => true]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('user.privacy.export'));

        $response->assertRedirect();
        $export = PrivacyExport::query()->where('user_id', $user->id)->latest('id')->first();
        $this->assertNotNull($export);
        $this->assertSame(PrivacyExport::STATUS_READY, $export->status);
        $this->assertNotNull($export->expires_at);
    }

    public function test_export_download_is_forbidden_for_other_users(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $export = PrivacyExport::query()->create([
            'user_id' => $owner->id,
            'status' => PrivacyExport::STATUS_READY,
            'path' => 'privacy-exports/test-owner.zip',
            'expires_at' => now()->addHour(),
        ]);
        Storage::disk('local')->put($export->path, 'fake-zip-content');

        $signedUrl = URL::temporarySignedRoute(
            'user.privacy.exports.download',
            now()->addMinutes(10),
            ['privacyExport' => $export->id]
        );

        $response = $this->actingAs($intruder)->get($signedUrl);
        $response->assertForbidden();
    }
}

