# Spécialisations & classes — Phase G (release 1.3.2)

**Objectif** : bibliothèque CMS avec sous-pages par fiche, variantes de sorts classe, affichage spécialisation par niveau, retrait des champs legacy.

**Voir aussi** : [CHECKLIST release 1.3.2](../110-%20To%20Do/CHECKLIST-release-1.3.2.md)

## Livrables

| Thème | Détail |
| --- | --- |
| **Menu bibliothèque** | `BibliothequeEntityPageService` + seeder + `pages:sync-bibliotheque-entities` : sous-pages enfants de `bibliotheque-breed` / `bibliotheque-specialization` avec `settings.linked_entity`. |
| **Affichage CMS** | `PageController::renderLinkedEntityPage` → `LinkedEntityShow.vue` (fiche entité + sections CMS optionnelles). |
| **Variantes sorts** | Max **4** sorts par emplacement (`BreedSpellSlotsEditor`, `UpdateBreedSpellsRequest`) ; même sort dans plusieurs emplacements autorisé. |
| **Spécialisation** | `SpecializationRelationsByLevel` : blocs sorts / capacités / équipements / ressources / consommables par niveau + « sans niveau ». |
| **Champs classe legacy** | `evolution`, `specificity`, `life_dice` masqués des descriptors (contenu via sections). |
| **Sections liées (DRY)** | Trait `HasLeveledSections`, services `EntityLeveledSectionsService` + `LegacyEntitySectionImportService`, requête `UpdateEntityLeveledSectionsRequest`, traits contrôleur `SyncsLeveledEntitySections` / `ProvidesAvailableEntitySections` — communs **breed** (`section_breed`) et **specialization** (`section_specialization`). |
| **BreedSeeder** | Phase `project:init` **5c** (après scrapping) : HTML optionnel `database/seeders/data/legacy-breeds/{slug}.html`, sinon sections depuis colonnes + kref capacités/caractéristiques. `--skip-breeds` pour ignorer. |
| **Legacy HTML** | `SpecializationSeeder` : import sections + kref capacités ; sync menu en fin de seeder. |

## Commandes

```bash
`pages:sync-bibliotheque-entities` est appelée automatiquement par `project:init`, `project:update` / `project:data sync`, `SpecializationSeeder`, `BibliothequeEntityPagesSeeder` et `DatabaseSeeder`.

```bash
php artisan pages:sync-bibliotheque-entities
php artisan db:seed --class=BibliothequeEntityPagesSeeder
```
```

`project:init` enchaîne le seeder après le scrapping (phase 5b).

## Recette

1. Menu **Bibliothèques → Classes** : sous-menu par classe ; page CMS = fiche `BreedViewFull`.
2. Édition classe : ajouter jusqu’à 4 sorts par emplacement de variante.
3. **Spécialisations** : fiche show avec tableaux par niveau.
4. Vérifier qu’`evolution` / `specificity` / `dé de vie` n’apparaissent plus en édition classe.
