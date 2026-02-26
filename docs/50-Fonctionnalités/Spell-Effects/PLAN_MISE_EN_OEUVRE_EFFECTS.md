# Plan de mise en œuvre — Système effect / sous-effet

Ce document décrit le **plan de mise en œuvre** du système unifié d’effets (effect + sous-effet, entités → effect via effect_usage, degrés de puissance). Il s’appuie sur [MODELE_EFFECT_SOUS_EFFECT.md](./MODELE_EFFECT_SOUS_EFFECT.md) et [EFFETS_TEMPLATES_ET_SURETE.md](./EFFETS_TEMPLATES_ET_SURETE.md).

---

## Réutilisation : composants et services existants

Pour ne pas complexifier le code, réutiliser au maximum les éléments suivants.

### Formules (sous-effets)

| Élément | Rôle | Utilisation |
|--------|------|-------------|
| **CharacteristicFormulaService** | Évalue une formule avec variables `[id]`, tables JSON, fonctions (floor, ceil, etc.). Déjà en singleton dans `AppServiceProvider`. | Injecter dans le service d’effets : pour chaque sous-effet avec un champ `formula`, appeler `evaluate($formula, $context)` avec le contexte (level, agi, value, etc.). Pas de nouveau moteur de formules. |
| **FormulaResolutionService** | Moteur sous-jacent (validation + évaluation). Utilise `FormulaConfigDecoder` et `SafeExpressionEvaluator`. | Utilisable directement si on ne veut pas passer par CharacteristicFormulaService. |
| **Syntaxe** | Variables `[level]`, `[agi]`, opérateurs, `floor`/`ceil`, dés `ndX`. | Aligner la doc `SYNTAXE_EFFETS.md` sur [SYNTAXE_FORMULES_CARACTERISTIQUES.md](../../10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md) et [FORMULAS_PRACTICES.md](../../10-BestPractices/FORMULAS_PRACTICES.md). |

### Sanitization du texte

| Élément | Rôle | Utilisation |
|--------|------|-------------|
| **Mews\Purifier** | Nettoyage HTML (configs `default`, `section_text`). | Pour les effets on veut **texte seul** (pas d’HTML). Soit ajouter un profil Purifier très strict (ex. `effect_text` avec `HTML.Allowed` vide ou uniquement du texte), soit un **EffectTextSanitizer** dédié qui fait `strip_tags` + whitelist des motifs `[var]` et `ndX`. Préférer un petit service dédié pour les effets : règles spécifiques (pas de balises, conserver [nom] et ndX). |
| **NotificationService::truncateAndSanitize** | `strip_tags` + troncature. | Idée pour “texte brut” : même approche strip_tags pour tout champ template/description d’effet, sans Purifier riche. |

### Modèles et relations

| Élément | Rôle | Utilisation |
|--------|------|-------------|
| **Laravel morph** | Polymorphisme (morphTo, morphMany). | Pour **effect_usage** : `entity_type` + `entity_id` sur la table, et sur les modèles Spell / Item / Consumable une relation du type `morphMany(EffectUsage::class, 'entity')` (ou nom équivalent). Pas de composant métier existant à réutiliser ; le pattern Eloquent standard suffit. |
| **Pivot avec order** | Tables pivot avec colonne `order`. | Même pattern que **spell_effects** (spell_id, spell_effect_type_id, order) : **effect_sub_effect** avec effect_id, sub_effect_id, order, scope. |

### UI admin

| Élément | Rôle | Utilisation |
|--------|------|-------------|
| **SpellEffectTypeController** + **Admin/spell-effect-types/Index** | Liste à gauche, panneau d’édition à droite ; options (categories, value_types) partagées. | S’en inspirer pour **sous-effets** (liste + détail) et **effects** (liste + détail avec sous-effets ordonnés). Même structure Inertia : index avec `list` + `selected` + `options`. |
| **SpellController** (spell_effects) | Validation en tableau (`spell_effects.*.spell_effect_type_id`, etc.) et sync des lignes. | Même approche pour la liste des sous-effets d’un effect : `effect_sub_effects.*.sub_effect_id`, `effect_sub_effects.*.order`, `effect_sub_effects.*.scope`, etc. |
| **CharacteristicController** | CRUD + règles de mapping, pivot caractéristiques. | Référence pour formulaires complexes et validation. |

