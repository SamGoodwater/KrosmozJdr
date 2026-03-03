# Référence des entrées de mapping (comparaison UI)

Ce document décrit les propriétés utilisées dans le mapping par entité (affichées en **Brut / Converti / Krosmoz**) et les propriétés DofusDB non mappées ou mises en réserve, pour les retrouver rapidement.

- **mapping** (dans le JSON) = propriétés converties **et** affichées dans la comparaison.
- **_mappingUnused** (ex. spell.json) = entrées en réserve (même structure que `mapping` + champ `explanation`). Non utilisées par le backend ; pour réafficher une propriété, copier l’entrée de `_mappingUnused` vers `mapping` (sans le champ explanation).
- **_mappingUnusedDocumentation** (monster.json, item.json) = liste `{ "dofusdbPath", "explanation" }` pour champs DofusDB jamais mappés ; documentation uniquement, ignorée par le backend.

---

## 1. Monster (Créature / Monstre)

| Clé mapping | Chemin DofusDB | Cible Krosmoz | Rôle |
|-------------|----------------|---------------|------|
| dofusdb_id | id | monsters.dofusdb_id | Identifiant source |
| name | name (lang) | creatures.name | Nom |
| description | description (lang) | creatures.description | Description |
| level | grades.0.level | creatures.level | Niveau (calcul dofusdb_level) |
| life | grades.0.lifePoints | creatures.life | PV (calcul avec level) |
| strength | grades.0.strength | creatures.strength | Force (caractéristique) |
| intelligence | grades.0.intelligence | creatures.intelligence | Intelligence |
| agility | grades.0.agility | creatures.agility | Agilité |
| wisdom | grades.0.wisdom | creatures.wisdom | Sagesse |
| chance | grades.0.chance | creatures.chance | Chance |
| pa | grades.0.actionPoints | creatures.pa | Points d’action |
| pm | grades.0.movementPoints | creatures.pm | Points de mouvement |
| po | grades.0.bonusRange | creatures.po | Portée |
| kamas | grades.0.kamas | creatures.kamas | Kamas |
| dodge_pa | grades.0.paDodge | creatures.dodge_pa | Esquive PA |
| dodge_pm | grades.0.pmDodge | creatures.dodge_pm | Esquive PM |
| ini | grades.0.initiative | creatures.ini | Initiative |
| vitality | grades.0.vitality | creatures.vitality | Vitalité |
| image | img | creatures.image | Image (stockage) |
| size | size | monsters.size | Taille (Minuscule → Gigantesque) |
| race | race | monsters.monster_race_id | Race du monstre |
| res_neutre, res_terre, res_feu, res_air, res_eau | grades.0.*Resistance | creatures.res_* | Résistances élémentaires |

### Propriétés DofusDB non mappées (monster)

- **spells** — Liste des sorts invocables (IDs). Géré par les **relations** (import des sorts liés), pas par le mapping.
- **drops** — Liste des drops (itemId, quantity). Géré par les **relations** (import des items liés).
- **grades** (objet complet) — On mappe uniquement des champs précis (grades.0.level, etc.) ; le reste (autres indices, détails) n’est pas utilisé.
- **subareas**, **areas** — Zones géographiques Dofus ; non utilisées dans Krosmoz.
- **useRaceStats** — Option de calcul des stats ; non utilisée.

---

## 2. Spell (Sort)

| Clé mapping | Chemin DofusDB | Cible Krosmoz | Rôle |
|-------------|----------------|---------------|------|
| dofusdb_id | id | spells.dofusdb_id | Identifiant source |
| name | name (lang) | spells.name | Nom |
| description | description (lang) | spells.description | Description |
| image | img | spells.image | Image |
| pa | levels.0.apCost | spells.pa | Coût en PA |
| po | levels.0.range | (affichage) | Portée valeur unique si pas de min/max (utilisée comme po_min/po_max) |
| spell_po_min | levels.0.range.min | spells.po_min | Portée min (0 = soi-même, 1 = cac). En édition : formule possible ([level], etc.) |
| spell_po_max | levels.0.range.max | spells.po_max | Portée max. En édition : formule possible. |
| area | levels.0.effects.0.zoneDescr.shape | (effets) | Zone sur Effect, pas sur Spell |
| level | levels.0.grade | spells.level | Niveau / grade du sort |
| cast_per_turn | levels.0.maxCastPerTurn | spells.cast_per_turn | Lancers par tour |
| cast_per_target | levels.0.maxCastPerTarget | spells.cast_per_target | Lancers par cible |
| sight_line | levels.0.needLineOfSight | spells.sight_line | Ligne de vue |
| element | elementId | spells.element | Élément (caractéristique) |
| category | categoryId | spells.category | Catégorie (caractéristique) |

