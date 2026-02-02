# Plan d’implémentation — Refonte scrapping V2

Ce document décrit **l’ordre des étapes** pour la refonte du scrapping, l’approche « greenfield » (config-driven, CLI d’abord) et les liens vers la découverte de l’API. Il complète [ETAT_AVANCEMENT_REFONTE_V2.md](./ETAT_AVANCEMENT_REFONTE_V2.md) et [TODOLIST_REFONTE_V2.md](./TODOLIST_REFONTE_V2.md).

---

## 1. Découverte API et configuration

- **Référence API DofusDB** : [DECOUVERTE_API_DOFUSDB.md](./DECOUVERTE_API_DOFUSDB.md) — base URL, pagination (`$limit`, `$skip`), filtres Feathers, structure des réponses, endpoints par entité (breeds, monsters, spells, items, etc.).
- **Configs V2** : `resources/scrapping/v2/sources/dofusdb/` — `source.json`, `entities/*.json`, `collect_aliases.json`. Le service de collecte et le mapping sont pilotés par ces fichiers ; toute nouvelle entité ou nouveau champ passe par la config, pas par du code métier en dur.

---

## 2. Approche greenfield (principes)

| Principe | Application |
|----------|--------------|
| **Config-driven** | Requêtes (endpoints, pagination, filtres) et mapping (source → cible + formatter) dans des JSON. Les formules complexes (level, life, attributs, résistances) sont déléguées à la BDD (`dofusdb_conversion_formulas`, handlers nommés). |
| **CLI d’abord** | Tout testable en ligne de commande : `php artisan scrapping:v2 --collect=monster --id=31 [--convert] [--validate] [--integrate] [--dry-run]`. L’API et l’UI s’appuient sur les mêmes services. |
| **Services indépendants** | Collect → Conversion → Validation → Intégration. Chaque brique a une responsabilité claire ; l’orchestrateur enchaîne sans logique métier DofusDB/KrosmozJDR. |
| **Remplacer, pas dupliquer** | À terme, le pipeline legacy est déprécié ; la bascule se fait entité par entité (ex. monster déjà en V2 pour l’import « Rafraîchir »). |

---

## 3. Ordre des étapes (phases)

### Phase 1 — Aligner la conversion V2 sur les formules / limites BDD

**Objectif** : que le pipeline V2 utilise les caractéristiques et formules stockées en BDD (limites, level, life, attributs, initiative, résistances).

| Étape | Statut | Contenu |
|-------|--------|--------|
| 1.1 | Fait | Passer `entityType` (monster / class / item) dans le contexte de conversion (Orchestrator → ConversionService → FormatterApplicator). |
| 1.2 | Fait | Formatters BDD dans FormatterApplicator : `dofusdb_level`, `dofusdb_life`, `dofusdb_attribute`, `dofusdb_ini` ; `dofusdb_life` utilise `levelPath` pour l’ordre level → life. |
| 1.3 | Fait | Config `monster.json` : level, life, strength, intelligence, agility, chance via formatters BDD. |
| 1.4 | À faire | Config `breed.json` : si level/life/attributs existent côté DofusDB pour les classes, utiliser les formatters BDD avec `entityType: class`. |
| 1.5 | Fait | Tests : OrchestratorTest vérifie level/life convertis par les formules BDD. |

### Phase 2 — Résistances (optionnel en V2)

| Étape | Statut | Contenu |
|-------|--------|--------|
| 2.1 | Fait | Résistances en batch : `resistanceBatch: true` dans la config entité ; ConversionService appelle `convertResistancesBatch()` après le mapping et fusionne `res_*` / `res_fixe_*`. |
| 2.2 | N/A | Champ par champ non retenu ; le batch est utilisé pour monster. |

### Phase 3 — Brancher le pipeline V2 en production

| Étape | Statut | Contenu |
|-------|--------|--------|
| 3.1 | Fait | Route dédiée `POST /api/scrapping/v2/import/{entity}/{id}` (ScrappingV2Controller::importOne). |
| 3.2 | Fait | Import monster en prod : `POST /api/scrapping/import/monster/{id}` utilise collecte legacy (spells/drops) puis **runOneWithRaw** V2 (conversion BDD, validation, intégration) ; relations en cascade via legacy + sync sur la créature. |
| 3.3 | À faire | Dashboard / UI : option ou bascule pour indiquer l’usage du pipeline V2 (ou migrer sans choix utilisateur). |

### Phase 4 — Nettoyage et documentation

| Étape | Statut | Contenu |
|-------|--------|--------|
| 4.1 | Fait | Créer `PLAN_IMPLEMENTATION.md` (ce document). |
| 4.2 | Fait | Documenter l’ordre de résolution des relations (V2) et le comparer à la vision : [RELATIONS_V2.md](./RELATIONS_V2.md). |
| 4.3 | À faire | Quand V2 est la référence pour toutes les entités : déprécier / supprimer l’orchestrateur legacy et le DataConversionService utilisé par l’orchestrateur. |

---

## 4. Ordre de résolution des relations (Phase 4.2)

Voir **[RELATIONS_V2.md](./RELATIONS_V2.md)** : graphe des dépendances (vision), comportement actuel du V2 (pas de résolution des relations dans l’orchestrateur), import monster hybride (V2 + legacy pour sorts/drops + sync), comparaison à la vision et pistes pour une résolution 100 % V2.

---

## 5. Références

- [ETAT_AVANCEMENT_REFONTE_V2.md](./ETAT_AVANCEMENT_REFONTE_V2.md) — État d’avancement et synthèse.
- [TODOLIST_REFONTE_V2.md](./TODOLIST_REFONTE_V2.md) — Tâches détaillées par phase.
- [DECOUVERTE_API_DOFUSDB.md](./DECOUVERTE_API_DOFUSDB.md) — API DofusDB (endpoints, pagination, filtres).
- [VISION_ET_ARCHITECTURE.md](./VISION_ET_ARCHITECTURE.md) — Vision cible, interdépendances, validation.
- [RELATIONS_V2.md](./RELATIONS_V2.md) — Ordre de résolution des relations (V2) et comparaison à la vision.
- [README Refonte](./README.md) — Contenu du dossier et principes.
