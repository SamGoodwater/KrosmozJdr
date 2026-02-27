# Système d’effets — état actuel

Ce document décrit **ce qui existe aujourd’hui** pour le système d’effets : modèle de données, services, API et UI admin.  
Les documents de référence sont : [MODELE_EFFECT_SOUS_EFFECT.md](./MODELE_EFFECT_SOUS_EFFECT.md), [ARCHITECTURE_EFFETS_3_COUCHES.md](./ARCHITECTURE_EFFETS_3_COUCHES.md), [SYNTAXE_EFFETS.md](./SYNTAXE_EFFETS.md).

---

## 1. Modèle de données en base

- `sub_effects` :
  - Référentiel des **actions de base** (`slug`, `type_slug`) avec `template_text`, `variables_allowed`, `param_schema`.
  - Rempli par `SubEffectSeeder` (actions : `booster`, `retirer`, `soigner`, `frapper`, …).
- `effects` :
  - Conteneur d’un **ensemble de sous-effets**.
  - Champs principaux : `name`, `slug`, `description`, `effect_group_id` (groupe de degrés), `degree`.
- `effect_sub_effect` :
  - Pivot `EffectSubEffect` entre `effects` et `sub_effects`.
  - Champs principaux :
    - `order`, `scope` (`general`, `combat`, `out_of_combat`),
    - `params` (JSON) : `{ characteristic, value_formula }`,
    - `duration_formula` (formule numérique pour la durée),
    - `logic_group`, `logic_operator` (`AND` / `OR`), `logic_condition` (formule booléenne numérique pour les `OR`),
    - champs legacy `value_min`, `value_max`, `dice_num`, `dice_side` (encore présents mais non utilisés dans la nouvelle UI).
- `effect_usages` :
  - Lien polymorphique `EffectUsage` : `entity_type` (`spell`, `item`, `consumable`, `resource`), `entity_id`, `effect_id`, `level_min`, `level_max`.
  - Permet de dire : “pour cette entité et cette tranche de niveaux, on applique tel effect”.

---

## 2. Services côté backend

- `EffectTextSanitizer` :
  - Nettoie tout texte d’effet (template, description, formules texte) : supprime HTML/JS, conserve `[variables]` et la notation `ndX`.
  - Utilisé dans les FormRequests (Store/Update Effect/SubEffect) et dans les seeders.

- `EffectTextResolver` :
  - Remplace les `[variables]` dans un texte à partir d’un **contexte** associatif.
  - Formate éventuellement la notation `ndX` en texte humain (“2 dés à 6 faces”).

- `EffectService` :
  - `getEffectsForEntity(string $entityType, int $entityId, int $level, ?string $context = null)` :
    - retourne la liste des `Effect` applicables pour une entité et un niveau donnés (via `effect_usages`),
    - filtre les sous‑effets selon le `scope` et le contexte (`combat` / `out_of_combat`).
  - `renderEffectText(Effect $effect, array $context = [], ?string $scope_filter = null, bool $format_dice_human = false)` :
    - produit un **texte global** en résolvant les templates des sous‑effets (ancienne logique, sans AND/OR).

- `EffectResolutionService` :
  - Moteur de **résolution détaillée** pour un `Effect` :
    - parcourt les lignes `effect_sub_effect` (scope, ordre),
    - construit un contexte par sous‑effet à partir de `params` (caractéristique, `value_formula`, etc.) et du contexte de base,
    - évalue `value_formula` et `duration_formula` via `CharacteristicFormulaService`,
    - applique la logique **AND / OR** avec `logic_condition`,
    - résout `template_text` par sous‑effet et renvoie une structure :
      - action (`action_slug`), caractéristique, valeur, durée,
      - opérateurs logiques, texte résolu, contexte utilisé.

---

## 3. API exposée

Contrôleur : `App\Http\Controllers\Api\Effect\EffectController`.

- `GET /api/effects/effects` :
  - Retourne la liste des `Effect` avec leurs sous‑effets (ressource `EffectResource`).

- `GET /api/effects/for-entity` :
  - Paramètres :
    - `entity_type` : `spell`, `item`, `consumable`, `resource`,
    - `entity_id` : identifiant de l’entité,
    - `level` : niveau,
    - `context` : `combat` \| `out_of_combat` \| null,
    - `format_dice_human` : booléen (formatage “2d6” → “2 dés à 6 faces”).
  - Réponse : pour chaque effect applicable :
    - `effect` : `EffectResource`,
    - `resolved_text` : texte global (compat, via `EffectService::renderEffectText`),
    - `resolved` : sortie détaillée de `EffectResolutionService::resolveEffect` (par sous‑effet),
    - `description` : description courte de l’effect.

---

## 4. UI admin actuelle

- **Admin Effets** (`resources/js/Pages/Admin/effects/Index.vue`) :
  - Liste des `Effect` à gauche, panneau d’édition à droite.
  - Pour chaque effect :
    - `name`, `slug`, `description`, `effect_group_id`, `degree`,
    - liste ordonnée de sous‑effets (pivot `effect_sub_effect`) avec :
      - **Action** : choix d’un `SubEffect` (slug),
      - **Contexte** : `scope` (`general`, `combat`, `out_of_combat`),
      - **Caractéristique** : select sur les caractéristiques/éléments (config `effect_sub_effects.characteristics`),
      - **Valeur (formule)** : champ texte `value_formula`,
      - **Durée (formule)** : champ texte `duration_formula`,
      - **Logique** : `logic_operator` (ET/OU) + `logic_condition` pour les OU.
    - Bouton “Ajouter un degré” qui duplique l’effect (même groupe, degree+1) avec les mêmes sous‑effets.

- **Entités (Spell / Item / Consumable / Resource)** :
  - Chaque entité a une relation `effectUsages()` (morphMany) vers `effect_usages`.
  - Sur la fiche Sort, l’UI `EffectUsagesManager` permet de lier des `Effect` à des tranches de niveau.

---

## 5. Résumé fonctionnel

- Les **sous‑effets** sont définis par un seeder (`SubEffectSeeder`) et décrits par un `template_text` + `param_schema`.
- Un **Effect** est un conteneur de sous‑effets (`effect_sub_effect`) avec :
  - action, caractéristique, valeur (formule), durée (formule),
  - contexte (scope) et logique (ET/OU + condition).
- Les entités (sorts, items, etc.) utilisent les effects via `effect_usages` (tranches de niveau).
- Les services `EffectService` et `EffectResolutionService` fournissent :
  - soit un texte global (compat),
  - soit une résolution détaillée par sous‑effet, prête pour un moteur de combat ou une UI avancée.
