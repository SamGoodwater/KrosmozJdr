# Refonte scrapping — Vision et architecture cible

Ce document résume la vision de la refonte, les points importants, l’architecture cible et les questions / difficultés anticipées. Il complète l’[audit (AUDIT_ETAT_DES_LIEUX.md)](./AUDIT_ETAT_DES_LIEUX.md).

---

## 1. Problèmes à adresser (synthèse)

### 1.1 Écart de structure DofusDB ↔ KrosmozJDR

| Problème | Détail |
|----------|--------|
| **Structures différentes** | L’API DofusDB et le modèle KrosmozJDR ne partagent pas la même structure de données. Les liens entre « objet DofusDB » et « entité KrosmozJDR » ne sont pas évidents. |
| **Propriétés inutiles** | DofusDB expose beaucoup de propriétés qui ne servent pas à KrosmozJDR. |
| **Propriétés manquantes** | KrosmozJDR a des propriétés que DofusDB n’expose pas (à dériver, à laisser vides ou à définir par convention). |
| **Valeurs à convertir** | Les données DofusDB doivent être transformées pour être exploitables (ex. **niveau divisé par 10**). Il faut donc, pour chaque propriété mappée, pouvoir attacher une **fonction de conversion**. |

Conséquence : il faut un **tableau de mapping explicite** qui indique, par entité et par propriété :
- quelle propriété DofusDB → quelle propriété KrosmozJDR ;
- quelle **fonction de conversion** appliquer (optionnel).

### 1.2 Hiérarchie DofusDB : superTypes et types

| Entité DofusDB | Rôle des superTypes / types | Exemple |
|----------------|-----------------------------|---------|
| **Items** | **SuperTypes** : Ressources (9), Consommables (6), Équipements (reste). **Types** : pour ressources et consommables (ex. potion, pain). Pour les équipements, DofusDB ne parle qu’en **superTypes** (arc, pelle, hache, etc.). | superType consumable → types potion, pain ; superType equipment → superTypes arc, pelle. |
| **Monstres** | **Races** jouent le rôle de « types ». | raceId → monster_race en KrosmozJDR. |
| **Sorts** | **Types** de sorts. | typeId → spell_type. |
| **Classes** | Pas de types. | breeds sans sous-catégorie. |

Il existe un **travail manuel de mapping** superTypes/types DofusDB → entités/types KrosmozJDR (ex. quels typeId vont en `resource_types`, `consumable_types`, `item_types`). Ce mapping doit vivre dans la **configuration**, pas en dur dans le code.

### 1.3 Pagination et filtres API DofusDB

| Contrainte | Réponse |
|------------|--------|
| **Limite par page** | Les requêtes sont limitées (souvent **50 ou 100** objets par appel). Le service de **collecte** doit gérer la pagination (`$limit`, `$skip`) et utiliser le **limit effectif** renvoyé par l’API (cap côté DofusDB). |
| **Filtres disponibles** | Il faut **documenter et exploiter** les filtres que l’API DofusDB autorise (ex. `name[$search]`, `typeId`, `id[$gte]`, `raceId`, etc.) pour cibler les données et réduire le nombre d’appels. |

La **configuration** doit décrire quelles requêtes faire pour quel type d’objet (endpoint, paramètres, pagination), afin que le service de collecte soit **piloté par la config** et non par du code métier dispersé.

---

## 2. Vision cible : approche 100 % config-driven

### 2.1 Principes

1. **Fichiers de configuration** comme source de vérité pour :
   - **Quelle requête faire** pour obtenir quel objet (collecte).
   - **Quel champ DofusDB** → quel champ KrosmozJDR, et **quelle fonction de conversion** (mapping + formatters).
2. **Orchestrateur** : seul responsable d’enchaîner Collect → Conversion → (Validation) → Intégration / simulation, sans logique métier DofusDB ou KrosmozJDR en dur.
3. **Services indépendants** :
   - **Collecte** : exécute les requêtes décrites en config (pagination, filtres).
   - **Conversion** : applique le mapping + formatters ; **réutilisable hors scrapping** (usage futur ailleurs).
   - **Intégration** : crée/met à jour les objets en base (ou simule) selon les paramètres donnés par l’orchestrateur.
4. **CLI d’abord** : tout doit être faisable et testable en **ligne de commande**. L’UI pourra s’appuyer sur les mêmes commandes / services plus tard.

### 2.2 Tableau de mapping (exigence centrale)

Il faut **un tableau de mapping** qui précise, pour chaque entité DofusDB ciblée :

