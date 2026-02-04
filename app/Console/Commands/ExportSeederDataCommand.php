<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use App\Models\EquipmentSlot;
use App\Models\SpellEffectType;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
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
        $all = ! $this->option('characteristics') && ! $this->option('formulas')
            && ! $this->option('spell-effect-types') && ! $this->option('equipment');

        $dir = database_path('seeders/data');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
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

        $this->info('Export terminé.');

        return self::SUCCESS;
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
            $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * Table characteristics (export BDD).\n * Généré par php artisan db:export-seeder-data --characteristics\n */\n\nreturn " . $this->varExportShort($data) . ";\n";
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
            $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * characteristic_creature (export BDD).\n */\n\nreturn " . $this->varExportShort($data) . ";\n";
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
            ])->all();
            $path = $dir . '/characteristic_object.php';
            $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * characteristic_object (export BDD).\n */\n\nreturn " . $this->varExportShort($data) . ";\n";
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
            $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * characteristic_spell (export BDD).\n */\n\nreturn " . $this->varExportShort($data) . ";\n";
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
