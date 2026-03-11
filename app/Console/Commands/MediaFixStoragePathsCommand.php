<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGeneratorFactory;

/**
 * Migre les médias stockés à la racine (ID seul) vers les dossiers MEDIA_PATH des modèles.
 *
 * Corrige les médias créés avant la correction du ModelAwarePathGenerator (morphMap).
 * Les fichiers passent de {id}/ à {MEDIA_PATH}/{id}/.
 *
 * @example php artisan media:fix-storage-paths
 * @example php artisan media:fix-storage-paths --dry-run
 */
class MediaFixStoragePathsCommand extends Command
{
    protected $signature = 'media:fix-storage-paths
        {--dry-run : Afficher les actions sans les exécuter}
        {--force : Pas de confirmation}';

    protected $description = 'Migre les médias vers les dossiers MEDIA_PATH des modèles';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $this->info('Migration des chemins de stockage des médias');
        $this->newLine();

        $medias = Media::all();
        if ($medias->isEmpty()) {
            $this->info('Aucun média en base.');
            return self::SUCCESS;
        }
        $toMigrate = [];
        $pathGenerator = PathGeneratorFactory::create($medias->first());

        foreach ($medias as $media) {
            $correctPath = $this->getCorrectPath($pathGenerator, $media);
            if ($correctPath === null || ! str_contains($correctPath, 'images/entity/')) {
                continue;
            }
            $disk = $media->disk;
            $oldDir = (string) $media->getKey() . '/';
            $newDir = $correctPath . '/';
            $storage = Storage::disk($disk);
            if (! $storage->exists($oldDir)) {
                continue;
            }
            if ($storage->exists($newDir) && $oldDir !== $newDir) {
                continue;
            }
            $toMigrate[] = [
                'media' => $media,
                'old_dir' => $oldDir,
                'new_dir' => $newDir,
            ];
        }

        if ($toMigrate === []) {
            $this->info('Aucun média à migrer.');
            return self::SUCCESS;
        }

        $this->line('Médias à migrer : ' . count($toMigrate));
        foreach (array_slice($toMigrate, 0, 5) as $item) {
            $m = $item['media'];
            $this->line("  - {$m->model_type}#{$m->model_id} : {$item['old_dir']} → {$item['new_dir']}");
        }
        if (count($toMigrate) > 5) {
            $this->line('  ...');
        }
        $this->newLine();

        if (! $force && ! $dryRun && ! $this->confirm('Continuer ?')) {
            return self::SUCCESS;
        }

        $migrated = 0;
        $errors = 0;
        foreach ($toMigrate as $item) {
            /** @var Media $media */
            $media = $item['media'];
            $disk = $media->disk;
            $storage = Storage::disk($disk);
            $oldDir = $item['old_dir'];
            $newDir = $item['new_dir'];

            if ($dryRun) {
                $this->line("  [dry-run] {$oldDir} → {$newDir}");
                $migrated++;
                continue;
            }

            try {
                $files = $storage->allFiles($oldDir);
                if ($files === []) {
                    continue;
                }
                $storage->makeDirectory(rtrim($newDir, '/'));
                foreach ($files as $file) {
                    $rel = substr($file, strlen($oldDir));
                    $dest = rtrim($newDir, '/') . '/' . $rel;
                    $storage->move($file, $dest);
                }
                $this->removeEmptyDir($storage, $oldDir);
                $migrated++;
            } catch (\Throwable $e) {
                $errors++;
                $this->warn("  Erreur {$media->getKey()} : " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Terminé : {$migrated} migré(s)" . ($errors > 0 ? ", {$errors} erreur(s)" : ''));

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function getCorrectPath(object $pathGenerator, Media $media): ?string
    {
        if (! method_exists($pathGenerator, 'getPath')) {
            return null;
        }

        return rtrim($pathGenerator->getPath($media), '/');
    }

    private function removeEmptyDir(\Illuminate\Contracts\Filesystem\Filesystem $storage, string $dir): void
    {
        $files = $storage->allFiles($dir);
        foreach ($files as $f) {
            $storage->delete($f);
        }
        $storage->deleteDirectory($dir);
    }
}
