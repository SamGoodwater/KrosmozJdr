<?php

declare(strict_types=1);

namespace App\Support\Project;

/**
 * Domaines des jobs Artisan lancés depuis l’admin (un job actif max par domaine).
 */
final class ProjectConsoleDomain
{
    public const REVIEW = 'review';

    public const CLEAR = 'clear';

    public const DEPS = 'deps';

    public const BACKUP = 'backup';

    public const DATA_SYNC = 'data-sync';

    public const RULES_DOWNLOADS = 'rules-downloads';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::REVIEW,
            self::CLEAR,
            self::DEPS,
            self::BACKUP,
            self::DATA_SYNC,
            self::RULES_DOWNLOADS,
        ];
    }

    public static function label(string $domain): string
    {
        return match ($domain) {
            self::REVIEW => 'Review',
            self::CLEAR => 'Nettoyage caches',
            self::DEPS => 'Mise à jour stack',
            self::BACKUP => 'Sauvegarde',
            self::DATA_SYNC => 'Synchronisation données',
            self::RULES_DOWNLOADS => 'Compilation du livre de règles',
            default => $domain,
        };
    }

    public static function pageUrl(string $domain): string
    {
        return match ($domain) {
            self::REVIEW => route('admin.project-review.index'),
            self::CLEAR => route('admin.project-clear.index'),
            self::DEPS => route('admin.project-update.index'),
            self::BACKUP => route('admin.backup.index'),
            self::DATA_SYNC => route('admin.content.dofusdb.index'),
            self::RULES_DOWNLOADS => route('admin.content.dashboard.index'),
            default => '/admin',
        };
    }

    public static function busyMessage(string $domain): string
    {
        return 'Un job « '.self::label($domain).' » est déjà en cours. Attendez la fin avant d’en lancer un autre.';
    }
}
