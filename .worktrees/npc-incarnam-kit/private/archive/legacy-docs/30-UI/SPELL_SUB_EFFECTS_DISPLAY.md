# Affichage des sous-effets de sort (actions)

## Objectif

Unifier la **forme** des lignes de sous-effet sur :

- les **définitions** d’effets (`effects_definitions` → `SpellEffectsJournal` → `SpellSubEffectRow`) ;
- les **chips** issus de l’API table (`effect_usages_chips` → `SpellEffectChips` → `SpellEffectUsageActionLine`).

La logique métier est centralisée dans :

- `resources/js/Composables/entity/useSpellSubEffectPresentation.js` (modèle unifié, scope, durée) ;
- `resources/js/Pages/Molecules/entity/spell/SpellSubEffectActionPresentation.vue` (rendu).

## Préfixes communs (toutes actions)

1. **Scope** (pivot `scope`)  
   - `combat` → badge `cbt`  
   - `out_of_combat` → badge `hors-cbt`  
   - `general` → rien  

2. **Critique uniquement** (`crit_only`)  
   - Toujours en tête de ligne : icône éclair.  
   - **Large / Compact** (`subEffectLayout` du journal) : texte **« Critique : »** après l’icône.  
   - **Line / Minimal** (chips table, `SpellEffectUsageActionLine`) : icône seule (pas de mot, gain de place).

## Actions (slug `sub_effects.slug`)

| Slug | Rendu (résumé) |
|------|----------------|
| `appliquer-etat` | **Appliquer l'état** : [état, tooltip] — Durée … \| zone |
| `s-appliquer-etat` | **Appliquer l'état** : [état] **à soit-même** — Durée … |
| `autre` | [valeur / texte] (éclair + badge critique si formule critique) — Durée … \| zone |
| `booster` | **Ajout** de [badge] (éclair + critique si besoin) **en** [caractéristique icône + couleur] — Durée … \| zone |
| `déplacer` | **Déplacement** de **X case(s) (Y m)** (1 case = 1,5 m ; une décimale pour les mètres ; formules dés/variables : suffixe « cases » sans conversion) + type (`movement_kind` : déplacement, saut, téléportation, repousse, attirance) — Durée … \| zone |
| `frapper` | **Attaque** : [badge] (éclair + critique) ([élément]) — **Ajout de vol de vie** [badge] si `life_steal_formula` — **Poison :** [durée] \| zone |
| `soigner` | **Soin** : [badge] (éclair + critique) ([élément]) — **Durée :** … \| zone |
| `invoquer` | **Invocation** de [vue texte monstre] |
| `protéger` | **Gain de** [badge] (éclair + critique) **de** [icône + « pts de bouclier »] — Durée … \| zone |
| `retirer` | **Retrait** de [badge] (éclair + critique) **en** [caractéristique] — Durée … \| zone |
| `voler-caracteristiques` | **Vol** de [badge] (éclair + critique) **en** [caractéristique] — Durée … \| zone |

**Zone** : pour le journal, la zone du **degré** est passée en prop (`degree-area`) et répétée en fin de ligne quand elle existe. Les chips réutilisent `area` du palier.

## Layouts côté vues

| Contexte | Composant | `layout` / densité |
|----------|-----------|---------------------|
| Fiche sort large | `SpellEffectsJournal` `sub-effect-layout="large"` | `large` → mot « Critique : » |
| Fiche sort compacte | `sub-effect-layout="compact"` | `compact` → idem |
| Tableau minimal (cartes) | `SpellEffectChips` `layout="minimal"` | `minimal` |
| Tableau chips « default » | `SpellEffectChips` `layout="default"` | `line` |

## Données API (chips)

`SpellTableController::buildEffectUsagesData` enrichit chaque chip avec notamment : `action_slug`, `crit_only`, `scope`, `value_formula`, `value_formula_crit`, `life_steal_formula`, `state_name`, `cells_display`, `teleport`, `duration_formula`, en plus des champs existants (`text`, `area`, `summon_monster`, etc.).

## Définitions (états)

`SpellEffectDefinitionsSerializer` ajoute `spell_state` `{ id, name, icon }` quand `spell_state_id` est présent, pour l’affichage état dans le journal.

## Incohérences connues / pistes d’amélioration

1. **« Poison : » sur toute durée d’attaque** : le libellé vient de la spec produit ; pour les dégâts non DOT, un libellé neutre du type **« Durée : »** serait plus exact.  
2. **Vue chips « default »** (`CharacteristicInlineGroup`) : les lignes avec `action_slug` pourraient un jour utiliser le même moteur que `SpellEffectUsageActionLine` pour homogénéiser le mode « ligne tableau dense ».  
3. **États sur chips** : seul `state_name` est exposé (pas d’icône référentiel sans charge async) ; le journal a un objet `spell_state` plus riche.  
4. **Types legacy** `damage` / `heal` / `heal_percent` : mappés côté présentation vers `frapper` / `soigner` si d’anciennes données subsistent.
