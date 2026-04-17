# Surcharges visuelles par valeur

## Principe

Chaque caractéristique possède une icône et une couleur par défaut (colonnes `icon`, `color` de la table `characteristics`). Certaines caractéristiques nécessitent que ces propriétés visuelles changent en fonction de la **valeur** courante. Par exemple :

- **Portée (range_spell)** : valeur `1` = icône CàC (corps à corps) + couleur rouge ; valeur `0` = le sort s'applique sur soi-même.
- **Ligne de vue (sight_line_spell)** : `true` = icône `sightLine.webp` ; `false` = icône `noSightLine.webp`.
- **Sort magique/physique (is_magic_spell)** : `true` = magique ; `false` = physique avec icône et couleur distinctes.

Le système de **surcharges visuelles par valeur** (`value_overrides`) résout ce besoin de manière générique, sans logique hardcodée dans le JavaScript.

## Stockage BDD

Colonne JSON `value_overrides` sur la table `characteristics` (migration `2026_04_11_100000`).

Chaque entrée du tableau JSON contient :

| Clé        | Type     | Requis | Description |
|------------|----------|--------|-------------|
| `value`    | mixed    | oui    | Valeur déclencheuse (`true`, `false`, `0`, `1`, `"fire"`, etc.) |
| `icon`     | string   | non    | Icône de remplacement (nom de fichier dans `caracteristics/` ou chemin complet) |
| `color`    | string   | non    | Couleur hex de remplacement (`#e53935`) |
| `subtitle` | string   | non    | Texte contextuel décrivant cette valeur spécifique |

### Exemple

```json
[
  {
    "value": 1,
    "icon": "cac.webp",
    "color": "#e53935",
    "subtitle": "Corps à corps — portée de 1 case."
  },
  {
    "value": 0,
    "subtitle": "Le sort s'applique sur le lanceur lui-même."
  }
]
```

## Différence avec description / helper

- **`descriptions`** : texte généraliste, identique quelle que soit la valeur de la caractéristique.
- **`helper`** : aide contextuelle courte, toujours la même.
- **`subtitle`** (dans `value_overrides`) : texte qui change selon la valeur courante. Affiché en priorité dans les tooltips, en italique.

## Flux de données

```
BDD (characteristics.value_overrides)
  → CharacteristicMetaByDbColumnService (normalise les icônes, cache 5min)
  → Inertia share (props.characteristics)
  → useCharacteristicsStore (getByDbColumn, etc.)
  → resolveDef() → _resolvedIcon, _resolvedColor, _resolvedSubtitle
  → useCharacteristicViewModel → subtitle dans le view model
  → Composants (CharacteristicPropertyTooltip, CharacteristicBoolean, SpellUsageTooltipPanel)
```

## Résolution frontend (`resolveValueOverride`)

Fonction exportée depuis `useCharacteristicDisplay.js`. Algorithme en deux passes :

1. **Passe stricte** : égalité `===` entre `entry.value` et la valeur courante.
2. **Passe souple** : correspondances croisées `true`/`1`/`"1"` et `false`/`0`/`"0"`, puis cast `String()`.

Cela permet de stocker `value: true` en JSON même si le backend envoie `1`.

## Priorité de résolution

```
value_overrides  >  icon_false  >  value_available  >  défaut
```

Les `value_overrides` sont vérifiées en premier dans `resolveDef()`. Si une entrée matche et définit `icon` ou `color`, elle prend la priorité. Les fallbacks `icon_false` et `value_available` ne sont appliqués que pour les propriétés non couvertes par l'override (couleur « si faux » pour un booléen : entrée `value: false` dans `value_overrides`).

## Administration

Dans la page admin des caractéristiques (`Admin/characteristics/Index.vue`), une section **« Surcharges visuelles par valeur »** permet d'ajouter, modifier et supprimer des entrées. Chaque entrée expose les champs : Valeur, Icône, Couleur, Sous-texte.

## Données pré-remplies (seeder)

Les valeurs par défaut sont définies dans `database/seeders/data/characteristic_icons_colors.php` (clé `value_overrides`, fusionnée avec les paliers maîtrise créature via `array_merge`) et appliquées par `CharacteristicSeeder` lorsque la ligne dans `characteristics.php` ne définit pas déjà un `value_overrides` explicite.

### Sorts (`group` spell)

- **Portée** : `range_spell`, `spell_range_min_spell`, `spell_range_max_spell` (valeurs `0` / `1`, CàC vs auto-cible).
- **Booléens** : `range_editable_spell`, `sight_line_spell`, `is_magic_spell`, `ritual_available_spell`, `allows_reaction_spell`.
- **Catégorie** : `category_spell` (entiers `0`–`3`).
- **Type stratégique** : `spell_type_spell` (chaînes slug : `degats`, `soin`, `protection`, etc.) — icônes SVG sous `icons/spell_type/`.
- **Élément(s)** : `element_spell` (masque de bits + combinaisons courantes documentées dans le fichier).

### Objet (`group` object)

- **Rareté** : `rarity_object` (`0`–`5`).
- **Résistance en % par palier** : `resistance_percent_tier_earth_object`, `resistance_percent_tier_fire_object`, `resistance_percent_tier_water_object`, `resistance_percent_tier_air_object`, `resistance_percent_tier_neutral_object` (`0`–`2`).
- **Seuils d20** : `critical_hit_object`, `failure_hit_object` (`0`–`3`).

### Créature (`group` creature)

- **Critique** : `critical_hit_creature` (`0`–`3`, aligné sur l’équipement).
- **Fiche monstre** : `hostility_creature` (`0`–`4`, aligné sur `Monster::HOSTILITY`), `monster_size` (`0`–`5`, aligné sur `Monster::SIZE`), `monster_is_boss` (`true` / `false`, sous-textes uniquement).
- **Palier maîtrise (0–2)** : les 18 clés `*_mastery_creature` listées dans `$masteryCreaturePalierKeys` (même tableau de surcharges `Non formé` / `Maîtrise` / `Expertise` pour toutes).

### Hors périmètre des surcharges seedées

- Valeurs ouvertes ou référentiels extensifs (ex. `monster_race`) : pas de tableau d’overrides par valeur dans le seeder ; l’affichage repose sur le libellé métier (race) et les métadonnées par défaut de la caractéristique.
