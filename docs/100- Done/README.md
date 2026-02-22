# 100-Done

Ce dossier est réservé aux **résumés descriptifs** de fonctionnalités livrées ou de décisions d’architecture, lorsqu’ils servent de référence (et non de journal chronologique).

Pour la documentation technique et les bonnes pratiques, voir :

- **Projet** : `docs/00-Project/`
- **Bonnes pratiques** : `docs/10-BestPractices/`
- **Contenu & entités** : `docs/20-Content/`
- **UI & Atomic Design** : `docs/30-UI/`
- **Fonctionnalités** : `docs/50-Fonctionnalités/`

### Livraisons

- **Scrapping (backend)** : [FINALISATION_SCRAPPING_BACKEND.md](./FINALISATION_SCRAPPING_BACKEND.md) — limites depuis config, initiative monster, import range via runMany, robustesse, tests, doc.
- **Scrapping (config)** : [INVENTAIRE_JSON_ET_MIGRATION_BDD_UI.md](../50-Fonctionnalités/Scrapping/INVENTAIRE_JSON_ET_MIGRATION_BDD_UI.md) — inventaire des JSON (rôle, clés utilisées), évaluation transfert BDD + UI admin, plan de migration en 3 phases.
- **Scrapping (mapping BDD + UI)** : migration `scrapping_entity_mappings` / `scrapping_entity_mapping_targets`, modèles, `ScrappingMappingService`, intégration dans `ConfigLoader` (mapping BDD prioritaire, fallback sur le mapping des JSON d'entité si BDD vide), contrôleur admin, page Vue « Mapping scrapping » (liste par entité, CRUD règles + cibles). Lien dans le menu Admin.
- **Admin caractéristiques — Panel Conversion** : select « Fonction de conversion » dans le panneau Conversion (général et par entité), alimenté par `ConversionFunctionRegistry::options()` (création et fiche caractéristique). Option « Aucune » + liste des fonctions enregistrées côté serveur.
