<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

/**
 * Estimations d’ordre de grandeur (docs/IA/COUTS.md, Sonnet 5 après cache).
 *
 * PNJ >> rencontre >> sort effet >> mise à jour objet.
 *
 * @example CostEstimator::forAction('encounter')['usd'];
 */
final class CostEstimator
{
    /** @var array<string, array{usd: float, label: string, hint: string}> */
    public const ACTIONS = [
        'npc' => [
            'usd' => 0.35,
            'label' => 'PNJ (fiche complète)',
            'hint' => 'Le plus cher : identité + kit + listes préfiltrées.',
        ],
        'encounter' => [
            'usd' => 0.10,
            'label' => 'Rencontre (monstre + 2–3 sorts)',
            'hint' => 'Un paquet cohérent, un appel.',
        ],
        'spell' => [
            'usd' => 0.05,
            'label' => 'Sort (texte d’effets)',
            'hint' => 'Delta sur le champ effect seulement.',
        ],
        'item' => [
            'usd' => 0.03,
            'label' => 'Équipement (mise à jour / unique)',
            'hint' => 'La grille algo reste le volume ; le LLM sert le retravail.',
        ],
        'consumable' => [
            'usd' => 0.03,
            'label' => 'Consommable',
            'hint' => 'Même ordre de grandeur qu’un objet.',
        ],
    ];

    /**
     * @return list<array{action: string, usd: float, label: string, hint: string, formatted: string}>
     */
    public function all(): array
    {
        $out = [];
        foreach (self::ACTIONS as $action => $row) {
            $out[] = [
                'action' => $action,
                ...$row,
                'formatted' => $this->formatUsd($row['usd']),
            ];
        }

        return $out;
    }

    /**
     * @return array{action: string, usd: float, label: string, hint: string, formatted: string}|null
     */
    public function forAction(string $action): ?array
    {
        $row = self::ACTIONS[$action] ?? null;
        if ($row === null) {
            return null;
        }

        return [
            'action' => $action,
            ...$row,
            'formatted' => $this->formatUsd($row['usd']),
        ];
    }

    public function formatUsd(float $usd): string
    {
        return '~ '.number_format($usd, 2, ',', ' ').' $';
    }

    public function actionForEntityType(string $entityType): string
    {
        return match ($entityType) {
            'monster', 'monsters' => 'encounter',
            'spell', 'spells' => 'spell',
            'npc', 'npcs' => 'npc',
            'item', 'items' => 'item',
            'consumable', 'consumables' => 'consumable',
            default => $entityType,
        };
    }
}
