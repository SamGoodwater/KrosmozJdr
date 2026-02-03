# Données par défaut des seeders

Ces fichiers PHP sont la **source des données** pour les seeders (caractéristiques, conversion DofusDB, équipement, effets de sort). Toute la config caractéristiques/conversion/équipement est gérée en BDD ; ces fichiers servent au setup initial et à la reproductibilité.

## Fichiers

| Fichier | Seeder | Description |
|---------|--------|-------------|
| `entity_characteristics.php` | `EntityCharacteristicSeeder` | Définitions des caractéristiques par entité (entity + characteristic_key, min/max, formules, conversion, etc.) |
| `dofusdb_conversion_formulas.php` | `DofusdbConversionFormulaSeeder` | Formules de conversion DofusDB → KrosmozJDR |
| `dofusdb_conversion_config.php` | `DofusdbConversionConfigSeeder` | Config conversion (pass_through, element_id_to_resistance, limits_source, etc.) |
| `equipment_slots.php` | `EquipmentCharacteristicConfigSeeder` | Slots d'équipement et caractéristiques par slot (bracket_max, forgemagie_max) |
| `spell_effect_types.php` | `SpellEffectTypeSeeder` | Types d'effets de sort (référentiel) |

## Workflow

1. **Modification via l'interface** : vous éditez les caractéristiques / formules / types d'effets dans l'admin.
2. **Export BDD → fichiers** : après modification, régénérer les fichiers pour qu'ils reflètent la BDD :
   ```bash
   php artisan db:export-seeder-data
   ```
   Options : `--characteristics`, `--formulas`, `--spell-effect-types`, `--equipment` pour n'exporter qu'une partie.
3. **Setup projet** : sur une nouvelle install, `php artisan db:seed` (ou les seeders concernés) lit ces fichiers et remplit les tables.

Ainsi les données par défaut du projet restent versionnées et reproductibles.