### Résumé

- **Formules** : réutiliser **CharacteristicFormulaService** (ou FormulaResolutionService), pas de nouveau moteur.
- **Sanitization** : créer un **EffectTextSanitizer** léger (strip_tags + conservation de `[var]` et ndX), éventuellement en s’inspirant de la config Purifier si on ajoute un profil `effect_text`.
- **Relations** : pattern Eloquent standard (morphMany pour effect_usage, belongsToMany avec pivot pour effect ↔ sub_effects).
- **UI admin** : s’aligner sur **SpellEffectTypeController** (liste + panneau) et **SpellController** (tableau de sous-effets validé et synchronisé).

---

## Vue d’ensemble des phases

| Phase | Objectif | Livrables principaux |
|-------|----------|----------------------|
| **0** | Prérequis : sûreté du texte | EffectTextSanitizer, tests, doc syntaxe [var] / ndX |
| **1** | Schéma BDD et modèles | Migrations, modèles Eloquent, relations |
| **2** | Services métier | Résolution variables, **résolution de formules** (sous-effets), formatage ndX, lecture effet par niveau |
| **3** | API et validation | Controllers, Form Requests, ressources API |
| **4** | UI admin (effets et usages) | CRUD effects, sous-effets, usages ; duplication degré |
| **5** | Intégration entités existantes | Lien Spell / Item / Consumable → effect_usage, affichage côté front |
| **6** | Import DofusDB (optionnel) | Génération effects / sub_effects à partir du scrapping |

Les phases 0 et 1 sont le socle ; 2–4 permettent d’utiliser le système en admin ; 5 et 6 l’intègrent aux entités et à l’import.

---

## Phase 0 — Prérequis : sûreté du texte

**Objectif** : tout texte d’effet (template, description) passe par un sanitizer unique ; documenter la syntaxe [var] et ndX.

| # | Tâche | Détail |
|---|--------|--------|
| 0.1 | **Service EffectTextSanitizer** | `App\Services\Effect\EffectTextSanitizer::sanitize(string $text): string`. Strip HTML/JS ; autoriser lettres, chiffres, ponctuation, `[nom_var]`, `ndX` (regex ou whitelist). Refuser ou échapper le reste. |
| 0.2 | **Tests unitaires** | Tests : texte sain inchangé ; balises HTML retirées ; `[agi]`, `2d6` préservés ; script/onerror rejetés. |
| 0.3 | **Documentation syntaxe** | Créer `SYNTAXE_EFFETS.md` : liste des variables autorisées (agi, strong, value, level, duration, element…), format ndX, **syntaxe des formules** (alignée sur FORMULAS_PRACTICES.md si réutilisation du moteur caractéristiques), exemples. |
| 0.4 | **Validation en entrée** | Règle : tout champ “template”, “description” ou “formula” d’effet/sous-effet doit être validé (ou sanitized) via ce service avant sauvegarde. |

**Livrables** : `EffectTextSanitizer`, tests, `docs/50-Fonctionnalités/Spell-Effects/SYNTAXE_EFFETS.md`. Lors des phases 3–4 (API / UI admin), les Form Requests ou mutators appelleront `EffectTextSanitizer::sanitize()` sur les champs template, description et formula avant sauvegarde.

---

## Phase 1 — Schéma BDD et modèles

**Objectif** : créer les tables et les modèles Eloquent avec relations.