- **Quelle propriété DofusDB** (chemin/source) → **quelle propriété KrosmozJDR** (modèle/champ ou structure cible).
- **Quelle fonction de conversion** appliquer (optionnel) : ex. `level / 10`, `pickLang`, `clampInt`, etc.

Les fonctions de conversion sont **déclarées** (registry) et **réutilisables** ; la config ne fait que les référencer par nom et arguments. Cela rejoint l’existant (formatters dans `resources/scrapping/formatters/registry.json` + mapping dans les JSON d’entités), à unifier et à clarifier dans la refonte.

### 2.3 Rôle de l’orchestrateur

- Reçoit les **paramètres** (entité, IDs ou filtres, options : simulation, force update, etc.).
- Appelle le **service de collecte** (config → requêtes).
- Passe les données brutes au **service de conversion** (config → mapping + formatters).
- Selon les options : envoie les données converties à un **éventuel validateur** (voir § 3), puis au **service d’intégration**.
- Le service d’intégration soit **enregistre en base**, soit **simule** (dry-run), selon les paramètres donnés par l’orchestrateur.

L’orchestrateur **ne contient pas** de logique de conversion ni de règles métier DofusDB/KrosmozJDR ; il ne fait qu’enchaîner les appels.

### 2.4 Interdépendances des objets — À ne pas oublier

**Importer un objet sans ses relations ne sert à rien.** Les entités KrosmozJDR (et DofusDB) sont liées entre elles ; le scrapping doit prendre en compte ces dépendances pour que les données soient exploitables.

#### Graphe des dépendances (exemples)

| Entité | Dépend de / est liée à | Explication |
|--------|------------------------|-------------|
| **Sorts** | **Monstres** | Les sorts peuvent invoquer → lien vers des monstres (invocations). |
| **Classes** | **Sorts** | Les classes possèdent des sorts (spell-levels par breed). |
| **Monstres** | **Ressources, équipements, consommables** | Drops : les monstres donnent des ressources, équipements ou consommables. |
| **Consommables / Équipements** | **Ressources** | Recettes : les consommables et équipements sont constitués de ressources (ingrédients). |

Résumé des flux :
- **Classes** → ont des **sorts**
- **Sorts** → peuvent invoquer des **monstres**
- **Monstres** → ont des **drops** (ressources, équipements, consommables)
- **Équipements / Consommables** → ont des **recettes** (ressources)

#### Implications pour la refonte

1. **Ordre de résolution**  
   Il faut définir un **ordre d’import** (ou un graphe de dépendances) pour que les objets référencés existent avant ceux qui les référencent. Exemple : ressources (et types) avant équipements/consommables ; monstres et sorts avant classes si on résout les liens à l’import.

2. **Résolution des relations**  
   - Les **IDs DofusDB** (ex. `monsterId`, `itemId`, `resourceId`) dans les données brutes doivent être **résolus** vers les entités KrosmozJDR (ex. `monster_id`, `item_id`) soit déjà en base, soit importées dans le même lot.  
   - L’**intégration** (ou un module dédié « résolution de relations ») doit : créer ou retrouver les types (resource_type, item_type, consumable_type, monster_race, spell_type), puis lier les entités (classe ↔ sorts, sort ↔ monstres invoqués, monstre ↔ drops, item ↔ recette).

3. **Option « avec relations »**  
   L’orchestrateur doit pouvoir être invoqué avec une option du type **include_relations** (ou équivalent) :  
   - soit importer **uniquement** l’objet demandé (sans résoudre les liens, ou avec des FK null si les cibles n’existent pas),  
   - soit importer **l’objet + les entités liées** (ex. une classe + ses sorts ; un monstre + les ressources/items des drops), selon une stratégie documentée (profondeur, limites).

4. **Config des relations**  
   La **configuration** doit décrire, par entité, quelles **relations** existent (champ source DofusDB → entité cible KrosmozJDR, type de relation) pour que l’intégration ou l’orchestrateur sache quoi résoudre et dans quel ordre.

En résumé : la refonte doit **expliciter le graphe de dépendances**, **définir l’ordre de résolution** et **gérer la résolution des relations** (création des types, liaison des entités) pour que l’import produise des objets exploitables, pas des enregistrements orphelins ou incohérents.

---

## 3. Validation par rapport aux caractéristiques KrosmozJDR

### 3.1 Fichier des caractéristiques

KrosmozJDR dispose d’un fichier de **caractéristiques** (limites, définitions, règles) : `config/characteristics.php` (et les JSON sous `config/characteristics/`). Il définit notamment :

