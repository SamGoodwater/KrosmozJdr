<?php

namespace App\Services\Scrapping\V2\Integration;

/**
 * Résultat d’une intégration (création/mise à jour en base ou simulation).
 */
final class IntegrationResult
{
    public function __construct(
        private bool $success,
        private ?int $creatureId = null,
        private ?int $monsterId = null,
        private string $creatureAction = '',
        private string $monsterAction = '',
        private string $message = '',
        private array $data = []
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getCreatureId(): ?int
    {
        return $this->creatureId;
    }

    public function getMonsterId(): ?int
    {
        return $this->monsterId;
    }

    public function getCreatureAction(): string
    {
        return $this->creatureAction;
    }

    public function getMonsterAction(): string
    {
        return $this->monsterAction;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /** @return array<string, mixed> */
    public function getData(): array
    {
        return $this->data;
    }

    /** Identifiant de l’entité intégrée (pour spell, class, item). Pour monster, préférer getCreatureId() / getMonsterId(). */
    public function getPrimaryId(): ?int
    {
        $id = $this->data['id'] ?? null;

        return is_numeric($id) ? (int) $id : null;
    }

    /** Action réalisée (created, updated, skipped, etc.) pour une entité générique. */
    public function getPrimaryAction(): string
    {
        return (string) ($this->data['action'] ?? '');
    }

    public static function ok(
        ?int $creatureId,
        ?int $monsterId,
        string $creatureAction,
        string $monsterAction,
        string $message = '',
        array $data = []
    ): self {
        return new self(true, $creatureId, $monsterId, $creatureAction, $monsterAction, $message, $data);
    }

    /** Résultat pour une entité générique (spell, class, item). */
    public static function okEntity(int $id, string $action, string $message = '', array $data = []): self
    {
        return new self(true, null, null, '', '', $message, array_merge($data, ['id' => $id, 'action' => $action]));
    }

    public static function fail(string $message, array $data = []): self
    {
        return new self(false, null, null, '', '', $message, $data);
    }
}
