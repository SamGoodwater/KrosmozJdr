# Plan d’implémentation — Phase 3 : conversion des valeurs d’effet (sorts)

**Objectif** : brancher la conversion des valeurs des sous-effets (dommages, soins, bonus, retraits, etc.) sur les **caractéristiques du groupe spell** : formules et limites en BDD (`characteristic_spell`, `conversion_formula`), évaluées selon l’action (frapper, soigner, booster, etc.) et la caractéristique cible.

**Références** : [PLAN_IMPLEMENTATION_MAPPING_EFFETS.md](./PLAN_IMPLEMENTATION_MAPPING_EFFETS.md), [CARACTERISTIQUES_EFFETS_PAR_ACTION.md](./CARACTERISTIQUES_EFFETS_PAR_ACTION.md), [DOFUSDB_EFFECTS_CONVERSION.md](./DOFUSDB_EFFECTS_CONVERSION.md).

---

## État d’implémentation (2026-02-28)

Les étapes 3.1 à 3.7 ont été implémentées :

- **3.1** : `SpellEffectConversionFormulaResolver` — résolution action → characteristic_key (power_spell pour frapper/soigner/voler-vie/protéger ; alias pa → action_points_spell, po → range_spell ; _spell pour booster/retirer/voler-caracteristiques).
- **3.2** : Calcul de `d` dans `SpellEffectsConversionService::computeDofusValueForConversion()` (moyenne dés ou valeur fixe).
- **3.3** : Intégration dans `buildParams()` : injection du resolver et de `DofusConversionService`, appel à `applyValueConversion()` qui remplit `params.value_converted`.
- **3.4 / 3.5** : Utilisation de `power_spell` et des clés existantes en `characteristic_spell` ; doc mise à jour dans CARACTERISTIQUES_EFFETS_PAR_ACTION.md (§ 2.5).
- **3.6** : Tests unitaires (SpellEffectConversionFormulaResolverTest, SpellEffectsConversionServiceValueConvertedTest).
- **3.7** : Affichage de `value_converted` dans la prévisualisation (SearchPreviewSection.vue).

Le contrôleur de prévisualisation a été sécurisé pour ne pas appeler `getExistingAttributesForComparison` lorsque `converted` est null.

---

## 1. Contexte et état actuel

- **Conversion actuelle** : `SpellEffectsConversionService::buildValueFormula()` et `buildParams()` produisent une chaîne brute (`13d18`, `42`) et la stockent dans `params.value_formula` (et `params.characteristic` si élément). Aucun appel à `DofusConversionService` ni à `CharacteristicGetterService`.
- **Existant côté caractéristiques** : `DofusConversionService::convert($characteristicKey, $variables, $entityType)` utilise `conversion_formula` et limites depuis la BDD (groupe spell via `CharacteristicGetterService`). Variable d’entrée standard : `d` (valeur Dofus à convertir).
- **Règles par action** : [CARACTERISTIQUES_EFFETS_PAR_ACTION.md](./CARACTERISTIQUES_EFFETS_PAR_ACTION.md) définit :
  - **Une règle par action** : frapper, soigner, voler-vie, protéger → une seule clé de caractéristique (ex. dommages_spell, soin_spell) ou une par élément selon config.
  - **Une règle par caractéristique** : booster, retirer, voler-caracteristiques → formule selon `params.characteristic` (pa_spell, pm_spell, strong, etc.).
  - **Aucune conversion** : déplacer, invoquer → pas d’appel conversion.

---

## 2. Principes de conception

1. **Valeur d’entrée `d`** : pour les dés (diceNum, diceSide), on utilise la **moyenne** : `d = diceNum * (diceSide + 1) / 2`. Pour une valeur fixe, `d = value`. Cela reste compatible avec les formules BDD qui utilisent `[d]`.
2. **Conserver la traceabilité** : garder `value_formula` (brut Dofus, ex. `13d18`) dans `params`. Ajouter dans `params` une clé **`value_converted`** (int) lorsque la conversion est appliquée (optionnellement `value_formula_converted` en chaîne si on veut stocker une formule Krosmoz).
3. **Entité** : toutes les conversions d’effets de sort utilisent l’entité **`spell`** pour résoudre les formules et limites (`characteristic_spell`).
4. **Fallback** : si aucune formule n’est définie pour la caractéristique cible, ne pas casser le flux : garder uniquement `value_formula` (pas de `value_converted`), ou utiliser une valeur par défaut documentée (ex. moyenne des dés arrondie).

