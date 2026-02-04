<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Concerns\GuardsProductionEnvironment;
use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use App\Models\EquipmentSlot;
use App\Models\SpellEffectType;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

/**
 * Exporte les données de la BDD vers database/seeders/data/ pour que les seeders
 * utilisent ces fichiers comme source (au lieu de config/).
 *
 * À lancer après modification des caractéristiques / formules / types d'effets via l'interface.
 * Crée une sauvegarde ZIP des fichiers existants avant export, puis nettoie les backups > 7 ou > 7 jours.
 *
 * Disponible uniquement en environnement local et testing (désactivé en production pour limiter la surface d'attaque).
 */
class ExportSeederDataCommand extends Command
{
    use GuardsProductionEnvironment;

    private const BACKUP_DIR = 'seeders-data-backups';

    private const BACKUP_MAX_COUNT = 7;

    private const BACKUP_MAX_AGE_DAYS = 7;

    protected $signature = 'db:export-seeder-data
                            {--characteristics : Exporter uniquement characteristics}
                            {--formulas : Exporter les formules de conversion (tables characteristic_creature/object/spell)}
                            {--spell-effect-types : Exporter uniquement spell_effect_types}
                            {--equipment : Exporter uniquement equipment_slots}';

    protected $description = 'Exporte characteristics, formules, spell_effect_types et equipment_slots vers database/seeders/data/';

    public function __construct(
        private readonly CharacteristicGetterService $getter
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->guardDevelopmentOnly()) {
            return self::FAILURE;
        }

        $all = ! $this->option('characteristics') && ! $this->option('formulas')
            && ! $this->option('spell-effect-types') && ! $this->option('equipment');

        $dir = database_path('seeders/data');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filesToWrite = $this->getFilesToExportForCurrentRun($all);
        $existingToBackup = array_filter($filesToWrite, fn (string $f) => is_file($dir . '/' . $f));
        if ($existingToBackup !== []) {
            $this->createBackupZip($dir, $existingToBackup);
        }

        if ($all || $this->option('characteristics')) {
            $this->exportCharacteristics($dir);
        }
        if ($all || $this->option('formulas')) {
            $this->exportConversionFormulasInGroups($dir);
        }
        if ($all || $this->option('spell-effect-types')) {
            $this->exportSpellEffectTypes($dir);
        }
        if ($all || $this->option('equipment')) {
            $this->exportEquipment($dir);
        }

        $this->cleanupOldBackups();

        $this->info('Export terminé.');