- **Limites** par type d’entité (ex. life, level, attributes).
- **Définitions** des champs (valid_values, default, etc.).

Une fois les données **converties** (format KrosmozJDR), il faut **vérifier** qu’elles respectent ces définitions (bornes, valeurs autorisées, champs requis).

### 3.2 Qui fait cette vérification ?

Trois options possibles :

| Option | Responsable | Avantages | Inconvénients |
|--------|-------------|-----------|----------------|
| **A. Service de conversion** | Le service de conversion applique mapping + formatters et **en plus** vérifie les limites/définitions (en lisant les caractéristiques). | Tout « DofusDB → KrosmozJDR exploitable » au même endroit. | Conversion + validation mélangées ; le service de conversion dépend des caractéristiques et peut devenir plus lourd. |
| **B. Service d’intégration** | L’intégrateur reçoit des données déjà converties et **vérifie** avant d’écrire (ou simuler). | Séparation nette : conversion = forme ; intégration = persistance + cohérence. | L’intégrateur doit connaître les caractéristiques ; si on refuse des lignes, il faut remonter des erreurs claires. |
| **C. Service de vérification dédié** | Un **service de validation** reçoit les données converties + la référence des caractéristiques et retourne OK / liste d’erreurs. L’orchestrateur appelle Collect → Conversion → **Validation** → Intégration (ou arrêt si erreurs). | Responsabilité unique, testable à part, réutilisable (imports manuels, autres sources). | Un composant de plus à maintenir. |

**Recommandation** : **Option C (service de vérification dédié)**.  
- La **conversion** reste « DofusDB → structure KrosmozJDR » sans dépendre des seuils métier.  
- La **validation** devient explicite : « les données converties respectent-elles les caractéristiques ? ».  
- L’**intégration** ne fait qu’écrire (ou simuler) des données déjà validées.  
- L’orchestrateur enchaîne : Collect → Conversion → Validation → Intégration (ou stop si la validation échoue).

Cela reste un choix d’architecture : on peut aussi commencer par B (validation dans l’intégrateur) et extraire un service de validation plus tard si le besoin se fait sentir.

### 3.3 Comportement en cas d’erreur de validation

À trancher :

- **Rejeter l’objet** et continuer le lot (avec rapport d’erreurs).
- **Arrêter tout le lot** à la première erreur.
- **Corriger automatiquement** (clamp, valeur par défaut) quand la config le permet, et ne rejeter qu’en cas d’impossibilité.

La politique (rejet / arrêt / auto-correction) peut être un paramètre de l’orchestrateur ou de la config.

---

## 4. Architecture cible (schéma)

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         ORCHESTRATEUR (CLI / futur UI)                  │
│  Paramètres : entité, IDs/filtres, options (dry-run, force, etc.)       │
└─────────────────────────────────────────────────────────────────────────┘
     │
     │ 1. Collecte
     ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  SERVICE DE COLLECTE                                                     │
│  - Lit la config « requêtes » (endpoints, pagination, filtres).          │
│  - Exécute les appels HTTP (pagination 50/100, limite effective).        │
│  - Retourne des données brutes DofusDB.                                  │
└─────────────────────────────────────────────────────────────────────────┘
     │
     │ 2. Conversion
     ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  SERVICE DE CONVERSION (réutilisable hors scrapping)                     │
│  - Lit la config « mapping » (propriété source → cible + formatter).      │
│  - Applique les fonctions de conversion (ex. level/10, pickLang).         │
│  - Retourne des structures au format KrosmozJDR.                           │
└─────────────────────────────────────────────────────────────────────────┘
     │
     │ 3. Validation (recommandé : service dédié)
     ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  SERVICE DE VÉRIFICATION (optionnel mais recommandé)                    │
│  - Reçoit les données converties + référence characteristics.             │
│  - Vérifie limites, valeurs autorisées, champs requis.                  │
│  - Retourne OK ou liste d’erreurs.                                        │
└─────────────────────────────────────────────────────────────────────────┘
     │
     │ 4. Intégration (ou simulation)
     ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  SERVICE D’INTÉGRATION                                                   │
│  - Reçoit les données converties (et validées).                         │
│  - Selon paramètres orchestrateur : écrit en base OU simulation.         │
│  - Gère relations, images, etc.                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

**Configs** :

- **Collecte** : quelle requête pour quel objet (URL, méthode, query, pagination, filtres supportés).
- **Conversion** : tableau de mapping (propriété DofusDB → propriété KrosmozJDR + fonction de conversion).
- **Caractéristiques** : déjà existant (`config/characteristics*`) ; utilisé par le service de vérification (et éventuellement par les formatters pour clamp/defaults).