---

## 3. Étapes d’implémentation

### Étape 3.1 — Service de résolution « action → characteristic_key » (spell)

**Objectif** : pour un sous-effet donné (slug + params.characteristic éventuel), déterminer la **characteristic_key** à utiliser pour récupérer la formule de conversion (groupe spell).

- **Créer** un service dédié (ex. `App\Services\Scrapping\Core\Conversion\SpellEffects\SpellEffectConversionFormulaResolver`) ou étendre un service existant.
- **Règles** :
  - **frapper** : clé fixe ou par élément (ex. `dommages_spell` ou `do_feu_spell`, `do_terre_spell`, … selon `params.characteristic`). Décision produit : une seule clé « dommages » vs une par élément à documenter.
  - **soigner** : une clé (ex. `soin_spell` ou équivalent dans characteristic_spell).
  - **voler-vie** : comme dommages/soin (ex. `vol_pv_spell` ou réutilisation soin/dommages).
  - **protéger** : une clé (ex. `bouclier_spell` ou `absorption_spell`).
  - **booster**, **retirer**, **voler-caracteristiques** : clé = `params.characteristic` (déjà rempli par la conversion), avec suffixe `_spell` si la BDD utilise ce suffixe pour le groupe spell (à vérifier dans `CharacteristicGetterService` / seeders).
  - **déplacer**, **invoquer**, **autre** : retourner `null` (pas de conversion).
- **Entrées** : `sub_effect_slug`, `params` (dont `characteristic`).
- **Sortie** : `?string` (characteristic_key pour le groupe spell) ou tableau [ key, variables supplémentaires ] si besoin (ex. level).

**Fichiers** : nouveau fichier `SpellEffectConversionFormulaResolver.php` (ou équivalent), éventuellement config/table « action → characteristic_key » si on veut rendre cela éditable plus tard.

---

### Étape 3.2 — Calcul de la valeur Dofus `d` (dés vs fixe)

**Objectif** : à partir d’une instance d’effet DofusDB (diceNum, diceSide, value), produire une valeur numérique `d` pour la formule.

- **Emplacement** : dans `SpellEffectsConversionService` ou dans le nouveau resolver.
- **Règles** :
  - Si `diceNum` et `diceSide` présents et > 0 : `d = diceNum * (diceSide + 1) / 2` (moyenne).
  - Sinon si `value` numérique : `d = value`.
  - Sinon : pas de conversion (retourner null ou ne pas appeler le service).
- **Sortie** : `?float` (valeur `d` pour `DofusConversionService::convert()`).

**Fichiers** : `SpellEffectsConversionService.php` (méthode privée `computeDofusValueForConversion(array $instance): ?float`) ou méthode dans le resolver.

---

### Étape 3.3 — Intégration dans la chaîne de conversion (buildParams / valeur convertie)

**Objectif** : dans `SpellEffectsConversionService::buildParams()` (et éventuellement `buildParamsForOther()` pour « autre » si une règle existe), après avoir construit `value_formula` et `characteristic` :

1. Résoudre la **characteristic_key** (étape 3.1) pour ce sous-effet.
2. Calculer **d** (étape 3.2) à partir de l’instance.
3. Si characteristic_key et d sont définis : appeler **DofusConversionService::convert($characteristicKey, ['d' => d], 'spell', $fallback, $context)**.
4. Clamper avec **CharacteristicLimitService** (déjà inclus dans `convert()`).
5. Mettre le résultat dans **params** : ex. `params.value_converted = (int) $result`. Garder `value_formula` inchangé (traceabilité).

