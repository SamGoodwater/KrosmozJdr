<?php

declare(strict_types=1);

namespace App\Console\Commands\Scrapping;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\GuardsProductionEnvironment;
use App\Models\Scrapping\ScrappingEntityMapping;
use App\Models\Scrapping\ScrappingEntityMappingTarget;
use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Models\Type\ResourceType;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristics\CharacteristicDefinitionsExportFromDatabaseService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

/**
 * Exporte les données de la BDD vers database/seeders/data/ pour que les seeders
 * utilisent ces fichiers comme source (au lieu de config/). Les caractéristiques
 * sont exportées en JSON (`characteristic-definitions/{groupe}/*.json`).
 *
 * À lancer après modification des caractéristiques / formules / types d'effets via l'interface.
 * Crée une sauvegarde ZIP des fichiers existants avant export, puis nettoie les backups > 7 ou > 7 jours.
 *
 * Disponible uniquement en environnement local et testing (désactivé en production pour limiter la surface d'attaque).
 */
class ScrappingSeedersExportCommand extends Command
{
    use GuardsProductionEnvironment;

    private const BACKUP_DIR = 'seeders-data-backups';

    private const BACKUP_MAX_COUNT = 7;

    private const BACKUP_MAX_AGE_DAYS = 7;

    protected $signature = 'scrapping:seeders:export
                            {--characteristics : Exporter uniquement characteristics}
                            {--formulas : Exporter les formules de conversion (tables characteristic_creature/object/spell)}
                            {--scrapping-mappings : Exporter les règles de mapping scrapping (DofusDB → Krosmoz)}
                            {--item-types : Exporter resource_types, consumable_types, item_types (types item scrapping)}';

    protected $description = 'Exporte définitions JSON caractéristiques, mapping scrapping et types item vers database/seeders/data/';

    protected $aliases = ['db:export-seeder-data'];

    public function __construct(
        private readonly CharacteristicGetterService $getter
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->guardDevelopmentOnly()) {
            return ArtisanExitCode::FAILURE;
        }

        $all = ! $this->option('characteristics') && ! $this->option('formulas')
            && ! $this->option('scrapping-mappings')
            && ! $this->option('item-types');

        $dir = database_path('seeders/data');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filesToWrite = $this->getFilesToExportForCurrentRun($all);
        $existingToBackup = array_filter($filesToWrite, fn (string $f) => is_file($dir.'/'.$f));
        if ($existingToBackup !== []) {
            $this->createBackupZip($dir, $existingToBackup);
        }

        if ($all || $this->option('characteristics')) {
            $this->exportCharacteristics($dir);
        }
        if ($all || $this->option('formulas')) {
            $this->exportConversionFormulasInGroups($dir);
        }
        if ($all || $this->option('scrapping-mappings')) {
            $this->exportScrappingMappings($dir);
        }
        if ($all || $this->option('item-types')) {
            $this->exportItemTypes($dir);
        }

        $this->cleanupOldBackups();

        $this->info('Export terminé.');

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * Fichiers qui seront écrits par cette exécution (noms de fichiers uniquement).
     *
     * @return list<string>
     */
    private function getFilesToExportForCurrentRun(bool $all): array
    {
        $dataDir = database_path('seeders/data');
        $files = [];
        if ($all || $this->option('characteristics')) {
            $defRoot = $dataDir.'/characteristic-definitions';
            if (is_dir($defRoot)) {
                foreach (File::allFiles($defRoot) as $fileInfo) {
                    $rel = str_replace($dataDir.DIRECTORY_SEPARATOR, '', $fileInfo->getPathname());
                    $files[] = str_replace(DIRECTORY_SEPARATOR, '/', $rel);
                }
            }
        }
        if ($all || $this->option('formulas')) {
            // formules exportées avec --characteristics
        }
        if ($all || $this->option('scrapping-mappings')) {
            $files[] = 'scrapping_entity_mappings.php';
        }
        if ($all || $this->option('item-types')) {
            $files[] = 'resource_types.php';
            $files[] = 'consumable_types.php';
            $files[] = 'item_types.php';
        }

        return array_values(array_unique($files));
    }

    /**
     * Crée une archive ZIP des fichiers existants dans data/ et la stocke dans storage/app/seeders-data-backups/.
     *
     * @param  list<string>  $basenames
     */
    private function createBackupZip(string $dataDir, array $basenames): void
    {
        $storageDir = storage_path('app/'.self::BACKUP_DIR);
        if (! File::isDirectory($storageDir)) {
            File::makeDirectory($storageDir, 0755, true);
        }

        $zipName = 'seeder-data-'.now()->format('Y-m-d_H-i-s').'.zip';
        $zipPath = $storageDir.'/'.$zipName;

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->warn("Impossible de créer le backup : {$zipPath}");

            return;
        }

        foreach ($basenames as $basename) {
            $fullPath = $dataDir.'/'.$basename;
            if (is_file($fullPath)) {
                $zip->addFile($fullPath, $basename);
            }
        }

