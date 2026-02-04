# Données par défaut des seeders

Ces fichiers PHP sont la **source des données** pour les seeders (caractéristiques, équipement, types d'effets de sort). La configuration des caractéristiques est gérée en BDD ; ces fichiers servent au setup initial et à la reproductibilité.

## Fichiers caractéristiques (nouvelle structure)

| Fichier | Seeder | Description |
|---------|--------|-------------|
| `characteristics.php` | `CharacteristicSeeder` | Table générale : une ligne par caractéristique (key, name, type, unit, sort_order, etc.) |
| `characteristic_creature.php` | `CreatureCharacteristicSeeder` | Groupe creature (monster, class, npc) : limites, formules, conversion_formula par entity |
| `characteristic_object.php` | `ObjectCharacteristicSeeder` | Groupe object (item, consumable, resource, panoply) : idem + forgemagie, base_price_per_unit, rune_price_per_unit |
| `characteristic_spell.php` | `SpellCharacteristicSeeder` | Groupe spell : limites, formules, conversion_formula |

## Autres fichiers

| Fichier | Seeder | Description |
|---------|--------|-------------|
| `equipment_slots.php` | `EquipmentCharacteristicConfigSeeder` | Slots d'équipement et caractéristiques par slot (bracket_max, forgemagie_max) |
| `spell_effect_types.php` | `SpellEffectTypeSeeder` | Types d'effets de sort (référentiel) |

## Workflow

1. **Setup initial** : `php artisan db:seed` (ou les seeders concernés) lit ces fichiers et remplit les tables.
2. **Modification via l'interface** : éditez les caractéristiques dans l'admin (`/admin/characteristics`).
3. **Export BDD → fichiers** : après modification en BDD, régénérer les fichiers pour que l'init du projet reflète votre configuration :
   ```bash
   php artisan db:export-seeder-data --characteristics
   ```
   Options : `--characteristics`, `--formulas` (inclus dans --characteristics), `--spell-effect-types`, `--equipment`.

Ainsi les données par défaut du projet restent versionnées et reproductibles.
