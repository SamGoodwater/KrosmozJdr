<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Console\Command;

/**
 * Récupère la liste des effets depuis l’API DofusDB et propose des mappings vers les sous-effets Krosmoz.
 *
 * Sortie : tableau PHP (effectId => [sub_effect_slug, characteristic_source, characteristic_key])
 * à coller dans DofusdbEffectMappingSeeder::MAPPINGS ou à écrire dans un fichier.
 *
 * Usage :
 *   php artisan scrapping:effects:map
 *   php artisan scrapping:effects:map --output=database/seeders/data/dofusdb_effect_mappings.php
 *   php artisan scrapping:effects:map --lang=fr --no-cache
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_MAPPING_EFFETS.md
 */
class ScrappingEffectsMapCommand extends Command
{
    protected $signature = 'scrapping:effects:map
                            {--lang=fr : Langue pour les descriptions (détection du mapping)}
                            {--limit=100 : Nombre d’effets par page API}
                            {--output= : Fichier PHP où écrire le tableau (sinon stdout)}
                            {--no-cache : Ignorer le cache HTTP}';

    protected $description = 'Récupère les effets DofusDB via l’API et génère des propositions de mapping pour le seeder';
    protected $aliases = ['dofusdb:fetch-effect-mappings'];

    private const BASE_URL = 'https://api.dofusdb.fr/effects';

    /** Sous-effets Krosmoz connus (slug). */
    private const SUB_EFFECTS = [
        'frapper', 'soigner', 'protéger', 'voler-vie', 'booster', 'retirer',
        'voler-caracteristiques', 'invoquer', 'déplacer', 'autre',
    ];

    /**
     * DofusDB characteristic id → characteristic_key Krosmoz (effets de sorts / créature).
     * Source : DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md, CARACTERISTIQUES_EFFETS_PAR_ACTION.md.
     * Les clés sont en format court (spell/creature) ; ajoute _spell ou _creature si ta BDD l’exige.
     */
    private const DOFUSDB_CHARACTERISTIC_ID_TO_KROSMOZ_KEY = [
        0 => 'pv',
        1 => 'pa',
        5 => 'level',
        10 => 'strong',
        11 => 'vitality',
        12 => 'sagesse',
        13 => 'chance',
        14 => 'agi',
        15 => 'intel',
        18 => 'critical',
        19 => 'po',
        23 => 'pm',
        26 => 'invocation',
        33 => 'res_terre',
        34 => 'res_feu',
        35 => 'res_eau',
        36 => 'res_air',
        37 => 'res_neutre',
        39 => 'echec_critique',
        44 => 'ini',
        78 => 'fuite',
        79 => 'tacle',
        82 => 'retrait_pa',
        83 => 'retrait_pm',
        88 => 'do_terre',
        89 => 'do_feu',
        90 => 'do_eau',
        91 => 'do_air',
        92 => 'do_neutre',
        103 => 'do_fixe_multiple',
    ];

    public function handle(DofusDbClient $client): int
    {
        $lang = (string) $this->option('lang');
        $limit = (int) $this->option('limit');
        $outputPath = $this->option('output');
        $skipCache = (bool) $this->option('no-cache');

        $this->info('Récupération des effets depuis ' . self::BASE_URL . ' (lang=' . $lang . ')…');

        $allEffects = $this->fetchAllEffects($client, $lang, $limit, $skipCache);
        $this->info('Effets récupérés : ' . count($allEffects));

        $mappings = $this->buildMappingsFromEffects($allEffects);

        $php = $this->formatMappingsAsPhp($mappings, $allEffects);

        if ($outputPath !== null && $outputPath !== '') {
            $dir = dirname($outputPath);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($outputPath, $php);
            $this->info('Écrit : ' . $outputPath);
        } else {
            $this->line($php);
        }

        return self::SUCCESS;
    }