        $zip->close();
        $this->info('Backup créé : '.$zipPath);
    }

    /**
     * Supprime les backups en trop : si plus de BACKUP_MAX_COUNT, supprime ceux plus vieux que BACKUP_MAX_AGE_DAYS.
     */
    private function cleanupOldBackups(): void
    {
        $storageDir = storage_path('app/'.self::BACKUP_DIR);
        if (! is_dir($storageDir)) {
            return;
        }

        $zips = File::glob($storageDir.'/seeder-data-*.zip');
        if ($zips === false || count($zips) <= self::BACKUP_MAX_COUNT) {
            return;
        }

        $cutoff = now()->subDays(self::BACKUP_MAX_AGE_DAYS)->timestamp;
        foreach ($zips as $path) {
            if (filemtime($path) < $cutoff && @unlink($path)) {
                $this->line('Backup supprimé (trop ancien) : '.basename($path));
            }
        }
    }

    private function exportCharacteristics(string $dir): void
    {
        $this->getter->clearCache();

        if (! Schema::hasTable('characteristics')) {
            $this->warn('Table characteristics absente : export des définitions JSON ignoré.');

            return;
        }

        $n = app(CharacteristicDefinitionsExportFromDatabaseService::class)->exportToDataDirectory();
        $this->info("Exported {$n} définitions JSON → {$dir}/characteristic-definitions/");
    }

    private function exportConversionFormulasInGroups(string $dir): void
    {
        $this->info('Les formules de conversion sont dans les tables de groupe (characteristic_*). Utilisez --characteristics pour tout exporter.');
    }

    private function exportItemTypes(string $dir): void
    {
        $this->exportItemTypesTable($dir, ResourceType::query()->whereNotNull('dofusdb_type_id')->orderBy('dofusdb_type_id')->get(), 'resource_types', 'resource_types', 'Ressources (superType 9). Régénéré par : php artisan scrapping:seeders:export --item-types');
        $this->exportItemTypesTable($dir, ConsumableType::query()->whereNotNull('dofusdb_type_id')->orderBy('dofusdb_type_id')->get(), 'consumable_types', 'consumable_types', 'Consommables (superTypes 6, 70). Régénéré par : php artisan scrapping:seeders:export --item-types');
        $this->exportItemTypesTable($dir, ItemType::query()->whereNotNull('dofusdb_type_id')->orderBy('dofusdb_type_id')->get(), 'item_types', 'item_types', 'Équipements. Régénéré par : php artisan scrapping:seeders:export --item-types');
    }

    /**
     * @param  Collection<int, covariant ResourceType|ConsumableType|ItemType>  $rows
     */
    private function exportItemTypesTable(string $dir, $rows, string $filename, string $label, string $comment): void
    {
        $data = $rows->map(fn ($r) => [
            'dofusdb_type_id' => $r->dofusdb_type_id,
            'name' => $r->name,
            'decision' => $r->decision,
            'state' => $r->state,
            'read_level' => $r->read_level,
            'write_level' => $r->write_level,
        ])->all();
        $path = $dir.'/'.$filename.'.php';
        $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * {$label} – {$comment}\n */\n\nreturn ".$this->varExportShort($data).";\n";
        file_put_contents($path, $content);
        $this->info('Exported '.count($data).' '.$label.' → '.$path);
    }

    private function exportScrappingMappings(string $dir): void
    {
        if (! Schema::hasTable('scrapping_entity_mappings')) {
            return;
        }

        $rows = ScrappingEntityMapping::with(['targets', 'characteristic'])
            ->orderBy('source')
            ->orderBy('entity')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $data = $rows->map(function (ScrappingEntityMapping $r) {
            $targets = $r->targets->map(fn (ScrappingEntityMappingTarget $t) => [
                'target_model' => $t->target_model,
                'target_field' => $t->target_field,
                'sort_order' => $t->sort_order,
            ])->values()->all();

            return [
                'source' => $r->source,
                'entity' => $r->entity,
                'mapping_key' => $r->mapping_key,
                'from_path' => $r->from_path,
                'from_lang_aware' => $r->from_lang_aware,
                'characteristic_key' => $r->characteristic?->key,
                'formatters' => $r->formatters,
                'spell_level_aggregation' => $r->spell_level_aggregation,
                'sort_order' => $r->sort_order,
                'targets' => $targets,
            ];
        })->all();

        $path = $dir.'/scrapping_entity_mappings.php';
        $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * Règles de mapping scrapping (DofusDB → Krosmoz). Régénéré par : php artisan scrapping:seeders:export --scrapping-mappings\n */\n\nreturn ".$this->varExportShort($data).";\n";
        file_put_contents($path, $content);
        $this->info('Exported '.count($data).' scrapping entity mappings → '.$path);
    }

    private function varExportShort(mixed $var, int $level = 0): string
    {
        if (! is_array($var)) {
            return match ($var) {
                null => 'null',
                true => 'true',
                false => 'false',
                default => var_export($var, true),
            };
        }
        if ($var === []) {
            return '[]';
        }

        $lines = ['['];
        $indent = str_repeat('    ', $level + 1);
        foreach ($var as $key => $value) {
            $exportedKey = is_int($key) ? (string) $key : var_export($key, true);
            $lines[] = $indent.$exportedKey.' => '.$this->varExportShort($value, $level + 1).',';
        }
        $lines[] = str_repeat('    ', $level).']';

        return implode("\n", $lines);
    }
}
