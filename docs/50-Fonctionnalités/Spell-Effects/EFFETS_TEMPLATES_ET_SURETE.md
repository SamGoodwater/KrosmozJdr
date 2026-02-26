# Effets (items & sorts) : templates, variables et sûreté

Proposition d’implémentation pour gérer les **effets textuels** des objets et des sorts : récupération DofusDB, sûreté du texte, variables (caractéristiques, dés), réutilisation et variantes par niveau.

---

## 1. Besoins exprimés

| Besoin | Description |
|--------|-------------|
| **Récupération** | Récupérer les effets des objets (et sorts) depuis DofusDB. |
| **Sûreté** | Texte sûr : pas d’HTML, pas de JS (validation / sanitization). |
| **Variables** | Les effets peuvent dépendre de caractéristiques (ex. vitesse ↔ agilité). Utiliser des placeholders complétables plus tard, ex. `[agi]`. Pour l’aléatoire : notation **ndX** (ex. 2d6). |
| **Réutilisation** | Un même effet peut être utilisé à plusieurs endroits (plusieurs sorts, plusieurs items). |
| **Par niveau** | Un effet peut différer selon le niveau (niveau du sort, niveau de l’objet). |

---

## 2. État actuel (rappel)

- **Items** : `effect` (JSON bonus Krosmoz converti), `bonus` (JSON brut DofusDB). Pas de texte d’effet réutilisable ni de template.
- **Sorts** : `spell_effect_types` (référentiel : slug, category, description, value_type, dofusdb_effect_id), `spell_effects` (instance par sort : value_min/max, dice_num/side, `raw_description`). `Spell.effect` = JSON (normalized + bonuses).
- **DofusDB** : dictionnaire `/effects` (définitions) ; instances sur `/items`, `/spell-levels` (effects[] avec effectId, from, to, value, etc.).

---

## 3. Principes de l’implémentation proposée

- **Simplicité** : réutiliser au maximum `spell_effect_types` et `spell_effects`, étendre si besoin plutôt que dupliquer.
- **Robustesse** : un seul flux de texte “effet” (template) → sanitization → stockage ; résolution des variables au moment de l’affichage ou du calcul.
- **Cohérence** : même mécanisme de template + variables pour sorts et items ; même règle de sûreté pour tout texte importé ou saisi.

---

## 4. Architecture proposée

### 4.1 Vue d’ensemble

