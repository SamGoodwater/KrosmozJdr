# Spécialisations (`specializations`)

## Rôle et description

Les **spécialisations** sont des fiches d’orientation de jeu (ex. tank, soigneur, DPS) modélisées par la table `specializations`. Elles enrichissent le contenu et les listes (sorts, capacités, inventaire, sections, etc.) **sans** être une sous-table des classes (`breeds`) : ce sont **deux entités distinctes** côté base.

## Différence avec les classes (`breeds`)

La spécialisation repose sur un **socle de fiche** proche de la classe (nom, descriptions courtes/longues, `state`, `read_level`, `write_level`, image, médias, soft delete, `created_by`), mais avec **nettement moins de champs métier** qu’une classe : pas d’`official_id`, pas de `dofusdb_id`, pas d’orientations élémentaires (`breed_element_orientations`), pas de champs de progression type `life_dice`, `evolution`, `dofus_version`, `auto_update`, etc. Voir le schéma dans la migration `2025_06_01_100140_entity_specializations_table.php` et le modèle `App\Models\Entity\Specialization`.

## Ce qui n’existe pas en base (à ne pas confondre)

- **Pas de `breed_id` ni `class_id`** sur `specializations` : aucune clé étrangère ne rattache une spécialisation à une classe. Le rapprochement « jeu » avec une classe se fait au niveau des **NPC** (qui peuvent avoir `breed_id` et/ou `specialization_id`) ou par la conception des données, pas par une FK dédiée sur la fiche spécialisation.
- **Pas de relation directe `creatures` ↔ `specializations`** : une créature n’a pas de `specialization_id`. La spécialisation se porte sur la fiche **NPC** (`npcs.specialization_id`) ou reste une entité autonome pour le contenu.

## Relations principales (Eloquent / pivots)

| Relation | Table pivot / FK | Notes |
|----------|-------------------|--------|
| **Sorts** | `specialization_spell` | N:N, pivot `level` |
| **Capacités** | `capability_specialization` | N:N, pivot `level` |
| **Traits de créature** | `creature_trait_specialization` | N:N, pivot `level` |
| **Consommables** | `consumable_specialization` | N:N, pivots `level`, `quantity` |
| **Ressources** | `resource_specialization` | N:N, pivots `level`, `quantity` |
| **Items** | `item_specialization` | N:N, pivots `level`, `quantity` |
| **Sections** | `section_specialization` | N:N, pivot `level` |
| **NPC** | `npcs.specialization_id` | 1:N (`hasMany`) |

Migrations pivots principales : `2026_05_09_120100_create_specialization_relation_pivots.php`, `2026_05_07_100030_create_creature_trait_specialization_table.php`, `2025_06_01_100440_pivot_capability_specialization_table.php`.

## Lecture et édition (policy)

La policy `SpecializationPolicy` autorise la **lecture** (`view` / `viewAny`) à tous, y compris les invités. La **création**, la **mise à jour** et la **suppression** sont réservées aux **administrateurs** (aligné sur les usages actuels du contrôleur web).

## Routes et API utiles

- **Web (Inertia)** : `routes/entities/specialization.php` — noms `entities.specializations.index`, `show`, `create`, `store`, `edit`, `update`, `updateSpells`, `updateCapabilities`, `updateCreatureTraits`, `updateConsumables`, `updateResources`, `updateItems`, `updateSections`, `delete`, `pdf`. Les chemins fixes (`/create`, etc.) sont déclarés **avant** `GET /{specialization}` pour éviter que `create` soit pris pour un ID.
- **API table** : `GET` `api.tables.specializations` (`SpecializationTableController`).
- **API bulk** : `PATCH` `api.entities.specializations.bulk` (`SpecializationBulkController`).

## Exemples d’utilisation

- Fiche spécialisation avec sorts et capacités liés par niveau.
- NPC associé à une spécialisation pour le fluff ou les règles d’affichage.
- Tableaux d’administration / édition rapide via les endpoints API.

## Liens utiles

- [ENTITY_CLASSES.md](ENTITY_CLASSES.md) — classes (`breeds`)
- [ENTITY_CAPABILITIES.md](ENTITY_CAPABILITIES.md)
- [ENTITY_NPCS.md](ENTITY_NPCS.md)
- [ENTITY_CREATURES.md](ENTITY_CREATURES.md) — pas de lien direct avec les spécialisations