    /**
     * @return list<array{id: int, category: int, elementId: int, characteristic: int, description_fr: string, boost: bool}>
     */
    private function fetchAllEffects(DofusDbClient $client, string $lang, int $limit, bool $skipCache): array
    {
        $all = [];
        $skip = 0;
        $options = $skipCache ? ['skip_cache' => true] : [];

        do {
            $url = self::BASE_URL . '?$limit=' . $limit . '&$skip=' . $skip . '&lang=' . $lang;
            $response = $client->getJson($url, $options);

            $total = (int) ($response['total'] ?? 0);
            $data = $response['data'] ?? [];

            foreach ($data as $row) {
                $id = (int) ($row['id'] ?? 0);
                if ($id === 0) {
                    continue;
                }
                $desc = $row['description'] ?? [];
                $descFr = is_array($desc) ? ($desc['fr'] ?? $desc['en'] ?? '') : '';
                $all[] = [
                    'id' => $id,
                    'category' => (int) ($row['category'] ?? 0),
                    'elementId' => (int) ($row['elementId'] ?? -1),
                    'characteristic' => (int) ($row['characteristic'] ?? 0),
                    'description_fr' => (string) $descFr,
                    'boost' => (bool) ($row['boost'] ?? false),
                ];
            }

            $skip += count($data);
            if (count($data) === 0 || $skip >= $total) {
                break;
            }

            $this->line('  … page suivante (skip=' . $skip . ')');
        } while (true);

        return $all;
    }

    /**
     * @param list<array{id: int, category: int, elementId: int, description_fr: string, boost: bool}> $effects
     * @return array<int, array{0: string, 1: string, 2: string|null}>
     */
    private function buildMappingsFromEffects(array $effects): array
    {
        $mappings = [];

        foreach ($effects as $e) {
            $id = $e['id'];
            $desc = mb_strtolower($e['description_fr']);
            $category = $e['category'];
            $elementId = $e['elementId'];
            $boost = $e['boost'];

            // Dommages élémentaires (category 2, elementId 0–4) ou description "dommage"
            if (($category === 2 && $elementId >= 0 && $elementId <= 4) || str_contains($desc, 'dommage') || str_contains($desc, 'dégât')) {
                $mappings[$id] = ['frapper', 'element', null];
                continue;
            }

            // Soin
            if (str_contains($desc, 'soin') || str_contains($desc, 'vie') && (str_contains($desc, 'rend') || str_contains($desc, 'récup'))) {
                $mappings[$id] = ['soigner', 'element', null];
                continue;
            }

            // Vol de vie
            if (str_contains($desc, 'vol de vie') || str_contains($desc, 'vole') && str_contains($desc, 'vie')) {
                $mappings[$id] = ['voler-vie', 'element', null];
                continue;
            }

            // Déplacement : repousse, attire, téléporte (case), pousse
            if (str_contains($desc, 'repousse') || str_contains($desc, 'pousse') || str_contains($desc, 'attire')
                || (str_contains($desc, 'téléporte') && str_contains($desc, 'case'))
                || str_contains($desc, 'case') && (str_contains($desc, 'de ') || preg_match('/#\d+\s*case/', $desc))) {
                $mappings[$id] = ['déplacer', 'none', null];
                continue;
            }

            // Invocation
            if (str_contains($desc, 'invocation') || str_contains($desc, 'invoque') || str_contains($desc, 'invoc')) {
                $mappings[$id] = ['invoquer', 'none', null];
                continue;
            }

            // Boost (ajout de caractéristique)
            if ($boost || str_contains($desc, 'ajout') && (str_contains($desc, 'pa') || str_contains($desc, 'pm') || str_contains($desc, 'caractéristique'))
                || str_contains($desc, 'bonus') && str_contains($desc, 'portée')) {
                $mappings[$id] = ['booster', 'characteristic', $this->resolveCharacteristicKey($e['characteristic'] ?? 0)];
                continue;
            }

            // Retrait (PA, PM, etc.)
            if (str_contains($desc, 'retrait') || str_contains($desc, 'retire') || str_contains($desc, 'enlève')) {
                $mappings[$id] = ['retirer', 'characteristic', $this->resolveCharacteristicKey($e['characteristic'] ?? 0)];
                continue;
            }

            // Vol de caractéristiques (PA, PM)
            if (str_contains($desc, 'vol') && (str_contains($desc, 'pa') || str_contains($desc, 'pm') || str_contains($desc, 'point'))) {
                $mappings[$id] = ['voler-caracteristiques', 'characteristic', $this->resolveCharacteristicKey($e['characteristic'] ?? 0)];
                continue;
            }

            // Protection / bouclier
            if (str_contains($desc, 'protège') || str_contains($desc, 'bouclier') || str_contains($desc, 'absorption')) {
                $mappings[$id] = ['protéger', 'none', null];
                continue;
            }

            // Non mappé : on ne met pas dans le tableau (reste en « autre » côté conversion)
            // Pour inclure explicitement en « autre » : $mappings[$id] = ['autre', 'none', null];
        }

        ksort($mappings, SORT_NUMERIC);

        return $mappings;
    }

