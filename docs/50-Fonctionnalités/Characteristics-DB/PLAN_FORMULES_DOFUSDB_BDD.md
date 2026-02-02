# Formules de conversion DofusDB → KrosmozJDR en base de données

**Objectif** : Stocker les formules de conversion (DofusDB → KrosmozJDR) en base de données, par caractéristique et par entité (monster, class, item), pour les piloter depuis l’admin et permettre des formules différentes selon qu’on récupère un équipement, un monstre ou une classe.

**Références** : `config/dofusdb_conversion.php` (section `formulas`), `App\Services\Scrapping\V2\Conversion\DofusDbConversionFormulas`.

---

## 1. Périmètre

| Actuel | Cible |
|--------|--------|
| Formules dans `config/dofusdb_conversion.formulas` (level, life, attributes, initiative) | Table `dofusdb_conversion_formulas` : une ligne par (characteristic_id, entity) avec type de formule et paramètres JSON |
| Code PHP fixe dans `DofusDbConversionFormulas` | Service lit la BDD (avec cache) ; le code applique le type (linear, linear_with_level, sqrt_attribute, ratio_initiative, passthrough) avec les paramètres stockés |

Les **mappings** (effectId → champ, elementId → res_*) restent en config ; seules les **formules de calcul** (paramètres par caractéristique/entité) passent en BDD.

---

## 2. Schéma

### Table `dofusdb_conversion_formulas`

| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| `id` | bigint (PK, auto) | non | |
| `characteristic_id` | string (FK) | non | Référence `characteristics.id` |
| `entity` | string | non | monster \| class \| item |
| `formula_type` | string | non | linear, linear_with_level, sqrt_attribute, ratio_initiative, passthrough |
| `parameters` | json | oui | Paramètres selon le type (divisor, level_factor, base, coeff, offset, denom, factor, clamp_ratio_min_zero, min_zero, etc.) |
| `formula_display` | string (text) | oui | Formule lisible pour l’admin (ex. « k = d / 10 ») |
| `created_at` | timestamp | oui | |
| `updated_at` | timestamp | oui | |

Contrainte unique : `(characteristic_id, entity)`.

**Types de formule et paramètres :**

| formula_type | Description | Exemple parameters |
|--------------|-------------|--------------------|
| **passthrough** | Valeur DofusDB recopiée telle quelle (eventuellement clampée) | `{}` |
| **linear** | k = round(d / divisor) | `{ "divisor": 10 }` |
| **linear_with_level** | k = round(d / divisor + level × level_factor) ; `level` = niveau Krosmoz déjà converti | `{ "divisor": 200, "level_factor": 5 }` |
| **sqrt_attribute** | k = round(base + coeff × sqrt(max(0, (d - offset) / denom))) | `{ "base": 6, "coeff": 24, "offset": 50, "denom": 1150 }` |
| **ratio_initiative** | ratio = (d - offset) / denom ; optionnel : clamp [0,1] ; k = round(factor × ratio) ; optionnel : min_zero | `{ "offset": 300, "denom": 4700, "factor": 10, "clamp_ratio_min_zero": true, "min_zero": true }` |

---

## 3. Service et cache

- **DofusDbConversionFormulaService** (nouveau) :
  - `getFormula(string $characteristicId, string $entity): ?array` → `['formula_type' => '...', 'parameters' => [...]]` ou null
  - `getFullFormulasConfig(): array` → structure proche de `config('dofusdb_conversion.formulas')` pour compatibilité
  - Cache Laravel (clé dédiée, TTL 3600), invalidation à chaque création/update/suppression (observer sur le modèle)

- **DofusDbConversionFormulas** (existant) :
  - Injecter `DofusDbConversionFormulaService`
  - Pour chaque conversion (level, life, attribute, initiative) : lire la formule depuis le service pour (characteristic_id, entity) ; si présente, appliquer selon `formula_type` + `parameters` ; sinon fallback sur `config('dofusdb_conversion.formulas')`

---

## 4. Ordre d’implémentation

1. Migration `dofusdb_conversion_formulas`
2. Modèle Eloquent `DofusdbConversionFormula` + relation vers `Characteristic`
3. `DofusDbConversionFormulaService` + cache + observer pour invalidation
4. Seeder : import depuis `config/dofusdb_conversion.formulas` (level, life, strength/intelligence/chance/agility, initiative pour monster et class ; level pour item). **Prérequis** : exécuter `CharacteristicConfigSeeder` avant (les characteristic_id doivent exister dans `characteristics`).
5. Adapter `DofusDbConversionFormulas` pour utiliser le service (avec fallback config)
6. Phase 7 (interface admin) : ajouter la gestion des formules DofusDB (CRUD par caractéristique/entité) dans l’admin super_admin
