# Référence — conversions kref « caractéristique » (Markdown règles)

Ce document liste **toutes** les chaînes que le catalogue `RulesCharacteristicKrefReplacementCatalog` (PHP) transforme en shortcode :

`[[kref:characteristic:<clé>|<libellé affiché>]]`

La commande `php artisan pages:rules-inject-characteristic-krefs` applique ces remplacements **dans les fichiers `.md`** (sauf fichiers exclus ci‑dessous). L’import `pages:import-rules-toc` applique le **même catalogue** via `RulesMarkdownCharacteristicKrefAutowrap` sur le contenu avant rendu HTML.

## Fichiers exclus de l’injection fichier

- `TABLE_DES_MATIERES.md`, `INDEX.md`
- `FORMAT_REGLES.md`, `REFERENCE_CLES_CARACTERISTIQUES.md`, ce fichier
- `AUDIT_REGLES_PUBLICATION.md`, `RECAP.md`, `COHERENCE_SEEDER_REGLES.md`

## Règles d’application

- Les blocs déjà `[[kref:…]]`, les blocs de code ` ``` ` et le code inline `` `...` `` ne sont **pas** modifiés.
- Les libellés sont remplacés par ordre de **longueur décroissante** pour éviter les collisions (ex. « Points d’action (PA) » avant « PA » seul).
- Les abréviations **PA, PM, PO, PV, CA** sont converties seulement lorsqu’elles forment un jeton isolé (pas une sous‑chaîne d’un mot).

## Table — caractéristiques & ressources (référence principale)

| Libellé(s) recherché(s) | Clé | Shortcode exemple |
|-------------------------|-----|---------------------|
| Vitalité | `vitality_creature` | `[[kref:characteristic:vitality_creature|Vitalité]]` |
| Force | `strength_creature` | `[[kref:characteristic:strength_creature|Force]]` |
| Agilité | `agility_creature` | `[[kref:characteristic:agility_creature|Agilité]]` |
| Intelligence | `intelligence_creature` | `[[kref:characteristic:intelligence_creature|Intelligence]]` |
| Sagesse | `wisdom_creature` | `[[kref:characteristic:wisdom_creature|Sagesse]]` |
| Chance | `chance_creature` | `[[kref:characteristic:chance_creature|Chance]]` |
| Points d’action / Points d’action | `action_points_creature` | idem libellé |
| Points d’action (PA) | `action_points_creature` | idem |
| Points de mouvement / Points de Mouvement | `movement_points_creature` | idem |
| Points de vie / Points de Vie | `life_points_creature` | idem |
| Portée (PO) | `range_creature` | idem |
| Classe d’armure / Classe d’Armure (variantes apostrophe) | `armor_class_creature` | idem |
| Classe d’armure (CA) | `armor_class_creature` | idem |
| Nombre d’invocations | `summoning_creature` | idem |
| Réserve de Wakfu | `wakfu_reserve_creature` | idem |
| Bonus de maîtrise | `mastery_bonus_creature` | idem |
| Esquive PA / Esquive PM | `dodge_action_points_creature` / `dodge_movement_points_creature` | idem |
| Tacle | `tackle_creature` | idem |

## Abréviations (libellé court après `|`)

| Jeton | Clé | Shortcode |
|-------|-----|-----------|
| PA | `action_points_creature` | `[[kref:characteristic:action_points_creature|PA]]` |
| PM | `movement_points_creature` | `[[kref:characteristic:movement_points_creature|PM]]` |
| PO | `range_creature` | `[[kref:characteristic:range_creature|PO]]` |
| PV | `life_points_creature` | `[[kref:characteristic:life_points_creature|PV]]` |
| CA | `armor_class_creature` | `[[kref:characteristic:armor_class_creature|CA]]` |

## Compétences (clés `*_creature` — noms français courants dans les règles)

| Libellé | Clé |
|---------|-----|
| Athlétisme | `athletics_creature` |
| Acrobaties / Acrobatie | `acrobatics_creature` |
| Discrétion | `stealth_creature` |
| Escamotage | `sleight_of_hand_creature` |
| Arcanes / Arcane | `arcana_creature` |
| Histoire | `history_creature` |
| Investigation | `investigation_creature` |
| Perception | `perception_creature` |
| Perspicacité | `insight_creature` |
| Médecine | `medicine_creature` |
| Nature | `nature_creature` |
| Religion | `religion_creature` |
| Dressage | `animal_handling_creature` |
| Survie | `survival_creature` |
| Persuasion | `persuasion_creature` |
| Intimidation | `intimidation_creature` |
| Supercherie / Tromperie | `deception_creature` |
| Représentation | `performance_creature` |
| Herbaliste | `nature_creature` (même clé que Nature — sens « compétence Nature » côté JDR) |

## Hors périmètre automatique (à traiter à la main si besoin)

- **Artisanat**, **Connaissance des créatures** : pas de clé `*_creature` unique documentée ici ; ajouter un shortcode manuel si une clé est créée en BDD.
- **Points de bouclier**, **PV temporaires**, **Résistances** détaillées, **Sauvegardes** nommées : voir les fichiers `resistance_*`, `save_*`, `fixed_damage_*` sous `database/seeders/data/characteristic-definitions/creature/` pour des clés précises.
- **Initiative** volontairement **non** injectée sur le seul mot « Initiative » (collisions avec « l’Initiative » narrative) ; utiliser explicitement `[[kref:characteristic:initiative_creature|Initiative]]` si besoin ciblé.

## Mise à jour du catalogue

1. Modifier `app/Support/Cms/RulesCharacteristicKrefReplacementCatalog.php` (`orderedPairs`, `abbreviationPatterns`).
2. Mettre à jour ce tableau Markdown pour rester aligné.
3. Lancer `php artisan pages:rules-inject-characteristic-krefs` puis `php artisan pages:import-rules-toc --force-content` pour propager en CMS.