    /** Retourne la characteristic_key Krosmoz pour un id caractéristique DofusDB, ou null si inconnu. */
    private function resolveCharacteristicKey(int $dofusdbCharacteristicId): ?string
    {
        if ($dofusdbCharacteristicId <= 0) {
            return null;
        }

        return self::DOFUSDB_CHARACTERISTIC_ID_TO_KROSMOZ_KEY[$dofusdbCharacteristicId] ?? null;
    }

    /**
     * @param array<int, array{0: string, 1: string, 2: string|null}> $mappings
     * @param list<array{id: int, description_fr: string, characteristic: int}> $effects Liste des effets (pour commentaires dans le fichier)
     */
    private function formatMappingsAsPhp(array $mappings, array $effects = []): string
    {
        $byId = [];
        foreach ($effects as $e) {
            $byId[$e['id']] = $e;
        }

        $lines = [
            '<?php',
            '',
            'declare(strict_types=1);',
            '',
            '/**',
            ' * Mappings effectId DofusDB → [sub_effect_slug, characteristic_source, characteristic_key].',
            ' * Généré par : php artisan scrapping:effects:map --output=database/seeders/data/dofusdb_effect_mappings_suggested.php',
            ' * Utilisé par DofusdbEffectMappingSeeder si le fichier existe.',
            ' * characteristic_key est rempli automatiquement quand l’effet a une caractéristique DofusDB connue (voir DOFUSDB_CHARACTERISTIC_ID_TO_KROSMOZ_KEY dans la commande).',
            ' * Commentaires : description FR (API) + si source=characteristic, id carac. DofusDB.',
            ' */',
            '',
            'return [',
        ];

        foreach ($mappings as $id => $triple) {
            [$slug, $source, $key] = $triple;
            $keyPhp = $key === null ? 'null' : "'" . addslashes($key) . "'";
            $info = $byId[$id] ?? null;
            $desc = $info['description_fr'] ?? '';
            $caracId = $info['characteristic'] ?? 0;
            $comment = '';
            if ($desc !== '' || ($source === 'characteristic' && $caracId > 0)) {
                $parts = [];
                if ($desc !== '') {
                    $parts[] = str_replace(["\r", "\n"], ' ', mb_substr($desc, 0, 70)) . (mb_strlen($desc) > 70 ? '…' : '');
                }
                if ($source === 'characteristic' && $caracId > 0) {
                    $parts[] = 'carac DofusDB id=' . $caracId;
                }
                $comment = ' // ' . $id . ' — ' . implode(' ; ', $parts);
            }
            $lines[] = '    ' . $id . ' => [\'' . addslashes($slug) . '\', \'' . addslashes($source) . '\', ' . $keyPhp . '],' . $comment;
        }

        $lines[] = '];';

        return implode("\n", $lines);
    }
}
