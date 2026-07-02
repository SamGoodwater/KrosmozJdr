# Vérification du pipeline effets / sous-effets (sorts)

Ce document répond à trois points de contrôle du pipeline de scrapping des sorts (effets et sous-effets).

---

## 1. Création automatique des effets et sous-effets ?

### Effets (Effect) et groupes (EffectGroup) — **créés automatiquement**

- **EffectGroup** : créé à la volée avec `firstOrCreate` sur `slug` (et `name`). Un groupe par sort (ou partagé si même slug).
- **Effect** : créé à la volée pour chaque degré/niveau du sort, avec `name`, `slug`, `degree`, `target_type`, `area`, `config_signature`. Aucune création si un effet avec la **même signature** existe déjà (voir section 2).

### Sous-effets (SubEffect) — **non créés automatiquement**

- Les **sous-effets** (frapper, soigner, booster, retirer, déplacer, invoquer, autre, etc.) sont des **référentiels** : ils doivent **exister en base** avant l’import.
- Le pipeline utilise le **slug** renvoyé par le mapping DofusDB → Krosmoz et fait un `SubEffect::whereIn('slug', $slugs)` pour récupérer les IDs. Si un slug n’existe pas dans la table `sub_effects`, la ligne de sous-effet est **ignorée** (elle n’apparaît pas dans la signature ni dans les pivots).
- **Référentiel** : `SubEffectSeeder` crée/met à jour les sous-effets (frapper, soigner, protéger, voler-vie, booster, retirer, voler-caracteristiques, déplacer, invoquer, autre). Pour ajouter un nouveau type d’action, il faut soit l’ajouter au seeder et relancer le seed, soit le créer via une interface admin si elle existe.

**En résumé** : on crée automatiquement les **Effect** et **EffectGroup** (avec déduplication pour Effect). Les **SubEffect** ne sont pas créés par le pipeline ; ils doivent être présents (seeder ou admin).

---

## 2. Vérification d’existence avant création ?

### Effect — **oui, déduplication par signature**

- Avant de créer un nouvel **Effect**, le service calcule une **signature** (`config_signature`) à partir des sous-effets normalisés (ordre, sub_effect_id, crit_only, params : characteristic, value_formula, value_formula_crit, value) **et** de `target_type` et `area`.
- Si un `Effect` existe déjà avec cette même `config_signature`, on **ne crée pas** de nouvel effet : on crée uniquement un **EffectUsage** (lien sort → effet existant). Ainsi, deux sorts avec les mêmes sous-effets (même configuration) partagent le même Effect.
- `target_type` (direct / trap / glyph) est déduit depuis les `triggers` DofusDB (P = piège, G = glyphe).

### EffectGroup — **oui, firstOrCreate**

- Création uniquement si le groupe (slug) n’existe pas ; sinon réutilisation.

### EffectSubEffect (pivot) — **oui, déduplication par (sub_effect_id, crit_only, params)**

- Avant d’ajouter une ligne dans `effect_sub_effect`, on vérifie qu’il n’existe pas déjà une ligne identique (même sous-effet, même crit_only, mêmes params) pour cet effet. Si oui, on ne crée pas de doublon.

### SubEffect — **N/A**

- Les SubEffect ne sont pas créés par le pipeline ; ils sont supposés déjà en base (seeder).

**En résumé** : oui, on vérifie avant de créer : **Effect** (par signature), **EffectGroup** (par slug), et **EffectSubEffect** (par déduplication sur le pivot).

---

## 3. Conversion des valeurs (dommages, etc.) pilotée par l’interface caractéristiques (groupe spell) ?

### Intention (documentation)

- [CARACTERISTIQUES_EFFETS_PAR_ACTION.md](./CARACTERISTIQUES_EFFETS_PAR_ACTION.md) et [PLAN_IMPLEMENTATION_MAPPING_EFFETS.md](./PLAN_IMPLEMENTATION_MAPPING_EFFETS.md) prévoient que la **conversion des valeurs** (dommages, soins, bonus, etc.) soit pilotée par les **caractéristiques du groupe spell** : formules de conversion et limites en BDD (`characteristic_spell`, `conversion_formula`, etc.), utilisées selon l’action (frapper, soigner, booster, etc.) et la caractéristique cible.

### État actuel du code (Phase 3 non finalisée)

- Les **propriétés de niveau de sort** (PA, portée, ligne de vue, etc.) sont bien **converties / bornées** via les caractéristiques (mapping + formatters + `CharacteristicLimitService`, groupe spell).
- Pour les **effets de sort** (sous-effets), la conversion actuelle :
  - **Construit** `value_formula` (ex. `13d18`) ou une valeur fixe à partir des champs DofusDB (`diceNum`, `diceSide`, `value`).
  - **Enregistre** cette valeur (et `characteristic` quand source = element) dans les **params** du pivot `effect_sub_effect`.
  - **N’applique pas** encore les formules de conversion BDD (groupe spell) à cette valeur : pas d’appel à `DofusConversionService` / `CharacteristicGetterService.getConversionFormula()` dans `SpellEffectsConversionService` pour transformer la valeur Dofus en valeur Krosmoz selon la caractéristique.

Donc : **l’interface de caractéristiques (groupe spell)** définit bien les formules et limites utilisées ailleurs (niveau de sort, PA, etc.), mais **la conversion des valeurs d’effet (dommages, soins, etc.) n’est pas encore branchée** sur cette interface ; c’est l’objectif de la **Phase 3** du plan (formules par action / par characteristic_key, appel au service de formules depuis la conversion des effets).

**En résumé** : la conversion des valeurs d’effet (dommages, soins, etc.) est **prévue** pour être contrôlée via l’interface des caractéristiques du groupe spell, mais n’est **pas encore implémentée** dans le pipeline de conversion des sous-effets ; les valeurs sont stockées telles quelles (formule dés ou valeur brute).

---

## Synthèse

| Question | Réponse |
|----------|--------|
| Création automatique de tous types de sous-effets et d’effets ? | **Effets (Effect) et groupes (EffectGroup)** : oui, créés automatiquement avec déduplication. **Sous-effets (SubEffect)** : non, ils doivent exister en base (SubEffectSeeder). |
| Vérification d’existence avant création ? | **Oui** : Effect (par config_signature), EffectGroup (firstOrCreate), EffectSubEffect (déduplication pivot). |
| Conversion des valeurs (dommages, etc.) pilotée par l’interface caractéristiques groupe spell ? | **Prévu** (doc + Phase 3), mais **pas encore en place** : les formules BDD du groupe spell ne sont pas encore appliquées aux valeurs des sous-effets. |

Pour brancher la conversion des valeurs d’effet sur les caractéristiques (groupe spell), il faut réaliser les tâches Phase 3 du [PLAN_IMPLEMENTATION_MAPPING_EFFETS.md](./PLAN_IMPLEMENTATION_MAPPING_EFFETS.md) (résolution de formule par action/caractéristique, appel depuis `SpellEffectsConversionService`).
