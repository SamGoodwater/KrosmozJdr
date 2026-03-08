<?php

namespace App\Services\Privacy;

use App\Models\PrivacyExport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class UserDataExportService
{
    /**
     * Génère un export RGPD complet sous forme d'archive zip.
     */
    public function generate(PrivacyExport $privacyExport): PrivacyExport
    {
        $privacyExport->update([
            'status' => PrivacyExport::STATUS_PROCESSING,
        ]);

        $user = User::with([
            'scenarios',
            'campaigns',
            'pages',
            'sections',
        ])->findOrFail($privacyExport->user_id);

        $payload = [
            'metadata' => [
                'generated_at' => now()->toIso8601String(),
                'format_version' => 1,
                'application' => config('app.name'),
                'user_id' => $user->id,
            ],
            'account' => $this->mapAccount($user),
            'relations' => $this->mapRelations($user),
            'notifications' => $this->mapNotifications($user),
            'media' => $this->mapMedia($user),
        ];

        $relativePath = $privacyExport->path !== ''
            ? $privacyExport->path
            : $this->defaultZipPath($user->id);

        $this->createZip($relativePath, $payload);

        $checksum = hash('sha256', Storage::disk('local')->get($relativePath));

        $privacyExport->update([
            'status' => PrivacyExport::STATUS_READY,
            'path' => $relativePath,
            'checksum' => $checksum,
            'expires_at' => now()->addDay(),
            'meta' => [
                'size' => Storage::disk('local')->size($relativePath),
                'format' => 'zip',
            ],
        ]);

        return $privacyExport->fresh();
    }

    private function mapAccount(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'role' => $user->role,
            'role_name' => $user->role_name,
            'avatar' => $user->avatarPath(),
            'notifications_enabled' => (bool) $user->notifications_enabled,
            'notification_channels' => $user->notification_channels ?? [],
            'notification_preferences' => $user->notification_preferences ?? [],
            'last_login_at' => $user->last_login_at?->toIso8601String(),
            'created_at' => $user->created_at?->toIso8601String(),
            'updated_at' => $user->updated_at?->toIso8601String(),
        ];
    }

    private function mapRelations(User $user): array
    {
        return [
            'scenarios' => $user->scenarios->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name ?? null,
                'slug' => $row->slug ?? null,
                'pivot' => $row->pivot?->toArray(),
            ])->values()->all(),
            'campaigns' => $user->campaigns->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name ?? null,
                'slug' => $row->slug ?? null,
                'pivot' => $row->pivot?->toArray(),
            ])->values()->all(),
            'pages' => $user->pages->map(fn ($row) => [
                'id' => $row->id,
                'title' => $row->title ?? null,
                'slug' => $row->slug ?? null,
                'pivot' => $row->pivot?->toArray(),
            ])->values()->all(),
            'sections' => $user->sections->map(fn ($row) => [
                'id' => $row->id,
                'title' => $row->title ?? null,
                'slug' => $row->slug ?? null,
                'pivot' => $row->pivot?->toArray(),
            ])->values()->all(),
        ];
    }

    private function mapNotifications(User $user): array
    {
        return DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->orderByDesc('created_at')
            ->get(['id', 'type', 'data', 'read_at', 'created_at', 'updated_at'])
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'type' => $row->type,
                    'data' => json_decode((string) $row->data, true),
                    'read_at' => $row->read_at,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ];
            })
            ->values()
            ->all();
    }

    private function mapMedia(User $user): array
    {
        return DB::table('media')
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->orderByDesc('id')
            ->get(['id', 'collection_name', 'file_name', 'mime_type', 'size', 'created_at'])
            ->map(fn ($row) => [
                'id' => $row->id,
                'collection_name' => $row->collection_name,
                'file_name' => $row->file_name,
                'mime_type' => $row->mime_type,
                'size' => $row->size,
                'created_at' => $row->created_at,
            ])
            ->values()
            ->all();
    }

    private function createZip(string $relativePath, array $payload): void
    {
        $disk = Storage::disk('local');
        $directory = dirname($relativePath);
        if ($directory !== '.' && ! $disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        $absolutePath = storage_path('app/private/' . $relativePath);

        $zip = new ZipArchive();
        $result = $zip->open($absolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($result !== true) {
            throw new \RuntimeException('Impossible de créer l’archive RGPD.');
        }

        $jsonFlags = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $zip->addFromString('account.json', json_encode($payload['account'], $jsonFlags));
        $zip->addFromString('relations.json', json_encode($payload['relations'], $jsonFlags));
        $zip->addFromString('notifications.json', json_encode($payload['notifications'], $jsonFlags));
        $zip->addFromString('media.json', json_encode($payload['media'], $jsonFlags));
        $zip->addFromString('metadata.json', json_encode($payload['metadata'], $jsonFlags));
        $zip->addFromString('README.txt', "Export RGPD KrosmozJDR\nGénéré le: {$payload['metadata']['generated_at']}\n");
        $zip->close();
    }

    private function defaultZipPath(int $userId): string
    {
        return sprintf(
            'privacy-exports/user-%d/export-%s.zip',
            $userId,
            Str::uuid()->toString()
        );
    }
}

