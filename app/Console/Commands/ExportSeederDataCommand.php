<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\EntityCharacteristic;
use App\Models\DofusdbConversionFormula;
use App\Models\EquipmentSlot;
use App\Models\SpellEffectType;
use App\Services\Characteristic\CharacteristicService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

/**
 * Exporte les données de la BDD vers database/seeders/data/ pour que les seeders
 * utilisent ces fichiers comme source (au lieu de config/).
 *
 * À lancer après modification des caractéristiques / formules / types d'effets via l'interface.
 */
class ExportSeederDataCommand extends Command
{
    protected $signature = 'db:export-seeder-data
                            {--characteristics : Exporter uniquement characteristics}
                            {--formulas : Exporter uniquement dofusdb_conversion_formulas}
                            {--spell-effect-types : Exporter uniquement spell_effect_types}
                            {--equipment : Exporter uniquement equipment_slots}';

    protected $description = 'Exporte characteristics, formules, spell_effect_types et equipment_slots vers database/seeders/data/';

    public function __construct(
        private readonly CharacteristicService $characteristicService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $all = ! $this->option('characteristics') && ! $this->option('formulas')
            && ! $this->option('spell-effect-types') && ! $this->option('equipment');

        $dir = database_path('seeders/data');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($all || $this->option('characteristics')) {
            $this->exportEntityCharacteristics($dir);
        }
        if ($all || $this->option('formulas')) {
            $this->exportDofusdbFormulas($dir);
        }
        if ($all || $this->option('spell-effect-types')) {
            $this->exportSpellEffectTypes($dir);
        }
        if ($all || $this->option('equipment')) {
            $this->exportEquipment($dir);
        }

        $this->info('Export terminé.');

        return self::SUCCESS;
    }

    private function exportEntityCharacteristics(string $dir): void
    {
        $this->characteristicService->clearCache();
        $rows = EntityCharacteristic::query()->orderBy('entity')->orderBy('sort_order')->orderBy('characteristic_key')->get();
        $data = $rows->map(fn ($r) => [
            'entity' => $r->entity,
            'characteristic_key' => $r->characteristic_key,
            'name' => $r->name,
            'short_name' => $r->short_name,
            'helper' => $r->helper,
            'descriptions' => $r->descriptions,
            'icon' => $r->icon,
            'color' => $r->color,
            'unit' => $r->unit,
            'sort_order' => $r->sort_order,
            'db_column' => $r->db_column,
            'type' => $r->type,
            'min' => $r->min,
            'max' => $r->max,
            'formula' => $r->formula,
            'formula_display' => $r->formula_display,
            'computation' => $r->computation,
            'default_value' => $r->default_value,
            'required' => $r->required,
            'validation_message' => $r->validation_message,
            'forgemagie_allowed' => $r->forgemagie_allowed,
            'forgemagie_max' => $r->forgemagie_max,
            'base_price_per_unit' => $r->base_price_per_unit !== null ? (float) $r->base_price_per_unit : null,
            'rune_price_per_unit' => $r->rune_price_per_unit !== null ? (float) $r->rune_price_per_unit : null,
            'applies_to' => $r->applies_to,
            'is_competence' => $r->is_competence,
            'characteristic_id' => $r->characteristic_id,
            'alternative_characteristic_id' => $r->alternative_characteristic_id,
            'skill_type' => $r->skill_type,
            'value_available' => $r->value_available,
            'labels' => $r->labels,
            'validation' => $r->validation,
            'mastery_value_available' => $r->mastery_value_available,
            'mastery_labels' => $r->mastery_labels,
        ])->all();

        $path = $dir . '/entity_characteristics.php';
        $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * Données entity_characteristics (export BDD).\n * Généré par php artisan db:export-seeder-data\n */\n\nreturn " . $this->varExportShort($data) . ";\n";
        file_put_contents($path, $content);
        $this->info('Exported ' . count($data) . ' entity_characteristics → ' . $path);
    }

    private function exportDofusdbFormulas(string $dir): void
    {
        $keyColumn = Schema::hasColumn('dofusdb_conversion_formulas', 'characteristic_key')
            ? 'characteristic_key'
            : 'characteristic_id';

        $rows = DofusdbConversionFormula::query()
            ->orderBy($keyColumn)
            ->orderBy('entity')
            ->get()
            ->map(function ($m) use ($keyColumn) {
                $key = $m->getAttribute($keyColumn);
                return [
                    'characteristic_key' => $key,
                    'entity' => $m->entity,
                    'formula_type' => $m->formula_type,
                    'parameters' => $m->parameters,
                    'formula_display' => $m->formula_display,
                    'conversion_formula' => $m->conversion_formula ?? null,
                    'handler_name' => $m->handler_name ?? null,
                ];
            })
            ->all();

        $path = $dir . '/dofusdb_conversion_formulas.php';
        $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * Formules de conversion DofusDB → Krosmoz (export BDD).\n * Généré par php artisan db:export-seeder-data\n */\n\nreturn " . $this->varExportShort($rows) . ";\n";
        file_put_contents($path, $content);
        $this->info('Exported ' . count($rows) . ' formulas → ' . $path);
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