- **Contexte** : passer au minimum `raw` (et éventuellement `convertedOutput` partiel) si des `conversion_function` en BDD en ont besoin pour les effets de sort.
- **Fallback** : si pas de formule en BDD, `$fallback = (float) round($d)` (ou moyenne dés) pour que `convert()` puisse retourner une valeur même sans formule.

**Fichiers** : `SpellEffectsConversionService.php` (injection de `SpellEffectConversionFormulaResolver`, `DofusConversionService`), modification de `buildParams()` et éventuellement `buildParamsForOther()`.

---

### Étape 3.4 — Caractéristiques BDD (groupe spell) pour les actions « une règle »

**Objectif** : s’assurer que les caractéristiques utilisées pour frapper, soigner, voler-vie, protéger existent bien dans le groupe **spell** et ont une `conversion_formula` (et limites) cohérentes.

- Vérifier dans **characteristic_spell** (ou seeders `characteristic_spell.php`) la présence de clés telles que :
  - `dommages_spell` (ou une par élément : `do_feu_spell`, etc.),
  - `soin_spell`,
  - `vol_pv_spell` ou réutilisation,
  - `bouclier_spell` / `absorption_spell`.
- Si des clés manquent : **ajouter** les lignes dans le seeder (ou migration de données) avec une formule par défaut (ex. `[d]` pour passer la valeur, ou formule dédiée si définie).
- Documenter dans [CARACTERISTIQUES_EFFETS_PAR_ACTION.md](./CARACTERISTIQUES_EFFETS_PAR_ACTION.md) le mapping **action → characteristic_key** retenu (pour étape 3.1).

**Fichiers** : `database/seeders/data/characteristic_spell.php`, `SpellCharacteristicSeeder`, et/ou migrations de données ; mise à jour de la doc.

---

### Étape 3.5 — Actions « par caractéristique » (booster, retirer, voler-caracteristiques)

**Objectif** : pour ces actions, la characteristic_key est déjà dans `params.characteristic` (ex. `pa`, `pm`, `strong`). Il faut s’assurer que la clé utilisée pour la BDD soit celle du **groupe spell** (ex. `pa_spell`, `pm_spell` si les formules sont en `characteristic_spell` sous ces clés).

- Dans le resolver (étape 3.1) : pour booster/retirer/voler-caracteristiques, prendre `params.characteristic` et :
  - soit utiliser tel quel si `characteristic_spell` a des lignes pour ces clés,
  - soit appliquer un suffixe `_spell` (ou la convention du projet) pour retrouver la ligne dans `characteristic_spell`.
- Vérifier dans les seeders que les caractéristiques concernées (pa, pm, strong, res_terre, etc.) ont bien une entrée **spell** avec `conversion_formula` (et limites) si on souhaite les convertir.

**Fichiers** : même resolver ; seeders / BDD ; doc.

---

### Étape 3.6 — Tests et régression

**Objectif** : garantir que la conversion des valeurs d’effet est appliquée correctement et que le reste du pipeline (signature, déduplication, intégration) reste inchangé.

- **Tests unitaires** :
  - Resolver : pour chaque action (frapper, soigner, booster, retirer, déplacer, autre), vérifier la characteristic_key retournée (ou null).
  - Calcul de `d` : dés (13d18 → moyenne), valeur fixe, absence de valeur.
  - `SpellEffectsConversionService` : au moins un cas avec conversion (mock du DofusConversionService) et vérification de la présence de `value_converted` dans params.
- **Tests d’intégration** : un sort DofusDB avec dés et une caractéristique spell ayant une formule non triviale ; vérifier que les enregistrements en BDD (effect_sub_effect.params) contiennent `value_converted` cohérent avec la formule.
- **Non-régression** : les tests existants (preview, simulation, import) continuent de passer ; la signature d’effet reste basée sur les champs existants (ordre, sub_effect_id, params incluant value_formula, characteristic, etc.). Décider si `value_converted` doit entrer dans la signature (souvent non : la valeur convertie est dérivée de value_formula + formule BDD).

**Fichiers** : `tests/Unit/...`, `tests/Feature/...` (à créer ou étendre).

---

### Étape 3.7 — Option : exposition de la formule / valeur en prévisualisation

