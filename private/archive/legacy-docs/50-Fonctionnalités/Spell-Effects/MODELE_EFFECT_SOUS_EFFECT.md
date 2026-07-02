# Modèle unifié : effect + sous-effet (une table effect, interopérable)

Ce document décide du **modèle de données** pour les effets : une seule table **effect** partagée par toutes les entités (sorts, items, etc.), avec une décomposition en **sous-effets** (ex. taper, soigner, vol PA) pour pouvoir composer et réutiliser.

---

## 1. Objectifs

- **Une seule table d’effets** : pas de table dédiée “spell_effect” vs “item_effect”. Toutes les entités (sort, item, consommable, etc.) pointent vers la même table **effects**.
- **Interopérabilité** : un même effet peut être attaché à un sort, à un item, ou aux deux ; pas de duplication par type d’entité.
- **Effet = texte + variables + dés** : l’effet (ou le sous-effet) est essentiellement du texte avec des placeholders `[var]` et une notation `ndX`.
- **Sous-effets** : pouvoir décomposer un effet en **sous-catégories** (ex. “taper” = ndX dégâts de type [element]). Un effet complet = liste ordonnée de sous-effets. Les variables (ndX, var) sont dans le sous-effet.

---

## 2. Deux schémas possibles

### Option A : La table effect contient les sous-effets ; les entités listent des sous-effets

- **effect** = un enregistrement par “effet complet” (ex. “Dégâts 2d6 feu + Vol 1 PA”), et cet enregistrement **contient** la liste des sous-effets (JSON ou table dédiée `effect_sub_effects`).
- Les sorts/items auraient un lien **direct vers les sous-effets** : “ce sort utilise sous-effet #1, #3, #5”.

**Problème** : si un sort “utilise” des sous-effets directement, on ne réutilise pas un “effet” en tant que bloc. On recompose à chaque fois la liste. Et si deux sorts ont exactement le même ensemble (2d6 feu + vol 1 PA), on duplique soit la liste, soit on est obligé d’introduire un “effet” = ensemble de sous-effets pour éviter la duplication. Donc on revient vers une notion d’effet = conteneur.

### Option B : Deux tables (effect + sub_effect) ; les entités lient l’effet, pas les sous-effets

- **sub_effects** (sous-effets) : atome réutilisable. Ex. type “taper”, template “ndX dégâts [element]”, variables `['element']`, dés `ndX`. Un sous-effet ne connaît pas le niveau.
- **effects** : **conteneur** = un niveau (ou tranche de niveau) + une **liste ordonnée de sous-effets**. Ex. “Effet niveau 1–5” = effect avec level_min=1, level_max=5, contenant [sous-effet taper 2d6 feu, sous-effet vol 1 PA].
- **Entités (sort, item)** : lien vers **effect** uniquement. Ex. “Ce sort a l’effet #42 pour les niveaux 1–5 et l’effet #43 pour les niveaux 6–10.” On ne lie jamais un sort/item directement à un sous-effet.

