<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Trait optionnel pour les modèles HasMedia : nommage des fichiers via constantes.
 *
 * Constantes possibles sur le modèle :
 * - MEDIA_PATH : répertoire de stockage (ex: images/entity/breeds), lu par ModelAwarePathGenerator.
 * - MEDIA_FILE_PATTERN_{COLLECTION} : motif pour une collection (ex: MEDIA_FILE_PATTERN_IMAGES, MEDIA_FILE_PATTERN_ICONS).
 * - MEDIA_FILE_PATTERN : motif par défaut pour toutes les collections.
 *
 * Placeholders dans le motif : [name], [date], [id], [uniqid].
 * Le nom final est passé dans Str::slug() pour éviter tout caractère inadapté au système de fichiers.
 * Exemple : 'breed-icon-[name]-[date]' → breed-icon-eniripsa-2025-02-07
 */
trait HasMediaCustomNaming
{
    /**
     * Retourne le nom de fichier pour une collection selon le motif défini, ou null pour garder le nom par défaut.
     *
     * @param string $collection Nom de la collection (images, icons, files…)
     * @param string $extension Extension à ajouter (ex: png). Laissé vide si le nom doit rester sans extension.
     */
    public function getMediaFileNameForCollection(string $collection, string $extension = ''): ?string
    {
        $pattern = $this->getMediaFilePatternForCollection($collection);
        if ($pattern === null || $pattern === '') {
            return null;
        }

        $name = Str::slug((string) ($this->name ?? $this->getKey()));
        $date = now()->format('Y-m-d');
        $id = (string) $this->getKey();
        $uniqid = uniqid('', false);

        $replace = [
            '[name]' => $name,
            '[date]' => $date,
            '[id]' => $id,
            '[uniqid]' => $uniqid,
        ];
        $base = str_replace(array_keys($replace), array_values($replace), $pattern);
        $base = Str::slug($base);
        if ($base === '') {
            $base = 'media-' . $id;
        }

        if ($extension !== '') {
            $ext = preg_replace('/[^a-z0-9]/i', '', $extension);

            return $ext !== '' ? $base . '.' . strtolower($ext) : $base;
        }

        return $base;
    }

    /**
     * Retourne le motif de nommage pour la collection (constante MEDIA_FILE_PATTERN_* ou MEDIA_FILE_PATTERN).
     */
    protected function getMediaFilePatternForCollection(string $collection): ?string
    {
        $key = 'MEDIA_FILE_PATTERN_' . strtoupper(Str::snake($collection));
        if (defined(static::class . '::' . $key)) {
            return (string) constant(static::class . '::' . $key);
        }
        if (defined(static::class . '::MEDIA_FILE_PATTERN')) {
            return (string) static::MEDIA_FILE_PATTERN;
        }

        return null;
    }
}