**Objectif** : en prévisualisation (et simulation), afficher éventuellement la **valeur convertie** (et la characteristic_key utilisée) à côté de la formule brute, pour faciliter le debug et la validation.

- Backend : la réponse de preview contient déjà les params (value_formula, characteristic, value_converted si présent).
- Frontend : dans le bloc « Simulation des effets », afficher par sous-effet : formule brute + « → converti : X » (et optionnellement la clé de caractéristique utilisée).

**Fichiers** : `SearchPreviewSection.vue` (ou composant dédié), pas de changement API si les params sont déjà renvoyés.

---

## 4. Ordre recommandé et dépendances

| Ordre | Étape | Dépendance |
|-------|--------|-------------|
| 1 | 3.4 + 3.5 (caractéristiques BDD / mapping doc) | Aucune ; permet de figer les clés pour le resolver. |
| 2 | 3.1 (resolver action → characteristic_key) | 3.4 documenté. |
| 3 | 3.2 (calcul de d) | Aucune. |
| 4 | 3.3 (intégration buildParams) | 3.1, 3.2, DofusConversionService injecté. |
| 5 | 3.6 (tests) | 3.3. |
| 6 | 3.7 (affichage preview) | Optionnel. |

---

## 5. Fichiers à créer ou modifier (résumé)

| Fichier | Action |
|---------|--------|
| `App\Services\Scrapping\Core\Conversion\SpellEffects\SpellEffectConversionFormulaResolver` | **Créer** (résolution action + characteristic → characteristic_key spell). |
| `SpellEffectsConversionService` | **Modifier** : injecter resolver + DofusConversionService ; calcul de `d` ; dans buildParams (et buildParamsForOther si besoin), appeler conversion et remplir `params.value_converted`. |
| `database/seeders/data/characteristic_spell.php` | **Vérifier / compléter** : clés dommages, soin, bouclier, etc. et formules. |
| `CARACTERISTIQUES_EFFETS_PAR_ACTION.md` | **Mettre à jour** : tableau action → characteristic_key (spell) utilisé par le resolver. |
| Tests unitaires / feature | **Créer ou étendre** (resolver, calcul d, conversion dans buildParams). |
| `SearchPreviewSection.vue` (ou équivalent) | **Optionnel** : afficher value_converted dans la simulation des effets. |

---

## 6. Décisions produit à trancher

1. **Frapper** : une seule caractéristique « dommages » (`dommages_spell`) pour tous les éléments, ou une clé par élément (`do_feu_spell`, `do_terre_spell`, …) ? Impact sur le resolver et sur les lignes dans `characteristic_spell`.
2. **Signature d’effet** : inclure ou non `value_converted` dans le calcul de `config_signature` ? Recommandation : **non**, pour que deux imports avec la même formule brute et la même config BDD produisent le même effet partagé ; la valeur convertie est dérivée.
3. **Fallback** : si aucune formule en BDD pour la characteristic_key, laisser uniquement `value_formula` (sans value_converted) ou remplir value_converted avec la moyenne des dés (ou valeur fixe) pour homogénéité affichage ?

---

## 7. Références

- [PLAN_IMPLEMENTATION_MAPPING_EFFETS.md](./PLAN_IMPLEMENTATION_MAPPING_EFFETS.md) — Phases 1 et 2, contexte Phase 3.
- [CARACTERISTIQUES_EFFETS_PAR_ACTION.md](./CARACTERISTIQUES_EFFETS_PAR_ACTION.md) — Règles par action.
- [FORMULES_CONVERSION_SORTS_REGLES.md](./FORMULES_CONVERSION_SORTS_REGLES.md) — Formules de conversion (power_spell, etc.) alignées sur les règles 5.3.1, 5.2.3, 3.3.2.
- [DOFUSDB_EFFECTS_CONVERSION.md](./DOFUSDB_EFFECTS_CONVERSION.md) — Données DofusDB (diceNum, diceSide, value).
- `DofusConversionService`, `CharacteristicGetterService`, `CharacteristicFormulaService` — Services existants à réutiliser.
