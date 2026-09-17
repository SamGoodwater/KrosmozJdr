# Formules créature stables (base + objets)

## Portée

Les définitions **creature** dont la formule est stockée en BDD (`characteristic_creature.formula`) décrivent une valeur **stable** pour la fiche : **stats de base + bonus d’équipement agrégés**.

- **Inclus** : colonnes créature, caractéristiques `_creature`, bonus d’objets exposés comme **`[clé_courte_agrégée_object]`** (ex. `[acrobatics_object]`, `[save_wisdom_object]`, `[life_points_max_object]`).
- **Exclu du calcul stable** : les effets **`_spell`** (temporaires, combat, buffs) — ils relèvent d’une couche dédiée, pas des formules `_creature` de référence.

## Runtime

1. `CreatureVariableMapBuilder` construit la carte à partir des colonnes / `characteristic_creature` (clés = `characteristic.key`).
2. `CreatureItemBonusAggregator` somme les bonus JSON des objets (clés courtes).
3. `CreatureObjectBonusToCreatureVariables::mergeInto` :
   - assigne **`{clé_courte}_object`** = total bonus équipement pour cette clé ;
   - fusionne en parallèle vers la cible stable (caractéristique avec `db_column`, ou variables bonus compétences françaises pour rétrocompatibilité).
4. `FormulaVariableResolver::withShortNames('creature', …)` ajoute les noms courts (`[level]` pour `[level_creature]`, etc.). **Ne pas** appeler `withShortNames('object')` sur tout le map : les alias courts entreraient en conflit entre créature et objet.

## Convention de formule (compétences & sauvegardes)

- **Maîtrise** : paliers `0 / 1 / 2` via **`[…_mastery_creature] * [mastery_bonus_creature]`** (pas de alias français silencieux).
- **Bonus équipement** : **`[…_object]`** aligné sur la clé courte d’agrégation (ex. `acrobatics` → `[acrobatics_object]`).
- **Sauvegardes** : conserver les bonus persistés **`[save_*_bonus]`** (feuille de perso) **+** **`[save_*_object]`** (objets).

## Évolution prévue : bonus « autre » et temporaires (non implémenté)

Quand le besoin apparaîtra (PNJ jouables, fiches joueur, buffs de combat, bonus MJ non liés à un sort), on **ne dupliquera pas** des placeholders `[…_other]` / `[…_temp]` dans toutes les formules seed. Le principe retenu :

### Couches

| Couche | Rôle | Où ça vit (vision) |
|--------|------|---------------------|
| **Stable** | Base créature + équipement + formules `_creature` actuelles | `CreatureRuntimeStatsService` (existant) |
| **Autre** | Modificateurs persistants mais hors « équipement » (dons longue durée, règles maison, traits non modélisés ailleurs) | À stocker (table ou JSON créature / entité joueur) sous forme de **deltas** |
| **Temporaire** | Buffs de durée courte, combat, consommables, sorts actifs | Même mécanisme de **deltas**, avec métadonnées (expiration, source, rencontre) |
| **Sort / effet spell** | Déjà hors calcul stable `_creature` | Couche combat / effets sorts (existant ou à brancher) |

### Deltas par clé

- Les ajustements « autre » et « temporaire » s’expriment comme des **sommes par `characteristic.key`** (ex. `+2` sur `action_points_creature`, `+1` sur `acrobatics_creature`), pas comme une colonne dédiée par stat en BDD.
- **Valeur effective** affichée ou utilisée pour un jet :  
  `effective = stable + Σ(autre pour cette clé) + Σ(temporaire pour cette clé)`  
  (et, le cas échéant, + couche sorts selon les règles du moment).

### Point d’extension applicatif (vision)

- Conserver le service de **résolution stable** tel quel.
- Ajouter plus tard un **composeur d’état effectif** (nom indicatif : agrégation stable + couches optionnelles) consommé par l’API fiche / combat.
- Les formules `_creature` en seed restent la **décomposition stable** ; le **détail** « d’où vient le +2 » pourra être exposé côté API/UI via un **breakdown** (stable / autre / temporaire) sans obliger chaque JSON à référencer tous les types de bonus.

Cette section fixe l’intention pour faciliter l’ajout de fonctionnalités sans refonte des définitions `_creature` déjà normalisées.

## Validation

La commande Artisan vérifie que chaque `[id]` utilisé dans les seed JSON creature est reconnu (liste blanche : caractéristiques, colonnes `creatures`, alias courts, variables bonus français, `d` pour conversions) :

```bash
php artisan characteristics:validate-creature-formula-placeholders
```

## Voir aussi

- [PROPOSITIONS_FORMULES_ET_PROPRIETES.md](./PROPOSITIONS_FORMULES_ET_PROPRIETES.md) — propositions générales formules / objets.
- [SYNTAXE_FORMULES_CARACTERISTIQUES.md](../../10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md) — syntaxe moteur.
