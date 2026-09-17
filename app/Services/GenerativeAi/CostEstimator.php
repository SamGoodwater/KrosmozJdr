<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

/**
 * Estimations d’ordre de grandeur (docs/IA/COUTS.md, après cache).
 *
 * Défaut Haiku 4.5 (moitié de Sonnet). PNJ >> rencontre >> sort effet >> mise à jour objet.
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
    public function all(?string $model = null): array
    {
        $factor = AnthropicModelCatalog::costFactorVersusSonnet(
            $model ?? AnthropicModelCatalog::DEFAULT
        );
        $out = [];
        foreach (self::ACTIONS as $action => $row) {
            $usd = round($row['usd'] * $factor, 2);
            $out[] = [
                'action' => $action,
                ...$row,
                'usd' => $usd,
                'formatted' => $this->formatUsd($usd),
            ];
        }

        return $out;
    }

    /**
     * @return array{action: string, usd: float, label: string, hint: string, formatted: string}|null
     */
    public function forAction(string $action, ?string $model = null): ?array
    {
        $row = self::ACTIONS[$action] ?? null;
        if ($row === null) {
            return null;
        }
        $factor = AnthropicModelCatalog::costFactorVersusSonnet(
            $model ?? AnthropicModelCatalog::DEFAULT
        );
        $usd = round($row['usd'] * $factor, 2);

        return [
            'action' => $action,
            ...$row,
            'usd' => $usd,
            'formatted' => $this->formatUsd($usd),
        ];
    }

    public function formatUsd(float $usd): string
    {
        return '~ '.number_format($usd, 2, ',', ' ').' $';
    }

    /**
     * Combien d’actions approximatives avec un crédit restant (ordre de grandeur).
     */
    public function remainingHint(?float $remainingUsd, ?string $model = null): ?string
    {
        if ($remainingUsd === null || $remainingUsd < 0) {
            return null;
        }

        $factor = AnthropicModelCatalog::costFactorVersusSonnet(
            $model ?? AnthropicModelCatalog::DEFAULT
        );
        $encounter = self::ACTIONS['encounter']['usd'] * $factor;
        $npc = self::ACTIONS['npc']['usd'] * $factor;
        $encounters = $encounter > 0 ? (int) floor($remainingUsd / $encounter) : 0;
        $npcs = $npc > 0 ? (int) floor($remainingUsd / $npc) : 0;

        return '≈ '.$encounters.' rencontres ou '.$npcs.' PNJ';
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