```
┌─────────────────────────────────────────────────────────────────────────────┐
│  DofusDB                                                                     │
│  GET /effects (dictionnaire)   GET /items, /spell-levels (instances)        │
└───────────────────────────────────┬─────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  Import / Scrapping                                                          │
│  • Extraire texte ou générer description à partir des champs (from, to,      │
│    dice, characteristic).                                                    │
│  • Sanitization obligatoire avant tout stockage (voir § 5).                  │
│  • Créer / lier effect_template (réutilisable) + usage (spell_effect ou      │
│    item_effect) avec paramètres et niveau.                                    │
└───────────────────────────────────┬─────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  Stockage                                                                     │
│  • effect_templates : texte template avec placeholders [agi], [value], ndX   │
│  • spell_effects : déjà existant (raw_description, value_min/max, dice…)     │
│  • item_effects (nouveau, optionnel) : lien item ↔ effect_template + params │
│  • Niveau : soit sur l’usage (spell_effect par level, item par level),       │
│    soit table effect_template_levels (template_id, level_min, level_max,    │
│    template_text ou paramètres).                                              │
└───────────────────────────────────┬─────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  Affichage / Règles                                                           │
│  • Résolution des variables : [agi] → valeur du personnage/cible             │
│  • ndX : affiché tel quel (“2d6”) ou résolu en “2 dés à 6 faces”             │
│  • Pas de ré-exécution HTML/JS : le texte stocké est déjà sûr.                │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 4.2 Réutilisation d’un même effet

- **Effect template** = une entrée “définition” : texte template (santé), variables autorisées, optionnellement `dofusdb_effect_id`.
- **Usages** :
  - **Sorts** : `spell_effects` (existant) référence `spell_effect_type_id`. On peut faire pointer `spell_effect_types` vers un `effect_template_id` (ou stocker le template directement sur `spell_effect_types`).
  - **Items** : soit une table `item_effects` (item_id, effect_template_id, value_min, value_max, dice_num, dice_side, level_min, level_max, order), soit un JSON structuré sur `items` qui référence des template_id + paramètres.

Recommandation : **une table `effect_templates`** partagée ; **spell_effect_types** et éventuellement **item_effects** y font référence. Ainsi “Dégâts Terre 10–20” est un template ; plusieurs sorts ou items l’utilisent avec des paramètres différents.

### 4.3 Niveau

- **Sorts** : les effets sont déjà par niveau de sort (spell_levels dans DofusDB). En Krosmoz, on peut avoir `spell_effects` avec un champ `spell_level_min` / `spell_level_max` (ou un lien vers une “version” du sort par niveau).
- **Items** : niveau d’objet (level) ; on peut avoir `item_effects` avec level_min / level_max, ou une table `effect_template_levels(template_id, level_min, level_max, template_text)` pour varier le texte par tranche de niveau.

Recommandation : **niveau sur l’usage** (spell_effect / item_effect) avec level_min / level_max ; si le texte du template change fortement par niveau, soit plusieurs lignes d’usage (une par tranche), soit une table dédiée `effect_template_levels` pour éviter de dupliquer des templates entiers.

---

## 5. Sûreté du texte (sanitization)

- **Règle** : tout texte issu de DofusDB ou saisi par un utilisateur et stocké en base doit passer par un **sanitizer** unique.
- **Contraintes** :
  - Aucun HTML (strip tags ou refus si balises présentes).
  - Aucun JavaScript (pas d’event handlers, pas d’URL javascript:).
  - Caractères autorisés : par exemple Unicode “texte” (lettres, chiffres, ponctuation courante, espaces, retours à la ligne). Option : whitelist de caractères ou utilisation d’une lib (ex. HTML Purifier en mode “texte seul” ou équivalent Laravel).
- **Placeholders** : autoriser explicitement la forme `[nom_variable]` (ex. `[agi]`, `[value]`, `[level]`) et **ndX** (ex. `2d6`, `1d4`) dans le template ; le sanitizer ne doit pas casser ces motifs.
- **Implémentation** : un service dédié, ex. `App\Services\Effect\EffectTextSanitizer`, avec une méthode `sanitize(string $text): string` utilisée à chaque écriture (import, création, mise à jour). Option : validation en entrée (refuser si le texte ne passe pas le sanitizer) plutôt que modifier silencieusement.

---

## 6. Variables et notation ndX

- **Variables** : liste explicite des noms autorisés (ex. `agi`, `strong`, `intel`, `value`, `level`, `duration`). Dans le template, notation `[nom]`. À l’affichage ou au calcul, un contexte (personnage, cible, niveau) fournit les valeurs ; remplacement par la valeur ou par un libellé “X (basé sur Agi)”.
- **Dés** : notation **ndX** (n = nombre de dés, X = faces). Stockée telle quelle dans le template ; à l’affichage, on peut la laisser en “2d6” ou la formater “2 dés à 6 faces”. Pas d’exécution de lancer de dé côté serveur pour l’affichage ; l’exécution réelle (jeu) peut être dans un module séparé qui interprète ndX.
- **Cohérence** : documenter la liste des variables et la syntaxe ndX dans un fichier (ex. `docs/50-Fonctionnalités/Spell-Effects/SYNTAXE_EFFETS.md`) et s’y tenir partout (import, édition, affichage).

### 6.1 Formules dans les sous-effets

Les sous-effets peuvent contenir une **formule** (champ dédié ou expression dans le template) à **résoudre** avec un service de résolution de formules. Ex. `[level]*2 + [agi]` : le moteur évalue l’expression en injectant le contexte (niveau, caractéristiques). La syntaxe peut s’aligner sur celle des caractéristiques (voir `docs/10-BestPractices/FORMULAS_PRACTICES.md`) pour réutiliser le même service (ex. `FormulaResolutionService`). Documenter la syntaxe des formules dans `SYNTAXE_EFFETS.md`.

### 6.2 Description optionnelle sur l’effect

La table **effects** peut porter un champ **description** (optionnel) : court aperçu textuel de ce que fait l’effet, sans détailler chaque sous-effet. Utile pour l’affichage en liste ou en tooltip. Ce texte doit aussi passer par le sanitizer.

---

## 7. Plan d’implémentation par étapes

| Étape | Objectif | Fichiers / BDD |
|-------|----------|----------------|
| **1** | Sanitization | `EffectTextSanitizer`, tests unitaires, utilisation dans tout flux qui écrit une description d’effet (import + formulaires). |
| **2** | Table `effect_templates` | Migration : id, slug (unique), template_text (text, sanitized), variables_allowed (JSON array), dice_notation_allowed (bool), dofusdb_effect_id (nullable), timestamps. Option : level_min/level_max si on gère le niveau au niveau du template. |
| **3** | Lien spell_effect_types → effect_templates | Migration : effect_template_id nullable sur spell_effect_types. Remplir template_text à partir de description existante + sanitize. |
| **4** | Variables et ndX dans les templates | Documenter syntaxe ; dans l’UI (admin), afficher les variables autorisées et exemples ndX ; à l’affichage public, résolution [var] et formatage ndX. |
| **5** | Import DofusDB → effect_templates | Lors du scrapping sorts/items : pour chaque effet (effectId), créer ou récupérer un effect_template, générer un template_text avec [value], [agi], etc. et ndX si besoin ; sanitize puis enregistrer. Lier spell_effects / item_effects aux templates. |
| **6** | Niveau (optionnel) | Ajouter level_min / level_max sur spell_effects et sur item_effects ; ou table effect_template_levels. Adapter l’API d’affichage pour retourner le bon texte selon le niveau. |
| **7** | Items : table item_effects (optionnel) | Si on veut le même modèle que les sorts : item_effects (item_id, effect_template_id, value_min, value_max, dice_num, dice_side, level_min, level_max, order). Sinon, garder un JSON sur items qui référence des template_id + paramètres. |

---

## 8. Résumé des choix

- **Un référentiel de templates** (`effect_templates`) : texte sûr, variables explicites, ndX autorisé.
- **Réutilisation** : sorts et items référencent ces templates (via spell_effect_types ou item_effects).
- **Sûreté** : un seul point d’entrée (EffectTextSanitizer) pour tout texte d’effet.
- **Niveau** : géré sur l’usage (spell_effect / item_effect) avec level_min / level_max pour rester simple.
- **Cohérence** : une syntaxe documentée ([var], ndX) et appliquée partout.

Cette approche reste simple (peu de tables nouvelles si on réutilise spell_effect_types), robuste (sanitization centralisée, pas d’HTML/JS), et cohérente (même mécanisme pour items et sorts).