| # | Tâche | Détail |
|---|--------|--------|
| 1.1 | **Table effect_groups** (optionnel) | id, name (ou slug), timestamps. Pour grouper les effects par “même effet logique” (degrés). |
| 1.2 | **Table sub_effects** | id ; slug (unique) ; type_slug (taper, soigner, vol_pa…) ; template_text (text, sanitized) ; **formula** (nullable, text) — expression à résoudre via le service de formules ; variables_allowed (JSON array) ; dofusdb_effect_id (nullable) ; timestamps. |
| 1.3 | **Table effects** | id ; name (nullable) ; slug (nullable, unique) ; **description** (nullable, text) — aperçu optionnel de ce que fait l’effet ; effect_group_id (nullable, FK effect_groups ou self) ; degree (nullable, 1,2,3…) ; timestamps. Pas de level : c’est sur effect_usage. |
| 1.4 | **Table effect_sub_effect** | id ; effect_id (FK) ; sub_effect_id (FK) ; order (smallint) ; **scope** (string, défaut `general` : `general` \| `combat` \| `out_of_combat`) — contexte d’application du sous-effet (combat / hors combat) ; value_min, value_max, dice_num, dice_side (nullable) ; params (JSON nullable) ; timestamps. |
| 1.5 | **Table effect_usage** | id ; entity_type (string : spell, item, consumable, resource…) ; entity_id (bigint) ; effect_id (FK effects) ; level_min (nullable) ; level_max (nullable) ; timestamps. Index (entity_type, entity_id). |
| 1.6 | **Modèles Eloquent** | SubEffect, Effect, EffectSubEffect (pivot), EffectUsage, EffectGroup (optionnel). Relations : Effect hasMany EffectSubEffect, belongsToMany SubEffect ; Effect belongsTo EffectGroup ; EffectUsage belongsTo Effect, morphTo entity ; Spell/Item/Consumable morphMany EffectUsage. |
| 1.7 | **Seeders de base** | Référentiel de types de sous-effets (taper, soigner, vol_pa, buff_agi…) dans sub_effects avec template_text par défaut (optionnel en phase 1). |

**Livrables** : migrations, modèles, seeders optionnels.

---

## Phase 2 — Services métier

**Objectif** : résolution des variables, **résolution des formules** (sous-effets), formatage ndX, et “quel effet pour quel niveau”.

| # | Tâche | Détail |
|---|--------|--------|
| 2.1 | **Résolution des variables** | Service ou helper : `resolveEffectText(string $template, array $context): string` — remplace `[agi]`, `[value]`, etc. par les valeurs du contexte (personnage, niveau, cible). Contexte = tableau associatif clé → valeur. |
| 2.2 | **Service de résolution de formules** | Les sous-effets peuvent contenir un champ **formula** (ex. `[level]*2 + [agi]`). Un service (ex. `EffectFormulaService` ou réutilisation de `CharacteristicFormulaService` / `FormulaResolutionService`) doit **évaluer** cette formule avec le contexte (level, agi, value, etc.). Aligner la syntaxe sur celle des caractéristiques (voir FORMULAS_PRACTICES.md) pour réutiliser le même moteur. |
| 2.3 | **Formatage ndX** | Helper : affichage “2d6” → “2d6” ou “2 dés à 6 faces” (configurable). Pas d’exécution de lancer de dé ici. |
| 2.4 | **Effet pour un niveau donné** | Pour une entité et un niveau : récupérer les effect_usage dont level_min ≤ level ≤ level_max, ordonner, retourner les effects avec leurs sous-effets. Signature du type `getEffectsForEntity(string $entityType, int $entityId, int $level, ?string $context = null): Collection` avec **context** optionnel : `combat` \| `out_of_combat` \| null (tous). Filtrer les sous-effets : si context = `combat` → scope in (`general`, `combat`) ; si `out_of_combat` → scope in (`general`, `out_of_combat`) ; si null → tous. |
| 2.5 | **Rendu texte complet d’un effect** | À partir d’un Effect (avec sous-effets ordonnés, éventuellement filtrés par **scope** selon le contexte combat/hors combat), résoudre chaque sous-effet : template + formula + paramètres ; concaténer, retourner un texte ou un DTO. Si l’effect a une **description**, elle peut servir d’aperçu sans résoudre les sous-effets. |

**Livrables** : `EffectService`, **service de résolution de formules** (dédié ou réutilisation), helpers résolution/ndX, possiblement `EffectTextResolver` dédié.

---

## Phase 3 — API et validation

