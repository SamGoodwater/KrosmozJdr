<?php

declare(strict_types=1);

namespace App\Services\Project;

/**
 * Suivi fichier d’une restauration (indépendant de la BDD / file database).
 *
 * @example
 * $status = ProjectBackupOperationStatus::forBackupRoot($root);
 * $status->write(['state' => 'running', 'phase' => 'safety', 'progress' => 20, 'message' => '…']);
 */
final class ProjectBackupOperationStatus
{
    public const FILENAME = '.restore-status.json';

    public function __construct(
        private readonly string $statusPath,
    ) {}

    public static function forBackupRoot(string $backupRoot): self
    {
        return new self(rtrim($backupRoot, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.self::FILENAME);
    }

    public function path(): string
    {
        return $this->statusPath;
    }

    /**
     * @param  array{
     *     state?: string,
     *     message?: string|null,
     *     archive?: string|null,
     *     started_at?: string|null,
     *     finished_at?: string|null,
     *     log?: list<string>,
     *     user_id?: int|null,
     *     phase?: string|null,
     *     progress?: int|null,
     *     safety_archive?: string|null
     * }  $payload
     */
    public function write(array $payload): void
    {
        $dir = dirname($this->statusPath);
        if (! is_dir($dir) && ! mkdir($dir, 0750, true) && ! is_dir($dir)) {
            throw new \RuntimeException('Impossible de créer le répertoire de statut de restauration.');
        }

        $existing = $this->read() ?? [];
        $merged = array_merge($existing, $payload, [
            'updated_at' => now()->toIso8601String(),
        ]);

        if (isset($merged['progress'])) {
            $merged['progress'] = max(0, min(100, (int) $merged['progress']));
        }

        $json = json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new \RuntimeException('Encodage JSON du statut de restauration impossible.');
        }

        $tmp = $this->statusPath.'.tmp';
        if (file_put_contents($tmp, $json) === false) {
            throw new \RuntimeException('Écriture du statut de restauration impossible.');
        }
        @chmod($tmp, 0640);
        if (! @rename($tmp, $this->statusPath)) {
            @unlink($this->statusPath);
            if (! @rename($tmp, $this->statusPath)) {
                @unlink($tmp);
                throw new \RuntimeException('Remplacement atomique du statut de restauration impossible.');
            }
        }
    }

    /**
     * @param  bool  $updateMessage  false pour les lignes secondaires (ex. sortie de maintenance)
     */
    public function appendLog(
        string $line,
        bool $updateMessage = true,
        ?string $phase = null,
        ?int $progress = null,
    ): void {
        $current = $this->read() ?? ['state' => 'running', 'log' => []];
        $log = $current['log'] ?? [];
        if (! is_array($log)) {
            $log = [];
        }
        $log[] = '['.now()->format('H:i:s').'] '.$line;
        if (count($log) > 300) {
            $log = array_slice($log, -300);
        }
        $current['log'] = array_values($log);
        if ($updateMessage) {
            $current['message'] = $line;
        }
        if ($phase !== null) {
            $current['phase'] = $phase;
        }
        if ($progress !== null) {
            $current['progress'] = max(0, min(100, $progress));
        }
        $this->write($current);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function read(): ?array
    {
        if (! is_file($this->statusPath)) {
            return null;
        }

        $raw = @file_get_contents($this->statusPath);
        if ($raw === false || $raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }

    public function clear(): void
    {
        if (is_file($this->statusPath)) {
            @unlink($this->statusPath);
        }
    }

    public function isBusy(): bool
    {
        $data = $this->read();
        if ($data === null) {
            return false;
        }

        $state = (string) ($data['state'] ?? '');

        return in_array($state, ['queued', 'running'], true);
    }
}
