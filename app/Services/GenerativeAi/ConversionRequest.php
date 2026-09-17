<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Models\User;

/**
 * Demande de conversion (1 paquet = 1 requête).
 *
 * @example new ConversionRequest(action: 'encounter', entityType: 'monster', entityId: 12)
 */
final readonly class ConversionRequest
{
    public function __construct(
        public string $action,
        public string $entityType,
        public ?int $entityId,
        public ?string $brief = null,
        public bool $force = false,
        public ?int $userId = null,
        public ?int $runId = null,
    ) {}

    /**
     * Refuse les invités et tout rôle < admin (y compris un job CLI sans --user).
     */
    public static function assertUserMayGenerate(?User $user): void
    {
        if ($user === null || ! $user->isAdmin()) {
            abort(403, 'Seuls les administrateurs peuvent lancer une conversion IA.');
        }
    }
}