---

## 5. Points importants à garder en tête

### 5.1 Configuration

- **Une config « requêtes »** : par entité (ou par type d’objet), définir les requêtes à faire (endpoint, paramètres, pagination, filtres autorisés). Éviter le code en dur pour les URLs et les paramètres.
- **Une config « mapping »** : par entité, tableau explicite [propriété DofusDB → propriété KrosmozJDR ; formatter optionnel]. Les formatters sont en registry (nom + args) pour réutilisation et testabilité.
- **Mapping superTypes/types** : documenter et configurer le mapping DofusDB (superTypes, types) → KrosmozJDR (resource_types, consumable_types, item_types, etc.) dans des fichiers dédiés (comme l’existant `item-super-types.json`), pas dans le code.

### 5.2 Conversion réutilisable

Le **service de conversion** ne doit pas être « scrapping-only ». Il doit pouvoir être utilisé plus tard pour d’autres imports ou traitements. Donc :

- Entrée : données brutes + **référence de config de mapping** (ou identifiant d’entité).
- Sortie : données au format KrosmozJDR (structure + types).
- Pas de dépendance directe à DofusDB ou au pipeline de scrapping dans ce service.

### 5.3 Pagination et limites

- Toujours utiliser le **limit effectif** renvoyé par l’API (ex. 50) pour avancer en `$skip`, pas une valeur fixe en code.
- Documenter et utiliser les **filtres DofusDB** pour réduire le volume (par typeId, raceId, level, name, etc.) et exposer ces filtres dans la config « requêtes ».

### 5.4 CLI d’abord

- Toutes les opérations (collecte seule, conversion seule, validation, intégration, simulation) doivent être **pilotables en CLI** (une ou plusieurs commandes avec options claires).
- L’UI future pourra appeler les mêmes services ou lancer les mêmes commandes en arrière-plan.

### 5.5 Simulation (dry-run)

- L’orchestrateur transmet un paramètre **simulation / dry-run** à l’intégration.
- L’intégrateur **ne persiste pas** en base dans ce cas ; il peut retourner un résumé (ce qui aurait été créé/modifié) pour faciliter les tests et la confiance.

---

## 6. Questions et difficultés anticipées

### 6.1 Structure des configs

- **Un fichier par entité** (comme aujourd’hui `entities/monster.json`, `entities/item.json`) vs **un gros fichier** : un fichier par entité garde la lisibilité et permet d’étendre une entité sans toucher aux autres. Recommandation : garder un fichier par entité, avec une structure claire (section requêtes, section mapping, section cible KrosmozJDR).
- **Où mettre le mapping superTypes/types ?** Soit dans les mêmes fichiers d’entité (section dédiée), soit dans des fichiers séparés par domaine (items, monsters, spells). L’existant `item-super-types.json` peut servir de base pour les items ; à dupliquer ou factoriser pour consommables/équipements.

### 6.2 Formatters et caractéristiques

- Certains formatters ont besoin de **limites** (ex. clamp level 1–200). Ces limites peuvent venir des **caractéristiques** (pour rester cohérent) ou être en dur dans la config du formatter. Pour garder une seule source de vérité : les limites « métier » dans characteristics ; le service de conversion peut accepter un contexte (entité, type) pour résoudre les bornes via characteristics, ou le **service de validation** applique les limites après conversion (recommandé pour garder la conversion simple).
- **Ordre des formatters** : la config doit définir l’ordre d’application (ex. pickLang puis truncate puis clamp). Déjà le cas dans les JSON actuels (liste ordonnée).

### 6.3 Entités composites (ex. monstre + créature, item + type)

- Une « ligne » DofusDB peut correspondre à **plusieurs tables** KrosmozJDR (ex. monster → creatures + monsters). La config de mapping doit pouvoir exprimer **plusieurs cibles** (plusieurs `to` ou équivalent) par entité. L’existant avec `to: [{ model: "items", field: "..." }]` va dans ce sens ; à généraliser si besoin (plusieurs modèles, relations).
- **Relations** (type_id, monster_race_id, etc.) : soit résolues en amont (registry, catalogues), soit gérées dans l’intégration. À documenter clairement (qui crée la ressource type, qui fait la liaison).

### 6.4 Ordre d’import et résolution des interdépendances