**Objectif** : exposer les effects, sous-effets et usages en API (admin et lecture), avec validation.

| # | Tâche | Détail |
|---|--------|--------|
| 3.1 | **Routes API** | CRUD sub_effects (admin) ; CRUD effects (admin) ; CRUD effect_usage (scoped par entity_type + entity_id) ; liste des effects pour une entité + niveau (GET). |
| 3.2 | **Controllers** | SubEffectController, EffectController, EffectUsageController (ou un EffectApiController regroupé). EffectUsage en contexte “sur un sort” / “sur un item” (nested ou query param). |
| 3.3 | **Form Requests** | Store/Update SubEffect (template_text passé par EffectTextSanitizer ou validation) ; Store/Update Effect (name, effect_group_id, degree) ; Store/Update EffectUsage (entity_type, entity_id, effect_id, level_min, level_max). |
| 3.4 | **Ressources API** | SubEffectResource, EffectResource (avec sous-effets, paramètres, **scope**), EffectUsageResource. Réponse “effet pour niveau X” (et option **context** = combat \| out_of_combat) = liste d’effects avec sous-effets filtrés et texte résolu. |

**Livrables** : routes, controllers, form requests, resources.

---

## Phase 4 — UI admin (effets et usages)

**Objectif** : interface pour gérer les effects, sous-effets, usages et degrés.

| # | Tâche | Détail |
|---|--------|--------|
| 4.1 | **CRUD Sous-effets** | Liste, création, édition des sub_effects (slug, type, template_text, **formula** optionnelle, variables). Template_text et formula validés/sanitized. Aide à la saisie : liste des variables, exemples ndX et **syntaxe des formules** (lien vers SYNTAXE_EFFETS.md). |
| 4.2 | **CRUD Effects** | Liste, création, édition d’un effect : name, description (optionnel), effect_group, degree. Gestion de la liste des sous-effets : ordre, paramètres (value_min/max, dice, params), et **scope** (Général \| Combat \| Hors combat). Le sélecteur de scope reste **discret** (petit select ou lien “Contexte” repliable), valeur par défaut Général, pour ne pas surcharger l’UI (cas rare, ex. ~30 sorts sur 19 classes). Drag & drop pour l’ordre (optionnel). |
| 4.3 | **Duplication “Ajouter un degré”** | Bouton “Ajouter un degré” sur un effect : duplication de l’effect (même effect_group_id, degree + 1, même name). Redirection vers l’édition du nouvel effect pour ajuster les sous-effets. |
| 4.4 | **Gestion des usages sur une entité** | Sur la fiche Sort / Item / Consumable : section “Effets”. Liste des effect_usage (tranche level_min–level_max → effect). Ajout : choisir un effect (et optionnellement un groupe pour filtre), renseigner level_min, level_max. Édition / suppression d’un usage. |
| 4.5 | **Affichage “effet pour niveau X”** | Sur la fiche entité, afficher pour un niveau donné (select ou slider) les effects appliqués et le texte résolu (prévisualisation) en s’appuyant sur le service Phase 2. |

**Livrables** : pages ou composants Vue (admin) pour sub_effects, effects, effect_usage, duplication degré.

**État (2026-02)** : Phase 4 réalisée. CRUD admin sous-effets et effects (routes `admin.sub-effects.*`, `admin.effects.*`), duplication degré, section Effets sur fiches Sort / Item / Consumable via `EffectUsagesManager` (usages + aperçu pour un niveau). Liens « Sous-effets » et « Effets » dans le menu Administration.

---

## Phase 5 — Intégration entités existantes

**Objectif** : brancher Spell, Item, Consumable (et Resource si besoin) sur effect_usage et afficher les effets côté front.