### Portée (po) : deux valeurs min/max + formules

La portée est stockée en **po_min** et **po_max** (string) pour permettre plages et formules :
- **0** = peut se lancer sur soi-même ; **1-1** = cac (mêlée) ; **2-6** = plage en cases.
- En édition manuelle, chaque borne peut être une **formule** (ex. `[level]`, `[level]*2`) évaluée en jeu.
- Le modèle expose l’attribut calculé **po_display** pour l’affichage "min-max" et l’API.

### Entrées en réserve (_mappingUnused) — à remettre dans `mapping` si besoin

- **number_between_two_cast** — Intervalle entre deux lancers (levels.0.minCastInterval).
- **number_between_two_cast_editable** — Indique si l’intervalle est éditable (levels.0.minCastIntervalEditable).
- **po_editable** — Portée éditable (levels.0.rangeEditable).
- **is_magic** — Sort magique ou non (levels.0.isMagic).
- **powerful** — Indicateur “puissant” (levels.0.powerful).

### Propriétés DofusDB non mappées (spell)

- **levels** (tableau complet) — Seuls certains champs de `levels[0]` sont mappés ; le reste (effets détaillés, autres niveaux) ne l’est pas.
- **effects** — Effets détaillés (zone, dégâts, etc.). Non mappés en V2 (formatter packDofusdbEffects absent).
- **breedId** — Classe associée ; utilisé en filtre, pas en mapping sort.
- **summon** — Invocation (monstre) ; géré par les **relations** si besoin.

---

## 3. Item (Ressource / Consommable / Équipement)

| Clé mapping | Chemin DofusDB | Cible Krosmoz | Rôle |
|-------------|----------------|---------------|------|
| dofusdb_id | id | resources/consumables/items.dofusdb_id | Identifiant source |
| name | name (lang) | name | Nom |
| description | description (lang) | description | Description |
| level | level | level | Niveau |
| price | price | price | Prix |
| image | img | image | Image |
| typeId | typeId | items/resources.type_id, resource_type_id | Type d’objet |
| resource_type_id | typeId | resources.resource_type_id | Type ressource (résolu) |
| weight | realWeight | resources.weight | Poids (ressources) |
| rarity | rarity | rarity | Rareté |
| recipe_ingredients | recipe | resources.recipe_ingredients | Ingrédients de recette |
| effect | effects | items.effect | Effets convertis (bonus Krosmoz) |
| bonus | effects | items.bonus | Effets bruts JSON |

### Propriétés DofusDB non mappées (item)

- **effects** (détail par effet) — Seuls les bonus convertis et le JSON brut sont mappés (effect, bonus) ; pas de mapping champ par champ.
- **recipe** (structure complète) — Mappé en `recipe_ingredients` pour les ressources ; champs secondaires non exposés.
- **itemSetId**, **criteria** — Sets d’objets, critères ; non utilisés.
- **possibleEffects** — Effets possibles (aléatoires) ; non mappés.

---

## Comment modifier la liste affichée

1. **Réduire les propriétés affichées** : déplacer l’entrée concernée de `mapping` vers `_mappingUnused` dans le JSON de l’entité (et ajouter le champ `"explanation"`). La conversion ne sera plus appliquée pour cette propriété tant qu’elle n’est pas dans `mapping`.
2. **Réafficher une propriété** : copier l’objet depuis `_mappingUnused` vers `mapping` (sans le champ `explanation`).
3. **Ajouter une nouvelle propriété** : ajouter une entrée dans `mapping` (key, from, to, formatters) ; elle sera convertie et affichée. S’inspirer des entrées en réserve ou de la doc DofusDB.

Fichiers concernés : `resources/scrapping/config/sources/dofusdb/entities/monster.json`, `spell.json`, `item.json`.