- **Ordre d’import** : définir un ordre (ou un graphe) pour que les dépendances soient créées avant les entités qui les référencent (ex. ressources et types avant équipements/consommables ; types de sorts avant sorts). La config ou l’orchestrateur doit connaître cet ordre.
- **Références manquantes** : si un objet référence un monstre/ressource/sort qui n’est pas encore en base, que faire ? (importer d’abord la cible, ignorer la relation, échouer, ou mettre en file d’attente pour un second passage.)
- **Profondeur** : option « importer avec relations » → jusqu’à quelle profondeur ? (ex. classe + sorts uniquement, ou classe + sorts + monstres invoqués + drops…) Limiter pour éviter des imports en cascade trop larges.
- **Cycles** : sorts ↔ monstres (invocations) crée un cycle ; l’ordre d’import ou un traitement en deux passes (créer d’abord les entités, puis lier) doit être défini.

### 6.5 Gestion des erreurs et rapports

- **Collecte** : timeout, 4xx/5xx, cap inattendu. Faut-il retenter ? Abandonner le lot ? Rapporter par objet ou par page ?
- **Conversion** : champ manquant, formatter en erreur. Rejeter l’objet ? Utiliser une valeur par défaut si configuré ?
- **Validation** : objet hors limites. Rejeter, corriger, ou arrêter le lot ?
- **Intégration** : conflit (doublon), contrainte FK. Comportement (skip, update, fail) ?

Recommandation : définir une **politique d’erreur** par phase (config ou paramètres orchestrateur) et un **rapport** unique en sortie (succès, rejets, erreurs) pour la CLI et la future UI.

### 6.6 Performance et rate limiting

- Beaucoup d’appels HTTP (pagination 50/100) → risque de lenteur et de surcharge côté DofusDB. Prévoir **délai entre requêtes**, **cache** (comme aujourd’hui), et éventuellement **limite de concurrence** si on parallélise plus tard.
- La config peut exposer des options (delay, max pages, max items) pour que l’opérateur CLI puisse borner les coûts.

### 6.7 Rétrocompatibilité et migration

- Pendant la transition, l’ancien système (UI, API Laravel) peut rester en place. La refonte peut d’abord exister en **nouvelle commande** (ex. `php artisan scrapping:v2`) et nouveaux services, sans casser l’existant.
- Une fois la refonte validée en CLI, migration des endpoints et de l’UI vers les nouveaux services, puis suppression de l’ancien code.

---

## 7. Résumé en une page

| Thème | Décision / orientation |
|-------|------------------------|
| **Problème central** | Structure DofusDB ≠ KrosmozJDR ; beaucoup de champs inutiles / manquants ; valeurs à convertir (ex. level/10) ; hiérarchie superTypes/types ; pagination 50/100 et filtres à exploiter. |
| **Approche** | 100 % config-driven : config « requêtes » + config « mapping » (propriété source → cible + formatter). |
| **Orchestrateur** | Enchaîne Collect → Conversion → Validation → Intégration ; pas de logique métier en dur. |
| **Collecte** | Service dédié ; requêtes et pagination pilotées par config ; utilisation du limit effectif et des filtres DofusDB. |
| **Conversion** | Service dédié, **réutilisable** ; mapping + formatters depuis la config ; sortie au format KrosmozJDR. |
| **Validation** | Recommandation : **service de vérification** dédié, qui vérifie les données converties contre `config/characteristics`. |
| **Intégration** | Reçoit données converties (et validées) ; selon paramètres : **enregistrement en base** ou **simulation** (dry-run). |
| **Mapping** | Tableau explicite par entité : propriété DofusDB → propriété KrosmozJDR + fonction de conversion (registry). |
| **SuperTypes/types** | Mapping manuel documenté et configuré (fichiers dédiés ou sections dans les configs d’entité). |
| **Pagination** | Gérée dans le service de collecte ; limit effectif renvoyé par l’API ; filtres documentés et utilisés. |
| **CLI** | Tout pilotable en ligne de commande en priorité ; UI ensuite si besoin. |
| **Simulation** | Paramètre orchestrateur → intégrateur ne persiste pas, retourne un résumé. |
| **Interdépendances** | **À ne pas oublier** : sorts ↔ monstres (invocations), classes → sorts, monstres → drops (ressources/équipements/consommables), équipements/consommables → recettes (ressources). Ordre de résolution et résolution des relations (types + liaisons) indispensables ; importer sans relations = inexploitable. |

Ce document pourra être complété par un **plan de mise en œuvre** (ordre des tâches, découpage des PR, critères de succès) et par des **exemples de fichiers de config** cibles (requêtes + mapping) pour une ou deux entités pilotes.
