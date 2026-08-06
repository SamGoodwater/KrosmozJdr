<?php

declare(strict_types=1);

namespace App\Console\Commands\Scrapping\Effects;

use App\Console\ArtisanExitCode;
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
 * @see docs/features/effects/README.md
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

    /**
     * DofusDB characteristic id → characteristic_key Krosmoz (effets de sorts / créature).
     * Source : DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md, CARACTERISTIQUES_EFFETS_PAR_ACTION.md.
     * Les clés sont en format court (spell/creature) ; ajoute _spell ou _creature si ta BDD l’exige.
     */
    private const DOFUSDB_CHARACTERISTIC_ID_TO_KROSMOZ_KEY = [
        1 => 'pa',
        10 => 'strong',
        11 => 'vitality',
        12 => 'sagesse',
        13 => 'chance',
        14 => 'agi',
        15 => 'intel',
        16 => 'do_fixe_multiple',
        18 => 'critical',
        19 => 'po',
        23 => 'pm',
        25 => 'power_spell',
        26 => 'invocation',
        27 => 'esquive_pa',
        28 => 'esquive_pm',
        33 => 'res_terre',
        34 => 'res_feu',
        35 => 'res_eau',
        36 => 'res_air',
        37 => 'res_neutre',
        44 => 'ini',
        49 => 'heal_bonus',
        54 => 'res_fixe_terre',
        55 => 'res_fixe_feu',
        56 => 'res_fixe_eau',
        57 => 'res_fixe_air',
        58 => 'res_fixe_neutre',
        78 => 'fuite',
        79 => 'tacle',
        85 => 'poussée',
        87 => 'critiques',
        88 => 'do_terre',
        89 => 'do_feu',
        90 => 'do_eau',
        91 => 'do_air',
        92 => 'do_neutre',
        // Jouables Krosmoz sans équivalent exact Dofus→échelle, mais utiles en effets de sort.
        98 => 'mastery_bonus',
        132 => 'tacle',
        133 => 'fuite',
    ];

    /**
     * Caractéristiques Dofus sans équivalent Krosmoz (hors périmètre conversion).
     * On conserve l’effet en revue manuelle (`autre` / none) plutôt qu’un booster sans clé.
     *
     * @var list<int>
     */
    private const OUT_OF_SCOPE_CHARACTERISTIC_IDS = [
        20, 21, 24, 38, 39, 48, 52, 71, 72, 73, 74, 75,
        99, 100, 101, 103, 106, 110, 121, 124,
        126, 127, 128, 129, 130, 131, 140, 141, 142,
    ];

    public function handle(DofusDbClient $client): int
    {
        $lang = (string) $this->option('lang');
        $limit = (int) $this->option('limit');
        $outputPath = $this->option('output');
        $skipCache = (bool) $this->option('no-cache');

        $this->info('Récupération des effets depuis '.self::BASE_URL.' (lang='.$lang.')…');

        $allEffects = $this->fetchAllEffects($client, $lang, $limit, $skipCache);
        $this->info('Effets récupérés : '.count($allEffects));

        $mappings = $this->buildMappingsFromEffects($allEffects);

        $php = $this->formatMappingsAsPhp($mappings, $allEffects);

        if ($outputPath !== null && $outputPath !== '') {
            $dir = dirname($outputPath);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($outputPath, $php);
            $this->info('Écrit : '.$outputPath);
        } else {
            $this->line($php);
        }

        return ArtisanExitCode::SUCCESS;
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
            $url = self::BASE_URL.'?$limit='.$limit.'&$skip='.$skip.'&lang='.$lang;
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

            $this->line('  … page suivante (skip='.$skip.')');
        } while (true);

        return $all;
    }

    /**
     * @param  list<array{id: int, category: int, elementId: int, characteristic: int, description_fr: string, boost: bool}>  $effects
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
            $characteristicId = (int) ($e['characteristic'] ?? 0);

            // États : laissés hors mapping pour que la conversion utilise appliquer-etat via le catalogue.
            if (str_contains($desc, 'état #') || str_contains($desc, 'etat #') || str_contains($desc, 'state #')) {
                continue;
            }

            // Dissipation / nettoyage d’états, sans caractéristique convertible.
            if (str_contains($desc, 'envoûtement') || str_contains($desc, 'envoutement')
                || str_contains($desc, 'enlève les') && str_contains($desc, 'état')) {
                $mappings[$id] = ['autre', 'none', null];

                continue;
            }

            // Caractéristiques hors périmètre Krosmoz : revue manuelle, pas de clé inventée.
            if ($characteristicId > 0 && in_array($characteristicId, self::OUT_OF_SCOPE_CHARACTERISTIC_IDS, true)) {
                $mappings[$id] = ['autre', 'none', null];

                continue;
            }

            // Déplacement avant les dommages : « Repousse … (sans dommages) » contient le mot dommages.
            if (str_contains($desc, 'repousse') || str_contains($desc, 'pousse') || str_contains($desc, 'attire')
                || str_contains($desc, 'téléporte') || str_contains($desc, 'teleporte')
                || str_contains($desc, 'avance de') || str_contains($desc, 'recule de')
                || (str_contains($desc, 'case') && (str_contains($desc, 'de ') || preg_match('/#\d+\s*case/', $desc) === 1)
                    && (str_contains($desc, 'repouss') || str_contains($desc, 'attir') || str_contains($desc, 'pouss')
                        || str_contains($desc, 'avance') || str_contains($desc, 'recule') || str_contains($desc, 'téléport')
                        || str_contains($desc, 'teleport') || str_contains($desc, 'saute') || str_contains($desc, 'bond')))) {
                $mappings[$id] = ['déplacer', 'none', null];

                continue;
            }

            // Dommages élémentaires (category 2, elementId 0–4) ou description "dommage"
            // « sans dommages » est un déplacement, déjà traité ci-dessus.
            if (($category === 2 && $elementId >= 0 && $elementId <= 4)
                || ((str_contains($desc, 'dommage') || str_contains($desc, 'dégât')) && ! str_contains($desc, 'sans dommage'))) {
                $mappings[$id] = ['frapper', 'element', null];

                continue;
            }

            // Soin
            if (str_contains($desc, 'soin') || str_contains($desc, 'vie') && (str_contains($desc, 'rend') || str_contains($desc, 'récup'))) {
                $mappings[$id] = ['soigner', 'element', null];

                continue;
            }

            // Vol de vie → même sous-effet que les dommages + life_steal_formula ([dgt]) côté conversion
            if (str_contains($desc, 'vol de vie') || (str_contains($desc, 'vole') && str_contains($desc, 'vie'))) {
                $mappings[$id] = ['frapper', 'element', null];

                continue;
            }

            // Invocation
            if (str_contains($desc, 'invocation') || str_contains($desc, 'invoque') || str_contains($desc, 'invoc')) {
                $mappings[$id] = ['invoquer', 'none', null];

                continue;
            }

            // Vol de caractéristiques (PA, PM, PO)
            if ((str_contains($desc, 'vol') || str_contains($desc, 'vole'))
                && (str_contains($desc, 'pa') || str_contains($desc, 'pm') || str_contains($desc, 'portée') || str_contains($desc, 'point'))) {
                $mappings[$id] = $this->mappingWithOptionalCharacteristic(
                    'voler-caracteristiques',
                    $this->resolveCharacteristicKeyForEffect($e, $desc)
                );

                continue;
            }

            // PV temporaires (charac 95 maxLifePoints) — avant bouclier / boost.
            if ($characteristicId === 95
                || str_contains($desc, 'vie temporaire')
                || str_contains($desc, 'pv temporaire')
                || str_contains($desc, 'points de vie temporaire')
                || str_contains($desc, 'maxlifepoints')
                || (str_contains($desc, 'n\'enlève pas') && str_contains($desc, 'vie'))) {
                $mappings[$id] = ['donner-pv-temporaires', 'none', null];

                continue;
            }

            // Protection / bouclier. Avant `boost` : DofusDB marque aussi ces effets avec boost=true.
            if (str_contains($desc, 'protège') || str_contains($desc, 'bouclier') || str_contains($desc, 'absorption')) {
                $mappings[$id] = ['protéger', 'none', null];

                continue;
            }

            // Retrait / malus. À tester avant `boost` : DofusDB marque aussi les variantes négatives comme boost.
            if (str_contains($desc, 'retrait') || str_contains($desc, 'retire') || str_contains($desc, 'enlève')
                || $this->isNegativeCharacteristicDescription($desc, (int) ($e['characteristic'] ?? 0))) {
                $mappings[$id] = $this->mappingWithOptionalCharacteristic(
                    'retirer',
                    $this->resolveCharacteristicKeyForEffect($e, $desc)
                );

                continue;
            }

            // Boost (ajout de caractéristique)
            if ($boost || str_contains($desc, 'ajout') && (str_contains($desc, 'pa') || str_contains($desc, 'pm') || str_contains($desc, 'caractéristique'))
                || str_contains($desc, 'bonus') && str_contains($desc, 'portée')) {
                $mappings[$id] = $this->mappingWithOptionalCharacteristic(
                    'booster',
                    $this->resolveCharacteristicKeyForEffect($e, $desc)
                );

                continue;
            }

            // Non mappé : on ne met pas dans le tableau (reste en « autre » côté conversion)
            // Pour inclure explicitement en « autre » : $mappings[$id] = ['autre', 'none', null];
        }

        ksort($mappings, SORT_NUMERIC);

        return $mappings;
    }

    /**
     * Détecte les variantes négatives DofusDB (`-#1...`) qui partagent le flag boost des bonus.
     */
    private function isNegativeCharacteristicDescription(string $description, int $characteristicId): bool
    {
        if ($characteristicId <= 0) {
            return false;
        }

        return str_contains($description, '-#')
            || str_contains($description, 'réduit')
            || str_contains($description, 'diminue')
            || str_contains($description, 'malus');
    }

    /**
     * @param  array{characteristic?: int}  $effect
     */
    private function resolveCharacteristicKeyForEffect(array $effect, string $description): ?string
    {
        $resolved = $this->resolveCharacteristicKey((int) ($effect['characteristic'] ?? 0));
        if ($resolved !== null) {
            return $resolved;
        }
        if (preg_match('/\bpa\b/u', $description) === 1) {
            return 'pa';
        }
        if (preg_match('/\bpm\b/u', $description) === 1) {
            return 'pm';
        }
        if (str_contains($description, 'portée') || str_contains($description, 'portee')) {
            return 'po';
        }
        if (str_contains($description, 'critique')) {
            return 'critical';
        }

        return null;
    }

    /**
     * Sans clé convertible, l’effet passe en revue manuelle (`autre`) pour ne pas polluer l’audit.
     *
     * @return array{0: string, 1: string, 2: string|null}
     */
    private function mappingWithOptionalCharacteristic(string $slug, ?string $characteristicKey): array
    {
        if ($characteristicKey === null || $characteristicKey === '') {
            return ['autre', 'none', null];
        }

        return [$slug, 'characteristic', $characteristicKey];
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
     * @param  array<int, array{0: string, 1: string, 2: string|null}>  $mappings
     * @param  list<array{id: int, description_fr: string, characteristic: int}>  $effects  Liste des effets (pour commentaires dans le fichier)
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
            $keyPhp = $key === null ? 'null' : "'".addslashes($key)."'";
            $info = $byId[$id] ?? null;
            $desc = $info['description_fr'] ?? '';
            $caracId = $info['characteristic'] ?? 0;
            $comment = '';
            if ($desc !== '' || ($source === 'characteristic' && $caracId > 0)) {
                $parts = [];
                if ($desc !== '') {
                    $parts[] = str_replace(["\r", "\n"], ' ', mb_substr($desc, 0, 70)).(mb_strlen($desc) > 70 ? '…' : '');
                }
                if ($source === 'characteristic' && $caracId > 0) {
                    $parts[] = 'carac DofusDB id='.$caracId;
                }
                $comment = ' // '.$id.' — '.implode(' ; ', $parts);
            }
            $lines[] = '    '.$id.' => [\''.addslashes($slug).'\', \''.addslashes($source).'\', '.$keyPhp.'],'.$comment;
        }

        $lines[] = '];';

        return implode("\n", $lines)."\n";
    }
}
