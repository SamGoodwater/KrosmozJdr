# Plan d’implémentation — Mapping effets DofusDB → KrosmozJDR

**Contexte** : L’harmonisation des noms des propriétés (BDD, config, seeders) est **en cours**. Ce plan découpe l’implémentation du mapping des effectId DofusDB vers les actions Krosmoz (table, service, UI, conversion par action) en **phases** pour avancer sans bloquer sur l’harmonisation, et pour aligner les nouveaux éléments sur les noms une fois stabilisés.

Références : [PLAN_MAPPING_EFFETS_DOFUSDB_KROSMOZ.md](./PLAN_MAPPING_EFFETS_DOFUSDB_KROSMOZ.md), [CARACTERISTIQUES_EFFETS_PAR_ACTION.md](./CARACTERISTIQUES_EFFETS_PAR_ACTION.md).

---

## Principe

- **Ne pas figer** de noms de colonnes ou de clés qui entrent en conflit avec l’harmonisation en cours.
- **Documenter** les noms cibles (conventions) pour que la table et les configs soient cohérentes avec le reste du projet une fois l’harmonisation terminée.
- **Phaser** : faire d’abord ce qui ne dépend pas des noms des caractéristiques (table mapping, service BDD + fallback), puis seeder/UI/conversion quand les noms sont stabilisés ou en utilisant des clés documentées à mettre à jour après coup.

---

## Phase 0 — Harmonisation (en cours, hors scope ce plan)

- Harmoniser les noms : **tables**, **colonnes**, **config** (characteristics, dofusdb_conversion, etc.), **seeders**, **characteristic_key** dans tout le projet.
- **À faire de votre côté** : stabiliser au moins les **characteristic_key** utilisées pour les sorts / créatures (groupes spell, creature) et les **noms de colonnes** des tables liées aux effets (effects, effect_sub_effect, characteristics, dofusdb_conversion_formulas, etc.).
- **Sortie** : une convention claire (ex. snake_case partout, suffixe `_key` pour les clés métier, etc.) et les listes à jour dans [DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md](../Characteristics-DB/DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md) / [CARACTERISTIQUES_EFFETS_PAR_ACTION.md](./CARACTERISTIQUES_EFFETS_PAR_ACTION.md).

---

## Phase 1 — Table + modèle + service de résolution (peu dépendant des noms harmonisés)

Objectif : avoir une **source de vérité en BDD** pour le mapping effectId → action, lue par le service de conversion, avec **fallback sur la constante PHP** actuelle.

### 1.1 Conventions de nommage à respecter

- **Table** : `dofusdb_effect_mappings` (snake_case, préfixe pour clarté).
- **Colonnes** : snake_case ; nommer pour rester cohérent avec le reste du projet après harmonisation (ex. `dofusdb_effect_id`, `sub_effect_slug`, `characteristic_source`, `characteristic_key`).
- **characteristic_key** : chaîne libre ; à remplir avec les **clés finales** une fois l’harmonisation terminée (ou les mettre à jour en masse après).

### 1.2 Tâches

| # | Tâche | Fichiers / actions | Dépendance harmonisation |
|---|--------|---------------------|---------------------------|
| 1.1 | Créer la migration `dofusdb_effect_mappings` | `database/migrations/xxxx_create_dofusdb_effect_mappings_table.php` | Aucune (noms de colonnes alignés conventions projet). |
| 1.2 | Créer le modèle Eloquent `DofusdbEffectMapping` | `app/Models/DofusdbEffectMapping.php` (ou dans `App\Models\Scrapping` si vous regroupez) | Aucune. |
| 1.3 | Service de résolution : lire BDD + cache + fallback constante | Nouveau service (ex. `DofusdbEffectMappingService`) ou évolution de `DofusDbEffectMapping` : `getSubEffectForEffectId(int $effectId): ?array` lit la table (avec cache Laravel ou cache applicatif), si rien → `DofusDbEffectMapping::getSubEffectForEffectId()` (constante actuelle) | Aucune. |
| 1.4 | Brancher le service dans `SpellEffectsConversionService` | Injecter le service de résolution ; appeler le service au lieu de `DofusDbEffectMapping::getSubEffectForEffectId()` (tout en gardant `DofusDbEffectMapping::elementIdToCharacteristicKey()` et `SUB_EFFECT_SLUG_OTHER`) | Aucune. |

**Livrable Phase 1** : un import de sort utilise la table si des lignes existent, sinon le comportement actuel (constante + « autre »). Pas encore d’UI ni de données obligatoires.

### 1.3 Structure de table proposée

```text
dofusdb_effect_mappings
  id (bigint, PK, auto)
  dofusdb_effect_id (int, unique, not null)  -- effectId DofusDB
  sub_effect_slug (string, not null)         -- slug du sous-effet Krosmoz (frapper, soigner, autre, …)
  characteristic_source (string, not null)   -- 'element' | 'characteristic' | 'none'
  characteristic_key (string, nullable)      -- clé Krosmoz si source = characteristic
  created_at / updated_at (timestamps)
```