| # | Tâche | Détail |
|---|--------|--------|
| 5.1 | **Relations sur les entités** | Spell, Item, Consumable (Resource) : relation `effectUsages()` (morphMany EffectUsage). Optionnel : accessor “effects for level X” qui appelle EffectService. |
| 5.2 | **Affichage public** | Où les effets sont affichés (fiche sort, fiche objet, tooltip…) : appeler l’API ou le service pour récupérer les effects pour le niveau pertinent ; afficher le texte résolu (ou les sous-effets formatés). |
| 5.3 | **Migration des données existantes** | Si spell_effects / spell_effect_types existent encore : décider soit de les garder en parallèle (effet “legacy” vs nouveau système), soit d’un script de migration qui crée des sub_effects + effects + effect_usage à partir des anciennes données. À traiter en phase 5 ou 6 selon la priorité. |

**Livrables** : relations sur les modèles, intégration affichage, éventuel script de migration.

---

## Phase 6 — Import DofusDB (optionnel)

**Objectif** : lors du scrapping, créer ou réutiliser des sub_effects et effects à partir des données DofusDB (items, spell-levels).

| # | Tâche | Détail |
|---|--------|--------|
| 6.1 | **Mapping effectId DofusDB → type sous-effet** | Config ou table : dofusdb_effect_id → sub_effect (slug ou id). Pour les effectId connus, récupérer ou créer le sub_effect et remplir template_text / paramètres à partir de from, to, dice, characteristic. |
| 6.2 | **Création d’effects lors de l’import item** | Pour chaque item importé avec effects[] : créer un effect (ou réutiliser un effect existant si même combinaison), lier les sous-effets avec les paramètres (value, dice), créer effect_usage (entity_type=item, entity_id, effect_id, level_min/max si disponible). |
| 6.3 | **Création d’effects lors de l’import spell** | Pour chaque spell-level avec effects[] : créer un effect par niveau (ou par tranche), lier aux sous-effets, créer effect_usage (entity_type=spell, entity_id, effect_id, level_min, level_max selon le grade du level). Grouper en effect_group + degree si plusieurs niveaux. |
| 6.4 | **Sanitization** | Tout template_text issu de DofusDB (ou généré) doit passer par EffectTextSanitizer avant insertion. |

**Livrables** : pipeline d’import (formatters ou jobs) qui alimentent sub_effects, effects, effect_usage ; config mapping DofusDB → sub_effect.

---

## Ordre recommandé et dépendances

```
Phase 0 (Sanitizer + doc syntaxe)
    ↓
Phase 1 (Migrations + modèles)
    ↓
Phase 2 (Services résolution / niveau)
    ↓
Phase 3 (API)  ←→  Phase 4 (UI admin)   [en parallèle possible après 3.1]
    ↓
Phase 5 (Intégration entités)
    ↓
Phase 6 (Import DofusDB, optionnel)
```

- **Phase 0** est indispensable avant toute saisie ou import de texte d’effet.
- **Phase 1** doit être terminée avant 2 et 3.
- **Phase 4** peut avancer dès que l’API (Phase 3) est suffisante pour le CRUD.
- **Phase 5** peut être partielle (uniquement Spell au début) puis étendue à Item / Consumable.
- **Phase 6** peut être reportée si l’on privilégie la saisie manuelle des effets au démarrage.

---

## Récapitulatif des livrables par phase

| Phase | Fichiers / éléments livrés |
|-------|----------------------------|
| 0 | `App\Services\Effect\EffectTextSanitizer`, tests, `SYNTAXE_EFFETS.md` (variables, ndX, **syntaxe formules**) |
| 1 | Migrations (effect_groups, sub_effects avec **formula**, effects avec **description**, effect_sub_effect, effect_usage), modèles Eloquent, seeders optionnels |
| 2 | `EffectService`, **service résolution de formules** (sous-effets), helpers résolution/ndX, `EffectTextResolver` (optionnel) |
| 3 | Routes API, SubEffectController, EffectController, EffectUsageController, Form Requests, Resources |
| 4 | Pages/composants admin Vue (sous-effets, effects, usages, “Ajouter un degré”) |
| 5 | Relations sur Spell/Item/Consumable, affichage front, script migration optionnel |
| 6 | Config mapping DofusDB, pipeline import (formatters/jobs), sanitization dans l’import |

Ce plan peut être découpé en tickets (issues) ou tâches de sprint en reprenant les numéros de tâches (0.1, 1.1, etc.).