**Avantages** :
- Réutilisation claire : un même **effect** (ex. #42) peut être attaché à plusieurs sorts ou items.
- Niveau géré au bon endroit : l’effect porte level_min / level_max ; le lien entité → effect peut aussi porter une tranche de niveau (ex. “ce sort à son niveau 3 utilise effect #42”).
- Sous-effets = briques réutilisables ; effect = combinaison figée (pour un niveau donné) ; entité = “qui utilise quel effet à quel niveau”.

**Recommandation : Option B** (deux tables, entités → effect).

---

## 3. Modèle retenu : effect + sub_effect, entités → effect

### 3.1 Tables

| Table | Rôle |
|-------|------|
| **sub_effects** | Atome : type (slug, ex. `taper`, `soigner`, `vol_pa`), template texte (sûr), variables autorisées (JSON), notation dés. **Formules** : un sous-effet peut contenir une ou des formules (ex. `[level]*2 + [agi]`) à résoudre via un **service de résolution de formules** ; les variables sont fournies par le contexte (niveau, caractéristiques du personnage, etc.). Pas de niveau. Réutilisable dans plusieurs effects. |
| **effects** | Conteneur : name/slug (optionnel), **description** (optionnel, text) pour donner un **aperçu** de ce que fait l’effet si besoin ; **effect_group_id** (optionnel, pour grouper les degrés), **degree** (optionnel, 1, 2, 3…). Pas de niveau : c’est effect_usage qui porte level_min / level_max. |
| **effect_sub_effect** | Pivot : effect_id, sub_effect_id, order ; **scope** (`general` \| `combat` \| `out_of_combat`, défaut `general`) — indique si la description du sous-effet s’applique en tout contexte, uniquement en combat ou uniquement hors combat ; valeurs concrètes (optionnel) : value_min, value_max, dice_num, dice_side, params JSON. Ainsi le même sous-effet “taper” peut être utilisé dans un effect avec “2d6 feu” et dans un autre avec “1d4 neutre”. |
| **effect_usage** (ou **entity_effect**) | Lien polymorphique : entity_type (`spell`, `item`, `consumable`, …), entity_id, effect_id, level_min, level_max. “Ce sort (entity_id=7) a effect_id 42 pour niveau 1–5 et effect_id 43 pour niveau 6–10.” |

Les entités ne référencent **jamais** un sous-effet directement ; elles référencent uniquement un **effect**. L’effect lui-même contient la liste de ses sous-effets (via la pivot effect_sub_effect).

**Formules dans les sous-effets** : un sous-effet peut stocker une expression formulaire (ex. dans un champ `formula` ou intégrée au template). Au moment du rendu ou du calcul, un **service de résolution de formules** évalue cette expression en injectant le contexte (niveau, agi, value, etc.). La syntaxe des formules peut s’aligner sur celle utilisée ailleurs dans le projet (ex. caractéristiques, voir `docs/10-BestPractices/FORMULAS_PRACTICES.md`) pour réutiliser le même moteur d’évaluation.

**Description sur l’effect** : le champ **description** (optionnel) sur la table effects permet de saisir un court aperçu de ce que fait l’effet (résumé lisible), sans avoir à détailler chaque sous-effet. Utile pour l’affichage en liste ou en tooltip.

**Contexte combat / hors combat** : certains effets diffèrent selon que le personnage est en combat ou hors combat (ex. une poignée de sorts par classe). Sur la pivot **effect_sub_effect**, un champ **scope** (`general` par défaut, `combat`, `out_of_combat`) indique pour quel contexte s’applique ce sous-effet. Règles d’affichage : *en combat* → sous-effets avec scope `general` ou `combat` ; *hors combat* → sous-effets avec scope `general` ou `out_of_combat`. L’UI reste **discrète** : un simple sélecteur (liste ou onglet) lors de l’ajout/édition d’un sous-effet à un effect, valeur par défaut « Général », sans surcharger l’écran (cas peu fréquent, ex. ~30 sorts sur 19 classes).

### 3.2 Schéma relationnel simplifié

```
┌─────────────────┐       ┌──────────────────────┐       ┌─────────────────┐
│ sub_effects     │       │ effect_sub_effect     │       │ effects         │
├─────────────────┤       ├──────────────────────┤       ├─────────────────┤
│ id              │◄──────│ sub_effect_id        │       │ id              │
│ slug (taper…)   │       │ effect_id            │──────►│ name, group_id, │
│ template_text   │       │ order                │       │ degree          │
│ variables (json)│       │ value_min/max (opt)   │       │ description (opt)│
│ formula (opt)   │       │ scope (general/combat/│       │ (pas de niveau) │
│ dice_notation   │       │   out_of_combat)      │       └────────┬────────┘
└─────────────────┘       │ dice_num/side (opt)  │                │
                          └──────────────────────┘                │
                                                                  │
                        ┌──────────────────────┐                   │
                        │ effect_usage         │                   │
                        ├──────────────────────┤                   │
                        │ entity_type (spell,  │                   │
                        │   item, consumable…) │                   │
                        │ entity_id            │                   │
                        │ effect_id             │───────────────────┘
                        │ level_min             │  ← tranche de niveau
                        │ level_max             │    (et donc degré utilisé)
                        └──────────────────────┘
```

### 3.3 Niveau : où le mettre ?

- **Sur effect** : level_min, level_max. “Cet effet s’applique pour le niveau 1–5 du sort/objet.” Utile si le même “bloc” (même liste de sous-effets) est utilisé pour une tranche de niveau.
- **Sur effect_usage** : level_min, level_max. “Ce sort utilise l’effet #42 quand le sort est niveau 1–5, et l’effet #43 quand le sort est niveau 6–10.” Permet de lier plusieurs effets à une même entité pour des niveaux différents.

**Décision** : le niveau reste **uniquement sur effect_usage**. L’effet n’a pas besoin de connaître le niveau de l’entité qui l’utilise ; c’est l’usage qui définit “pour la tranche [1–5] j’utilise cet effet, pour [6–10] cet autre”. Les effects restent réutilisables et indépendants du niveau.

---

## 4. Degrés de puissance (sorts : 3 à 5 degrés)

Les sorts ont en général **3 à 5 degrés de puissance**. À chaque degré, des sous-effets peuvent être ajoutés, retirés ou améliorés. On veut pouvoir :

- Créer l’effet “degré 1” (ex. #effect42-d1) pour la tranche de niveau [1–5].
- Créer le “degré 2” (ex. #effect42-d2) pour [6–10] en **dupliquant** l’effet degré 1, en lui donnant le degré 2 et en gardant le même nom (ou un libellé de groupe).
- Identifier clairement que #effect42-d1 et #effect42-d2 font partie du **même groupe d’effets** (même sort, même “effet” logique à des degrés différents).

### 4.1 Côté données : groupe d’effets

Pour ne pas dépendre uniquement du nom, on ajoute une **notion de groupe** en base :

- **Option simple** : sur la table **effects**, colonnes optionnelles **effect_group_id** (FK vers une table `effect_groups` ou self-ref vers `effects`) et **degree** (1, 2, 3…). Tous les effects d’un même “groupe” partagent le même effect_group_id (ou le même parent) et ont un degree différent.
- **Table effect_groups** (optionnelle) : id, name/slug. Chaque effect a effect_group_id + degree. Ainsi “Flèche explosive” degré 1 et degré 2 sont deux effects avec le même effect_group_id et degree 1 et 2.

L’**effect_usage** ne change pas : on définit toujours “niveau [1–5] → effect_id 42, niveau [6–10] → effect_id 43”. C’est bien l’**usage** qui dit quelle tranche de niveau correspond à quel degré (quel effect). Les effects 42 et 43 sont simplement marqués comme appartenant au même groupe (effect_group_id) avec degree 1 et 2.

### 4.2 Côté UI et utilisation

- **Création** : on crée l’effet du degré 1 (sous-effets, paramètres). Puis “Ajouter un degré” = **dupliquer** cet effet : nouveau record effect avec le même nom (ou libellé de groupe), effect_group_id identique, degree = 2. On édite le degré 2 (ajout/suppression/amélioration de sous-effets).
- **Lien sort → effets** : sur le sort, on définit les usages : niveau [1–5] → #effect42 (degré 1), niveau [6–10] → #effect43 (degré 2). L’UI peut afficher “Degré 1 (niveau 1–5)” et “Degré 2 (niveau 6–10)” en s’appuyant sur effect_usage (level_min, level_max) et sur le degree de l’effect pointé.

Résumé : **duplication d’effet = nouveau degré** ; **group (effect_group_id + degree)** pour associer les effets entre eux ; **effect_usage** définit quelle tranche de niveau du sort (ou de l’item) utilise quel effect (donc quel degré). L’effet ne porte pas de niveau ; c’est toujours l’usage qui fait le lien niveau ↔ effet.

---

## 5. Synthèse

- **Une seule table effect (conteneur)** pour toutes les entités, avec lien polymorphique **effect_usage** (entity_type, entity_id, effect_id, **level_min, level_max**). Pas de table “spell_effect” ou “item_effect” séparée → interopérabilité.
- **Niveau uniquement sur effect_usage** : l’effet ne connaît pas le niveau de l’entité ; c’est l’usage qui définit “tranche [1–5] → effect #42, tranche [6–10] → effect #43”.
- **Effet = texte + variables + dés** au niveau du **sous-effet** (template_text, variables, ndX).
- **Deux tables (effect + sub_effect)** : effect = conteneur (liste de sous-effets) ; entités référencent **effect** via effect_usage.
- **Degrés de puissance** : effects optionnellement regroupés (effect_group_id + degree). En UI, “ajouter un degré” = dupliquer l’effet dans le même groupe avec degree + 1 ; effect_usage sur le sort définit niveau [1–5] → effect degré 1, niveau [6–10] → effect degré 2, etc.
- **Contexte combat / hors combat** : sur la pivot **effect_sub_effect**, le champ **scope** (`general`, `combat`, `out_of_combat`) indique pour quel contexte s’affiche le sous-effet. En combat : general + combat ; hors combat : general + out_of_combat. UI discrète (petit select, défaut Général).