Index unique sur `dofusdb_effect_id`.

---

## Phase 2 — Données initiales + UI admin (après ou en fin d’harmonisation) ✅

Objectif : **alimenter** la table (mappings connus) et permettre d’**éditer** les mappings en admin.

**Implémenté** : Seeder `DofusdbEffectMappingSeeder`, routes `admin.dofusdb-effect-mappings.*`, contrôleur `Admin\DofusdbEffectMappingController`, page Vue `Admin/dofusdb-effect-mappings/Index.vue`, lien depuis la page Scrapping « Mapping effets DofusDB ». Cache du service invalidé après store/update/destroy.

**Remplissage de la base depuis l’API** : la commande `php artisan scrapping:effects:map` (alias legacy : `dofusdb:fetch-effect-mappings`) interroge l’API DofusDB (`GET /effects`), déduit pour chaque effectId un mapping vers un sous-effet Krosmoz (frapper, soigner, déplacer, booster, etc.), remplit **characteristic_key** quand l’effet a une caractéristique DofusDB connue (voir tableau id → clé dans la commande), et écrit le résultat dans `database/seeders/data/dofusdb_effect_mappings_suggested.php`. Chaque ligne du fichier est commentée avec la description FR de l’effet et l’id carac. DofusDB si pertinent.

**Que faire avec le fichier généré ?**
1. **Lancer le seeder** : `php artisan db:seed --class=DofusdbEffectMappingSeeder` → la table `dofusdb_effect_mappings` est remplie avec les entrées du fichier (ou les 5 en dur si le fichier n’existe pas).
2. **Corriger si besoin** : via l’admin **Mapping effets DofusDB** (lien sur la page Scrapping), tu peux modifier un sous-effet, une source ou une characteristic_key. Les clés sont en format court (ex. `pa`, `strong`, `po`) ; si ta BDD utilise un suffixe `_spell` ou `_creature`, tu peux ajuster en admin ou en éditant le fichier avant de relancer le seeder.
3. **Regénérer** : pour repartir de l’API (après mise à jour du jeu de données DofusDB ou des heuristiques), relancer `scrapping:effects:map --no-cache --output=.../dofusdb_effect_mappings_suggested.php` puis le seeder.

### 2.1 Tâches

| # | Tâche | Fichiers / actions | Dépendance harmonisation |
|---|--------|---------------------|---------------------------|
| 2.1 | Seeder des mappings initiaux | `database/seeders/DofusdbEffectMappingSeeder.php` : insérer les 5 entrées actuelles (96–100 → frapper + element). Utiliser les **characteristic_key** harmonisées si disponibles. | Recommandé : avoir les characteristic_key finales (ex. pour élément : neutre, feu, eau, terre, air). |
| 2.2 | Routes + contrôleur admin | Routes `admin.dofusdb-effect-mappings.index`, `store`, `update`, `destroy` (ou resource) ; contrôleur dédié ou dans un module Scrapping/Admin. | Faible. |
| 2.3 | Liste des mappings | Page admin : tableau (dofusdb_effect_id, sub_effect_slug, characteristic_source, characteristic_key) ; optionnel : colonne « description DofusDB » (appel catalogue ou cache). | Faible. |
| 2.4 | Formulaire création / édition | Formulaire : choix sous-effet (liste depuis `SubEffect`), characteristic_source (select), characteristic_key (select ou autocomplete depuis caractéristiques groupe spell/creature selon convention). | **Forte** : liste des characteristic_key et des sub_effect slug doit refléter les noms harmonisés. |
| 2.5 | (Optionnel) Bouton « Charger effectId depuis l’API » | Pré-remplir une liste d’effectId non encore mappés (GET /effects ou ids rencontrés en base) pour faciliter l’ajout. | Aucune. |

**Livrable Phase 2** : un admin peut consulter et modifier les mappings sans toucher au code.

### Simulation des effets dans l’interface de scrapping

En prévisualisation (sans import), l’UI affiche désormais pour les **sorts** :

