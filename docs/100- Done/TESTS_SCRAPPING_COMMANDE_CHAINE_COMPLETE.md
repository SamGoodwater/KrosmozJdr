# Tests chaîne complète de la commande scrapping

**Livré** : réécriture et extension des tests de la commande `php artisan scrapping` pour couvrir toute la chaîne (API → conversion → validation → intégration BDD) pour toutes les entités et les principaux paramètres.

## Contenu

- **ScrappingCommandTest** (`tests/Feature/Scrapping/ScrappingCommandTest.php`) :
  - Utilise `RefreshDatabase` et `CreatesSystemUser` pour les imports réels.
  - **Validation / options** : entité requise, entité inconnue, plusieurs entités (monster, class).
  - **Collecte** : fetchMany (monster), fetchOne par `--id` (monster), `--ids` (item), resource (avec item-types), limit / max-items, start-skip, max-pages.
  - **Sortie** : `--output=raw`, `--output=summary`, `--json` (structure valide).
  - **Simulate** : `--simulate` ne provoque pas d’écriture en base, exit 0.
  - **Chaîne complète par entité** : monster, class (breed), item (resource), spell, panoply — un test par entité qui vérifie l’écriture en BDD (Creature/Monster, Breed, Resource, Spell, Panoply).
  - **Item avec effects** : un test vérifie qu’un item avec tableau `effects` est converti et que `effect` / `bonus` sont renseignés lorsqu’il est intégré en table `items`.
  - **Options** : `--replace-existing` (mise à jour d’un enregistrement existant), `--debug` (exit 0 et sortie contenant "debug").

## Référence

- État d’avancement scrapping : [docs/50-Fonctionnalités/Scrapping/Architecture/ETAT_AVANCEMENT.md](../50-Fonctionnalités/Scrapping/Architecture/ETAT_AVANCEMENT.md).
- Bonnes pratiques tests : [docs/10-BestPractices/TESTING_PRACTICES.md](../10-BestPractices/TESTING_PRACTICES.md).