        return self::SUCCESS;
    }

    /**
     * Fichiers qui seront écrits par cette exécution (noms de fichiers uniquement).
     *
     * @return list<string>
     */
    private function getFilesToExportForCurrentRun(bool $all): array
    {
        $files = [];
        if ($all || $this->option('characteristics')) {
            $files = array_merge($files, [
                'characteristics.php',
                'characteristic_creature.php',
                'characteristic_object.php',
                'characteristic_spell.php',
            ]);
        }
        if ($all || $this->option('formulas')) {
            // formules exportées avec --characteristics
        }
        if ($all || $this->option('spell-effect-types')) {
            $files[] = 'spell_effect_types.php';
        }
        if ($all || $this->option('equipment')) {
            $files[] = 'equipment_slots.php';
        }

        return array_values(array_unique($files));
    }

    /**
     * Crée une archive ZIP des fichiers existants dans data/ et la stocke dans storage/app/seeders-data-backups/.
     *
     * @param list<string> $basenames
     */
    private function createBackupZip(string $dataDir, array $basenames): void
    {
        $storageDir = storage_path('app/' . self::BACKUP_DIR);
        if (! File::isDirectory($storageDir)) {
            File::makeDirectory($storageDir, 0755, true);
        }

        $zipName = 'seeder-data-' . now()->format('Y-m-d_H-i-s') . '.zip';
        $zipPath = $storageDir . '/' . $zipName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->warn("Impossible de créer le backup : {$zipPath}");

            return;
        }

        foreach ($basenames as $basename) {
            $fullPath = $dataDir . '/' . $basename;
            if (is_file($fullPath)) {
                $zip->addFile($fullPath, $basename);
            }
        }

        $zip->close();
        $this->info('Backup créé : ' . $zipPath);
    }

    /**
     * Supprime les backups en trop : si plus de BACKUP_MAX_COUNT, supprime ceux plus vieux que BACKUP_MAX_AGE_DAYS.
     */
    private function cleanupOldBackups(): void
    {
        $storageDir = storage_path('app/' . self::BACKUP_DIR);
        if (! is_dir($storageDir)) {
            return;
        }

        $zips = File::glob($storageDir . '/seeder-data-*.zip');
        if ($zips === false || count($zips) <= self::BACKUP_MAX_COUNT) {
            return;
        }

        $cutoff = now()->subDays(self::BACKUP_MAX_AGE_DAYS)->timestamp;
        foreach ($zips as $path) {
            if (filemtime($path) < $cutoff && @unlink($path)) {
                $this->line('Backup supprimé (trop ancien) : ' . basename($path));
            }
        }
    }

    private function exportCharacteristics(string $dir): void
    {
        $this->getter->clearCache();

        if (Schema::hasTable('characteristics')) {
            $rows = Characteristic::query()->orderBy('sort_order')->orderBy('key')->get();
            $data = $rows->map(fn ($r) => [
                'key' => $r->key,
                'name' => $r->name,
                'short_name' => $r->short_name,
                'helper' => $r->helper,
                'descriptions' => $r->descriptions,
                'icon' => $r->icon,
                'color' => $r->color,
                'unit' => $r->unit,
                'type' => $r->type,
                'sort_order' => $r->sort_order,
            ])->all();
            $path = $dir . '/characteristics.php';
            $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * Table générale characteristics. Régénéré par : php artisan db:export-seeder-data --characteristics\n */\n\nreturn " . $this->varExportShort($data) . ";\n";
            file_put_contents($path, $content);
            $this->info('Exported ' . count($data) . ' characteristics → ' . $path);
        }

        if (Schema::hasTable('characteristic_creature')) {
            $rows = CharacteristicCreature::with('characteristic')->orderBy('characteristic_id')->orderBy('entity')->get();
            $data = $rows->map(fn ($r) => [
                'characteristic_key' => $r->characteristic->key,
                'entity' => $r->entity,
                'db_column' => $r->db_column,
                'min' => $r->min,
                'max' => $r->max,
                'formula' => $r->formula,
                'formula_display' => $r->formula_display,
                'default_value' => $r->default_value,
                'required' => $r->required,
                'validation_message' => $r->validation_message,
                'conversion_formula' => $r->conversion_formula,
                'sort_order' => $r->sort_order,
            ])->all();
            $path = $dir . '/characteristic_creature.php';
            $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * Groupe creature (monster, class, npc). Régénéré par : php artisan db:export-seeder-data --characteristics\n */\n\nreturn " . $this->varExportShort($data) . ";\n";
            file_put_contents($path, $content);
            $this->info('Exported ' . count($data) . ' characteristic_creature → ' . $path);
        }

        if (Schema::hasTable('characteristic_object')) {
            $rows = CharacteristicObject::with('characteristic')->orderBy('characteristic_id')->orderBy('entity')->get();
            $data = $rows->map(fn ($r) => [
                'characteristic_key' => $r->characteristic->key,
                'entity' => $r->entity,
                'db_column' => $r->db_column,
                'min' => $r->min,
                'max' => $r->max,
                'formula' => $r->formula,
                'formula_display' => $r->formula_display,
                'default_value' => $r->default_value,
                'required' => $r->required,
                'validation_message' => $r->validation_message,
                'conversion_formula' => $r->conversion_formula,
                'sort_order' => $r->sort_order,
                'forgemagie_allowed' => $r->forgemagie_allowed,
                'forgemagie_max' => $r->forgemagie_max,
                'base_price_per_unit' => $r->base_price_per_unit,
                'rune_price_per_unit' => $r->rune_price_per_unit,
            ])->all();
            $path = $dir . '/characteristic_object.php';
            $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * Groupe object : item, consumable, resource, panoply.\n * Régénéré par : php artisan db:export-seeder-data --characteristics\n */\n\nreturn " . $this->varExportShort($data) . ";\n";
            file_put_contents($path, $content);
            $this->info('Exported ' . count($data) . ' characteristic_object → ' . $path);
        }

        if (Schema::hasTable('characteristic_spell')) {
            $rows = CharacteristicSpell::with('characteristic')->orderBy('characteristic_id')->orderBy('entity')->get();
            $data = $rows->map(fn ($r) => [
                'characteristic_key' => $r->characteristic->key,
                'entity' => $r->entity,
                'db_column' => $r->db_column,
                'min' => $r->min,
                'max' => $r->max,
                'formula' => $r->formula,
                'formula_display' => $r->formula_display,
                'default_value' => $r->default_value,
                'required' => $r->required,
                'validation_message' => $r->validation_message,
                'conversion_formula' => $r->conversion_formula,
                'sort_order' => $r->sort_order,
            ])->all();
            $path = $dir . '/characteristic_spell.php';
            $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * Groupe spell. Régénéré par : php artisan db:export-seeder-data --characteristics\n */\n\nreturn " . $this->varExportShort($data) . ";\n";
            file_put_contents($path, $content);
            $this->info('Exported ' . count($data) . ' characteristic_spell → ' . $path);
        }
    }

    private function exportConversionFormulasInGroups(string $dir): void
    {
        $this->info('Les formules de conversion sont dans les tables de groupe (characteristic_*). Utilisez --characteristics pour tout exporter.');
    }

    private function exportSpellEffectTypes(string $dir): void
    {
        $rows = SpellEffectType::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn ($m) => [
                'slug' => $m->slug,
                'name' => $m->name,
                'category' => $m->category,
                'description' => $m->description,
                'value_type' => $m->value_type,
                'element' => $m->element,
                'unit' => $m->unit,
                'is_positive' => $m->is_positive,
                'sort_order' => $m->sort_order,
                'dofusdb_effect_id' => $m->dofusdb_effect_id,
            ])
            ->all();

        $path = $dir . '/spell_effect_types.php';
        $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * Types d'effets de sort (export BDD).\n * Généré par php artisan db:export-seeder-data\n */\n\nreturn " . $this->varExportShort($rows) . ";\n";
        file_put_contents($path, $content);
        $this->info('Exported ' . count($rows) . ' spell effect types → ' . $path);
    }

    private function exportEquipment(string $dir): void
    {
        $slots = EquipmentSlot::query()
            ->with('slotCharacteristics')
            ->orderBy('sort_order')
            ->get();

        $out = [];
        foreach ($slots as $slot) {
            $characteristics = [];
            foreach ($slot->slotCharacteristics as $sc) {
                $characteristics[$sc->characteristic_key] = [
                    'bracket_max' => $sc->bracket_max,
                    'forgemagie_max' => $sc->forgemagie_max,
                    'base_price_per_unit' => $sc->base_price_per_unit !== null ? (float) $sc->base_price_per_unit : null,
                    'rune_price_per_unit' => $sc->rune_price_per_unit !== null ? (float) $sc->rune_price_per_unit : null,
                ];
                if ($characteristics[$sc->characteristic_key]['base_price_per_unit'] === null) {
                    unset($characteristics[$sc->characteristic_key]['base_price_per_unit']);
                }
                if ($characteristics[$sc->characteristic_key]['rune_price_per_unit'] === null) {
                    unset($characteristics[$sc->characteristic_key]['rune_price_per_unit']);
                }
            }
            $out[$slot->id] = [
                'name' => $slot->name,
                'characteristics' => $characteristics,
            ];
        }

        $path = $dir . '/equipment_slots.php';
        $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * Slots d'équipement (export BDD).\n * Généré par php artisan db:export-seeder-data --equipment\n */\n\nreturn " . $this->varExportShort($out) . ";\n";
        file_put_contents($path, $content);
        $this->info('Exported ' . count($out) . ' equipment slots → ' . $path);
    }

    private function varExportShort(mixed $var): string
    {
        return var_export($var, true);
    }
}
