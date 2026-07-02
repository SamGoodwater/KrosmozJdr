# Référence — clés caractéristiques (alignement outil / BDD)

Ce document aligne les **noms lisibles** du livre de règles sur les **`characteristic.key`** utilisés dans la base (`characteristics.key`), les définitions JSON sous `database/seeders/data/characteristic-definitions/`, et les **mentions riches** (`@…`) côté éditeur (payload `characteristic` → `{ "key": "<id>" }` où `<id>` est la clé ci‑dessous).

> **Source de vérité** : fichiers `*-creature-definition.json`, `*-object-definition.json`, etc. En cas d’écart, le JSON prime.

---

## Caractéristiques principales (créature / PJ)

| Nom dans les règles | Clé système |
|---------------------|--------------|
| Vitalité | `vitality_creature` |
| Force | `strength_creature` |
| Agilité | `agility_creature` |
| Intelligence | `intelligence_creature` |
| Sagesse | `wisdom_creature` |
| Chance | `chance_creature` |

---

## Ressources de combat courantes

| Nom dans les règles | Clé système |
|---------------------|--------------|
| Points d’action (PA) | `action_points_creature` |
| Points de mouvement (PM) | `movement_points_creature` |
| Portée (PO) | `range_creature` |
| Initiative | `initiative_creature` |
| Nombre d’invocations | `summoning_creature` |

---

## Santé, défense, dégâts

| Nom dans les règles | Clé système |
|---------------------|--------------|
| Points de vie (PV) | `life_points_creature` |
| Classe d’armure (CA) | `armor_class_creature` |
| Tacle | `tackle_creature` |
| Fuite (esquive « fuite ») | `dodge_creature` |
| Esquive PA | `dodge_action_points_creature` |
| Esquive PM | `dodge_movement_points_creature` |

Les **résistances**, **dommages fixes**, **sauvegardes** et **modificateurs** suivent le même schéma : voir les fichiers `resistance_*`, `fixed_damage_*`, `save_*`, `modifier_*` dans `creature/`.

---

## Progression & jets

| Nom dans les règles | Clé système |
|---------------------|--------------|
| Bonus de maîtrise | `mastery_bonus_creature` |
| Perception | `perception_creature` |
| Réserve de Wakfu | `wakfu_reserve_creature` |

Les compétences (Athlétisme, Discrétion, etc.) ont des clés dédiées (`athletics_creature`, `stealth_creature`, …) et des variantes `*_mastery`, `*_passive` selon les besoins — voir le dossier `creature/`.

---

## Rédaction dans les `.md`

Convention recommandée : **première occurrence** d’une grandeur dans une sous‑section :

> **Vitalité** (`vitality_creature`) — …

Pour générer une référence riche importable vers le CMS :

```markdown
[[kref:characteristic:vitality_creature|Vitalité]]
```

Voir aussi [FORMAT_REGLES.md](FORMAT_REGLES.md), section sur les clés et les liens.

Liste des **conversions automatiques** (libellé → shortcode) pour les fichiers Markdown : [REFERENCE_KREF_CONVERSIONS_CARACTERISTIQUES.md](REFERENCE_KREF_CONVERSIONS_CARACTERISTIQUES.md).

---

## Clés réservées (formules / conversions)

Hors caractéristiques catalogue : `d` (valeur Dofus), `level` (niveau JDR). Utiles dans les champs formule côté données, pas comme stats de fiche.
