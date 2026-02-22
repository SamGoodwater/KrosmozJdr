# Données par défaut des seeders

Ces fichiers PHP sont la **source des données** pour les seeders (caractéristiques, types d'effets de sort). La configuration des caractéristiques est gérée en BDD ; ces fichiers servent au setup initial et à la reproductibilité.

## Fichiers caractéristiques (nouvelle structure)

| Fichier | Seeder | Description |
|---------|--------|-------------|
| `characteristics.php` | `CharacteristicSeeder` | Table générale : une ligne par caractéristique (key, name, type, unit, sort_order, etc.) |
| `characteristic_creature.php` | `CreatureCharacteristicSeeder` | Groupe creature (monster, class, npc) : limites, formules, conversion_formula, conversion_function par entity |
| `characteristic_object.php` | `ObjectCharacteristicSeeder` | Groupe object : idem + forgemagie, base_price_per_unit, rune_price_per_unit, value_available, item_type_ids (pivot characteristic_object_item_type) |
| `characteristic_spell.php` | `SpellCharacteristicSeeder` | Groupe spell : limites, formules, conversion_formula, conversion_function, value_available |

Les fichiers de groupe (creature, object, spell) peuvent contenir des clés non persistées (ex. `required`, `validation_message`, `sort_order`) : elles sont ignorées à l’import. L’export n’écrit que les colonnes présentes en BDD.

## Autres fichiers

| Fichier | Seeder | Description |
|---------|--------|-------------|
| `spell_effect_types.php` | `SpellEffectTypeSeeder` | Types d'effets de sort (référentiel) |

## Workflow (dev)

1. **Setup initial** : `php artisan db:seed` (ou les seeders concernés) lit ces fichiers et remplit les tables.
2. **Paramétrage via l'interface** : éditez les caractéristiques dans l'admin (`/admin/characteristics`) — formules, bornes min/max, spécificités par entité (monstre, classe, PNJ…).
3. **Export BDD → seeders** : pour récupérer ce qui est en base et l'écrire dans ces fichiers (afin d’initialiser le projet avec des caractéristiques déjà paramétrées) :
   ```bash
   php artisan db:export-seeder-data --characteristics
   ```
   Cela écrase `characteristics.php`, `characteristic_creature.php`, `characteristic_object.php` et `characteristic_spell.php` avec le contenu actuel des tables. Vous pouvez ensuite committer ces fichiers pour versionner votre configuration.

**Autres options** : `--spell-effect-types`. Sans option, toutes les exportations sont lancées.

Ainsi les données par défaut du projet restent versionnées et reproductibles.

## Exhaustivité des données (audit)

- **characteristics.php** : 106 entrées. Toutes ont `name`, `short_name`, `helper` renseignés. Les champs `descriptions`, `icon`, `color` sont souvent NULL (à compléter via l’admin ou l’export si besoin).
- **characteristic_icons_colors.php** : mapping optionnel `icons`, `colors`, `descriptions` par clé ; utilisé par CharacteristicSeeder quand la valeur est NULL. Icônes dans `storage/app/public/images/icons/characteristics/` (copiées depuis Icones).
- **Formules de conversion** : toutes les lignes des fichiers de groupe ont une `conversion_formula` (plus de NULL). Par défaut : `[d]` (pass-through) ou `min(1,max(0,round([d])))` pour les champs 0/1 (sorts). Les formules spécifiques (niveau, vie, rareté, dégâts, etc.) sont conservées.
- **conversion_function** : pris en charge dans `CharacteristicGroupSeeder::commonAttributes` ; peut être renseigné dans les fichiers de données pour une fonction personnalisée (registry).
