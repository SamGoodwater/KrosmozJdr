<?php

namespace App\Console\Commands;

use App\Models\Entity\Capability;
use App\Support\ElementConstants;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Importe les capacités depuis un export JSON PHPMyAdmin de l'ancienne base.
 *
 * Format supporté : export JSON du plugin "Export to JSON" pour PHPMyAdmin
 * (array racine avec objets type=header|database|table).
 *
 * Mapping ancien → nouveau :
 * - id, uniqid, timestamp_* : non conservés (nouveaux IDs auto)
 * - usable "1" → state "playable", usable "0" → state "draft"
 * - poéditables "0"/"1" → po_editable bool
 * - Valeurs par défaut pour read_level, write_level, created_by
 *
 * @example
 * php artisan capabilities:import-legacy database/seeders/data/capability.json --dry-run
 * php artisan capabilities:import-legacy database/seeders/data/capability.json --force-update
 */
class ImportLegacyCapabilitiesCommand extends Command
{
    protected $signature = 'capabilities:import-legacy
        {file : Chemin vers le fichier JSON (export PHPMyAdmin)}
        {--dry-run : Affiche le plan sans écrire en base}
        {--force-update : Met à jour les capacités existantes (même nom) au lieu de les ignorer}';

    protected $description = 'Importe les capacités depuis un export JSON PHPMyAdmin de l\'ancienne base';

    public function handle(): int
    {
        $path = (string) $this->argument('file');
        if (! is_file($path)) {
            $this->error("Fichier introuvable : {$path}");

            return self::FAILURE;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            $this->error('Impossible de lire le fichier.');

            return self::FAILURE;
        }

        $rows = $this->extractCapabilityRows($raw);
        if ($rows === null) {
            $this->error('Format JSON invalide ou aucune donnée capability trouvée.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $forceUpdate = (bool) $this->option('force-update');

        $this->info(sprintf('Capacités à traiter : %d', count($rows)));
        if ($dryRun) {
            $this->warn('Mode dry-run : aucun écriture en base.');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        if (! $dryRun) {
            DB::beginTransaction();
        }

        try {
            foreach ($rows as $i => $row) {
                $result = $this->importOne($row, $forceUpdate, $dryRun);
                match ($result['action']) {
                    'created' => $created++,
                    'updated' => $updated++,
                    'skipped' => $skipped++,
                    'error' => $errors[] = sprintf('#%d "%s" : %s', $i + 1, $row['name'] ?? '?', $result['message'] ?? 'Erreur inconnue'),
                };
            }

            if (! $dryRun) {
                DB::commit();
            }

            $this->newLine();
            $this->info('Import terminé.');
            $this->table(
                ['Action', 'Count'],
                [
                    ['Créées', $created],
                    ['Mises à jour', $updated],
                    ['Ignorées (existant)', $skipped],
                    ['Erreurs', count($errors)],
                ]
            );

            if (count($errors) > 0) {
                $this->newLine();
                $this->error('Erreurs :');
                foreach ($errors as $err) {
                    $this->line("  - {$err}");
                }

                return self::FAILURE;
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            if (! $dryRun) {
                DB::rollBack();
            }
            $this->error('Import interrompu : ' . $e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * Extrait le tableau de lignes capability depuis un export PHPMyAdmin.
     *
     * @return array<int, array<string, mixed>>|null
     */
    private function extractCapabilityRows(string $raw): ?array
    {
        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return null;
        }

        foreach ($decoded as $item) {
            if (is_array($item) && ($item['type'] ?? '') === 'table' && ($item['name'] ?? '') === 'capability') {
                $data = $item['data'] ?? null;

                return is_array($data) ? $data : null;
            }
        }

        return null;
    }

    /**
     * Importe une capacité et retourne le résultat.
     *
     * @param  array<string, mixed>  $row  Ligne brute de l'export
     * @return array{action: 'created'|'updated'|'skipped'|'error', message?: string}
     */
    private function importOne(array $row, bool $forceUpdate, bool $dryRun): array
    {
        $mapped = $this->mapLegacyRow($row);
        if ($mapped === null) {
            return ['action' => 'error', 'message' => 'Données invalides (nom manquant)'];
        }

        $name = (string) $mapped['name'];
        $existing = Capability::withoutTrashed()->where('name', $name)->first();

        if ($existing !== null) {
            if (! $forceUpdate) {
                return ['action' => 'skipped'];
            }
            if (! $dryRun) {
                $existing->update($mapped);
            }

            return ['action' => 'updated'];
        }

        if (! $dryRun) {
            Capability::create($mapped);
        }

        return ['action' => 'created'];
    }

    /**
     * Mappe une ligne de l'ancien format vers les champs du nouveau modèle.
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>|null
     */
    private function mapLegacyRow(array $row): ?array
    {
        $name = trim((string) ($row['name'] ?? ''));
        if ($name === '') {
            return null;
        }

        $usable = (string) ($row['usable'] ?? '0');
        $state = $usable === '1' ? Capability::STATE_PLAYABLE : Capability::STATE_DRAFT;

        $poEditable = $this->toBool($row['po_editable'] ?? '0');
        $isMagic = $this->toBool($row['is_magic'] ?? '0');
        $ritualAvailable = $this->toBool($row['ritual_available'] ?? '1');

        return [
            'name' => $name,
            'description' => $this->nullIfEmpty($row['description'] ?? null),
            'effect' => $this->nullIfEmpty($row['effect'] ?? null),
            'level' => $this->stringOrDefault($row['level'] ?? null, '0'),
            'pa' => $this->nullIfEmpty($row['pa'] ?? null) ?? '0',
            'po' => $this->stringOrDefault($row['po'] ?? null, ''),
            'po_editable' => $poEditable,
            'time_before_use_again' => $this->stringOrDefault($row['time_before_use_again'] ?? null, ''),
            'casting_time' => $this->nullIfEmpty($row['casting_time'] ?? null) ?? '',
            'duration' => $this->nullIfEmpty($row['duration'] ?? null) ?? '',
            'element' => $this->convertElement($row['element'] ?? null),
            'is_magic' => $isMagic,
            'ritual_available' => $ritualAvailable,
            'powerful' => $this->nullIfEmpty($row['powerful'] ?? null),
            'state' => $state,
            'read_level' => 0,
            'write_level' => 3,
            'image' => null,
            'created_by' => null,
        ];
    }

    private function toBool(mixed $val): bool
    {
        if (is_bool($val)) {
            return $val;
        }

        return in_array((string) $val, ['1', 'true', 'yes', 'on'], true);
    }

    private function nullIfEmpty(mixed $val): ?string
    {
        $s = $val === null ? '' : trim((string) $val);

        return $s === '' ? null : $s;
    }

    private function stringOrDefault(mixed $val, string $default): string
    {
        $s = $this->nullIfEmpty($val);

        return $s ?? $default;
    }

    private function convertElement(mixed $val): int
    {
        if ($val === null || $val === '') {
            return 0;
        }

        $key = is_numeric($val) ? (string) (int) $val : strtolower(trim((string) $val));
        $mapping = ElementConstants::LEGACY_STRING_TO_INT;

        return $mapping[$key] ?? 0;
    }
}
