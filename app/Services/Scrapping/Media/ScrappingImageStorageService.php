<?php

namespace App\Services\Scrapping\Media;

use App\Services\FileService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Service de téléchargement et stockage des images issues du scrapping.
 *
 * @description
 * Télécharge une image distante (ex: DofusDB) et la range dans une arborescence stable :
 *   scrapping/images/{entity}/{bucket}/{dofusdb_id}.{ext}
 *
 * Sécurité:
 * - URL strictement http/https
 * - host en allowlist (config('scrapping.images.allowed_hosts'))
 * - type MIME image/*
 * - taille max (config('scrapping.images.max_bytes'))
 *
 * @example
 * $url = $service->storeFromUrl('https://api.dofusdb.fr/img/items/1048.png', 'resources', '1048');
 * // => "/storage/scrapping/images/resources/001/1048.png"
 */
class ScrappingImageStorageService
{
    /**
     * Télécharge l'image si possible, retourne l'URL publique (/storage/...) ou null si non sauvegardée.
     */
    public function storeFromUrl(?string $url, string $entityFolder, ?string $dofusdbId): ?string
    {
        if (!$url || !$dofusdbId) {
            return null;
        }

        $cfg = config('scrapping.images', []);
        if (!(bool) ($cfg['enabled'] ?? false)) {
            return null;
        }

        $diskName = (string) ($cfg['disk'] ?? FileService::DISK_DEFAULT);
        $baseDir = trim((string) ($cfg['base_dir'] ?? 'scrapping/images'), '/');
        $timeout = (int) ($cfg['timeout'] ?? 15);
        $maxBytes = (int) ($cfg['max_bytes'] ?? (5 * 1024 * 1024));
        $allowedHosts = (array) ($cfg['allowed_hosts'] ?? []);
        $forceUpdate = (bool) ($cfg['force_update'] ?? false);

        $parts = @parse_url($url);
        if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $scheme = strtolower((string) $parts['scheme']);
        $host = strtolower((string) $parts['host']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }
        if (!empty($allowedHosts) && !in_array($host, array_map('strtolower', $allowedHosts), true)) {
            return null;
        }

        // Déterminer extension
        $ext = strtolower(pathinfo((string) ($parts['path'] ?? ''), PATHINFO_EXTENSION));
        if (!$ext || !in_array($ext, FileService::EXTENSIONS_IMAGE, true)) {
            // Fallback: on tentera de déduire via Content-Type
            $ext = 'png';
        }

        $bucket = $this->bucketFromId($dofusdbId);
        $entityFolder = trim($entityFolder, '/');
        $safeId = preg_replace('/[^0-9A-Za-z_-]/', '', (string) $dofusdbId) ?: 'unknown';
        $relativePath = "{$baseDir}/{$entityFolder}/{$bucket}/{$safeId}.{$ext}";

        $disk = Storage::disk($diskName);
        if ($disk->exists($relativePath)) {
            if (!$forceUpdate) {
                return $disk->url($relativePath);
            }
            // Overwrite demandé: supprimer avant de retélécharger
            $disk->delete($relativePath);
        }

        try {
            $response = Http::timeout($timeout)
                ->withHeaders([
                    'User-Agent' => config('scrapping.data_collect.user_agent', 'KrosmozJDR-Scrapping/1.0'),
                    'Accept' => 'image/*',
                ])
                ->get($url);

            if (!$response->successful()) {
                return null;
            }

            $contentType = strtolower((string) $response->header('Content-Type', ''));
            if (!str_starts_with($contentType, 'image/')) {
                return null;
            }

            $body = $response->body();
            if ($body === '' || strlen($body) > $maxBytes) {
                return null;
            }

            // Créer le dossier (implicit) + écrire
            $disk->put($relativePath, $body);

            Log::info('Scrapping image stored', [
                'entity' => $entityFolder,
                'dofusdb_id' => $dofusdbId,
                'path' => $relativePath,
                'host' => $host,
            ]);

            return $disk->url($relativePath);
        } catch (\Throwable $e) {
            Log::warning('Scrapping image download failed', [
                'url' => $url,
                'entity' => $entityFolder,
                'dofusdb_id' => $dofusdbId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Bucket pour limiter le nombre de fichiers par dossier.
     *
     * @example "1048" => "001"
     */
    private function bucketFromId(string $id): string
    {
        if (ctype_digit($id)) {
            $bucket = (int) floor(((int) $id) / 1000);
            return str_pad((string) $bucket, 3, '0', STR_PAD_LEFT);
        }

        // Fallback: bucket par hash
        return substr(md5($id), 0, 3);
    }
}


