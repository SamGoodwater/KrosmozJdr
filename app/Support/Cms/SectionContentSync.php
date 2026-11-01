<?php

declare(strict_types=1);

namespace App\Support\Cms;

use App\Models\Section;

/**
 * Synchronisation sûre fichier → section CMS (règles, L’Essentiel).
 *
 * Chaque import mémorise l’empreinte du HTML écrit (`source_hash` dans `data` et `params`).
 * À l’import suivant, une section n’est remplacée que si son contenu n’a pas bougé depuis :
 * une édition faite dans le CMS est signalée comme conflit et conservée.
 *
 * @example
 * $plan = SectionContentSync::plan($section, $html); // 'update'
 * $section->data = SectionContentSync::stamp($section->data ?? [], $html);
 */
final class SectionContentSync
{
    public const HASH_KEY = 'source_hash';

    /** Section absente : à créer. */
    public const CREATE = 'create';

    /** Contenu déjà identique au fichier. */
    public const SAME = 'same';

    /** Contenu vide ou inchangé depuis le dernier import : remplaçable. */
    public const UPDATE = 'update';

    /** Contenu modifié hors import (CMS, ou section importée avant les empreintes) : on n’écrase pas. */
    public const CONFLICT = 'conflict';

    public static function hash(string $html): string
    {
        return sha1($html);
    }

    /**
     * Contenu HTML actuellement affiché par la section (`data.content`, sinon `params.content`).
     */
    public static function currentContent(Section $section): string
    {
        $data = is_array($section->data) ? $section->data : [];
        $params = is_array($section->params) ? $section->params : [];

        return (string) ($data['content'] ?? $params['content'] ?? '');
    }

    /**
     * @return self::CREATE|self::SAME|self::UPDATE|self::CONFLICT
     */
    public static function plan(?Section $section, string $html): string
    {
        if ($section === null) {
            return self::CREATE;
        }

        $current = self::currentContent($section);
        if ($current === $html) {
            return self::SAME;
        }
        if (trim($current) === '') {
            return self::UPDATE;
        }

        $data = is_array($section->data) ? $section->data : [];
        $storedHash = (string) ($data[self::HASH_KEY] ?? '');

        return $storedHash !== '' && hash_equals($storedHash, self::hash($current))
            ? self::UPDATE
            : self::CONFLICT;
    }

    /**
     * Écrit le contenu et son empreinte dans un payload `data` / `params`.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function stamp(array $payload, string $html): array
    {
        $payload['content'] = $html;
        $payload[self::HASH_KEY] = self::hash($html);

        return $payload;
    }
}
