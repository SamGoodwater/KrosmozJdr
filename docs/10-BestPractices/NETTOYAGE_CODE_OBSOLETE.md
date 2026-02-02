# Code obsolète et éléments conservés

Référence des **éléments supprimés** (ou dépréciés) et de ceux **conservés volontairement** pour rétrocompatibilité ou usage actif.

---

## Éléments supprimés

| Élément | Remplacement / raison |
|--------|------------------------|
| Page admin `/admin/dofus-conversion-formulas` | Édition des formules dans **Admin > Caractéristiques** (section « Formules de conversion Dofus → JDR » par caractéristique). |
| `DofusConversionFormulaController::index()` et `defaultFormulaDisplay()` | Contrôleur réduit à `formulaPreview()` pour l’API d’aperçu graphique. |
| `DataConversionService::getRequiredFieldsForEntity()` | Code mort ; validation des champs requis par `ValidationService` (V2) via `validateConvertedData()`. |

**Route conservée** : `GET /admin/dofus-conversion-formulas/formula-preview` (utilisée par la page Caractéristiques pour l’aperçu graphique).

---

## Éléments conservés (rétrocompatibilité ou usage actif)

| Élément | Raison |
|--------|--------|
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