- **Prévisualisation (ID unique)** : bloc « Simulation des effets (aucune création en base) » avec le groupe d’effets, et pour chaque effet : degré, type de cible (direct / piège / glyphe), zone (`area`), liste des sous-effets (slug + paramètres), et badge **Création** ou **Réutilisation (effet #id)** selon que l’effet serait créé ou réutilisé (même `config_signature`).
- **Prévisualisation batch** (recherche + « Prévisualiser la sélection ») : pour chaque sort, une colonne « Effets (simul.) » indique le résumé (nombre d’effets, créations / réutilisations).

Aucun objet n’est créé en base tant que l’utilisateur ne lance pas l’import. Backend : `IntegrationService::simulateSpellEffects()`, réponses `preview` et `preview/batch` enrichies avec `spell_effects_simulation`.

---

## Phase 3 — Conversion par action (après harmonisation)

Objectif : faire en sorte que la **valeur** convertie (value_formula, etc.) s’appuie sur les **règles par action** décrites dans [CARACTERISTIQUES_EFFETS_PAR_ACTION.md](./CARACTERISTIQUES_EFFETS_PAR_ACTION.md) (1 règle dommages/soin/bouclier vs par caractéristique).

**Plan d’implémentation détaillé** : [PLAN_IMPLEMENTATION_PHASE3_CONVERSION_VALEURS_EFFETS.md](./PLAN_IMPLEMENTATION_PHASE3_CONVERSION_VALEURS_EFFETS.md) (étapes 3.1 à 3.7, fichiers, tests, décisions produit).

### 3.1 Tâches

| # | Tâche | Fichiers / actions | Dépendance harmonisation |
|---|--------|---------------------|---------------------------|
| 3.1 | Déterminer la formule à appliquer selon (action, characteristic_key) | Dans le service de conversion des effets (ou dans le service de caractéristiques) : selon sub_effect_slug, soit 1 règle (frapper → formule dommages, soigner → soin, etc.), soit formule par characteristic_key (booster, retirer, voler). | **Forte** : les characteristic_key et les noms de config (formules, limites) doivent être stabilisés. |
| 3.2 | Appeler le service de formules (DofusDbConversionFormulaService / CharacteristicService) | Pour les actions « par caractéristique », résoudre la formule de conversion depuis la BDD (ou config) via la characteristic_key. | **Forte**. |
| 3.3 | Appliquer la conversion à la valeur brute (diceNum/diceSide/value) | Remplacer ou compléter `buildValueFormula()` / `buildParams()` pour utiliser la formule résolue quand une conversion est définie. | **Forte**. |

**Livrable Phase 3** : les valeurs des sous-effets (frapper, soigner, booster, retirer, etc.) sont converties selon les règles documentées et les caractéristiques harmonisées.

---

## Synthèse des dépendances à l’harmonisation

| Phase | Dépendance | Comment avancer pendant l’harmonisation |
|-------|------------|----------------------------------------|
| **Phase 1** | Faible | Faire tout de suite : migration, modèle, service BDD + fallback, branchement dans SpellEffectsConversionService. Utiliser des noms de colonnes déjà cohérents (snake_case). |
| **Phase 2** | Seeder : moyenne. UI : liste faible, formulaire forte (clés/options). | Migration/Modèle/Service faits en Phase 1. Seeder : mettre des placeholders ou les 5 entrées avec des clés à mettre à jour après. UI liste possible tôt ; formulaire (select characteristic_key, sub_effect) à finaliser quand les listes sont stables. |
| **Phase 3** | Forte | Démarrer une fois les characteristic_key et les configs de formules (dofusdb_conversion, characteristics) harmonisées. Documenter l’appel au service de formules pour ne pas oublier. |

---

## Ordre recommandé

1. **Maintenant (pendant harmonisation)**  
   - Phase 1 : migration, modèle, service de résolution (BDD + cache + fallback), branchement dans la conversion.  
   - Mettre à jour ce plan ou [PLAN_MAPPING_EFFETS_DOFUSDB_KROSMOZ.md](./PLAN_MAPPING_EFFETS_DOFUSDB_KROSMOZ.md) avec les noms de colonnes et conventions retenus.

2. **Dès que les noms sont stabilisés (ou en parallèle sur des zones déjà fixées)**  
   - Phase 2 : seeder (avec les clés finales), routes admin, liste des mappings, formulaire création/édition (en utilisant les characteristic_key et sub_effect slugs harmonisés).

3. **Après harmonisation complète (config + characteristic_key + formules)**  
   - Phase 3 : résolution de la formule par action/caractéristique, intégration dans la conversion des effets (buildParams / value_formula).

---

## Fichiers à créer ou modifier (résumé)

| Fichier | Phase |
|---------|--------|
| `database/migrations/xxxx_create_dofusdb_effect_mappings_table.php` | 1 |
| `app/Models/DofusdbEffectMapping.php` | 1 |
| Service de résolution (ex. `App\Services\Scrapping\DofusdbEffectMappingService` ou évolution de `DofusDbEffectMapping`) | 1 |
| `SpellEffectsConversionService` (injection + appel service) | 1 |
| `database/seeders/DofusdbEffectMappingSeeder.php` | 2 |
| Routes + contrôleur admin mapping effets | 2 |
| Vues admin (liste + formulaire) | 2 |
| Logique conversion par action (formules) + appel CharacteristicService / DofusDbConversionFormulaService | 3 |

---

## Références

- [PLAN_MAPPING_EFFETS_DOFUSDB_KROSMOZ.md](./PLAN_MAPPING_EFFETS_DOFUSDB_KROSMOZ.md)
- [CARACTERISTIQUES_EFFETS_PAR_ACTION.md](./CARACTERISTIQUES_EFFETS_PAR_ACTION.md)
- [DOFUSDB_EFFECTS_CONVERSION.md](./DOFUSDB_EFFECTS_CONVERSION.md)
- [NAMING_CONVENTIONS.md](../10-BestPractices/NAMING_CONVENTIONS.md) (pour aligner les noms avec le projet)
