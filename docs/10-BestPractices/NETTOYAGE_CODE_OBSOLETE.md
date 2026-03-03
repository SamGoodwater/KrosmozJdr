# Code obsolète et éléments conservés

Référence des **éléments supprimés** (ou dépréciés) et de ceux **conservés volontairement** pour rétrocompatibilité ou usage actif.

---

## Éléments supprimés

| Élément | Remplacement / raison |
|--------|------------------------|
| Configs scrapping `item-type.json`, `item-super-type.json`, `monster-race.json` (DofusDB entities) | Types et races gérés en BDD ; catalogues exposés par `DofusDbItemTypesCatalogService` et `DofusDbMonsterRacesCatalogService` (URLs en dur). Ces configs catalog-only n’étaient utilisées que pour lister les entités dans l’API config. |
| Page admin `/admin/dofus-conversion-formulas` | Édition des formules dans **Admin > Caractéristiques** (section « Formules de conversion Dofus → JDR » par caractéristique). |
| `DofusConversionFormulaController::index()` et `defaultFormulaDisplay()` | Contrôleur réduit à `formulaPreview()` pour l’API d’aperçu graphique. |
| `DataConversionService::getRequiredFieldsForEntity()` | Code mort ; validation des champs requis par `ValidationService` (V2) via `validateConvertedData()`. |
| Menu et admin « Types d'effets de sort » (`admin.spell-effect-types`), `SpellEffectTypeController`, page `Admin/spell-effect-types/Index.vue`, `SpellEffectsManager.vue`, route `PATCH entities.spells.updateEffects` | Ancien système d'effets de sort (référentiel `spell_effect_types` + pivot `spell_effects`). Remplacé par le **système unifié** (Sous-effets, Effets, `effect_usage`) : voir `EffectUsagesManager` sur la fiche Sort/Item/Consumable et `docs/50-Fonctionnalités/Spell-Effects/PLAN_MISE_EN_OEUVRE_EFFECTS.md`. |

**Routes conservées** : `GET /admin/dofus-conversion-formulas/formula-preview` (utilisée par la page Caractéristiques pour l’aperçu graphique).

---

## Éléments conservés (rétrocompatibilité ou usage actif)

| Élément | Raison |
|--------|--------|
| Tables `type_spell_effect_types`, `spell_effects` et modèles `SpellEffectType`, `SpellEffect` | Données potentiellement existantes ; `SpellEffectTypeSeeder` et option `--spell-effect-types` de `scrapping:seeders:export` conservés. Plus d'UI d'édition : utiliser le système unifié (effect / sub_effect / effect_usage). |
| Table `dofusdb_conversion_formulas` : colonnes `formula_type` et `parameters` | Seeder et enregistrements existants. Fallback lorsque `conversion_formula` est vide (puis `config/dofusdb_conversion.php`). |
| Config `config/dofusdb_conversion.php` | Fallback pour formules level, life, attributes, initiative, limites, mappings. La BDD prime si une formule est renseignée en base. |
| Service `DataConversionService` | Pipeline de conversion du scrapping ; délègue à `DofusDbConversionFormulas` et `ValidationService` (V2). |
| Dossier `storage/debugbar/` | Exclu par `.gitignore` ; peut être vidé en local si besoin. |

---

## Évolutions possibles

- **Migration `conversion_formula`** : Remplir `conversion_formula` depuis `formula_type`/`parameters` pour toutes les lignes, puis déprécier ces colonnes.
- **Documentation Scrapping** : `docs/50-Fonctionnalités/Scrapping/` — s’assurer que l’édition des formules dans Caractéristiques y est décrite.

---

## Références

- Admin caractéristiques : `resources/js/Pages/Admin/characteristics/Index.vue`
- API aperçu : `GET admin/dofus-conversion-formulas/formula-preview` (query : `characteristic_id`, `entity`, optionnel `conversion_formula`)
- Services : `App\Services\Scrapping\V2\Conversion\DofusDbConversionFormulas`, `DofusDbConversionFormulaService`
