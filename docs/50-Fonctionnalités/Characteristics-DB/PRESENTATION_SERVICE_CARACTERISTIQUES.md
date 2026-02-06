# Service de caractéristiques — Présentation et objectifs

Ce document décrit le **but** du service de caractéristiques, les **groupes d’entités**, le principe **même nom / sens différent**, et les **propriétés communes** (face visible) d’une caractéristique. Les propriétés plus spécifiques (bornes, formules, conversion, etc.) sont détaillées dans d’autres documents du dossier.

---

## 1. Objectif du service

Le service de caractéristiques a pour but d’**établir l’ensemble des caractéristiques du JDR dans la base de données** plutôt que de les gérer via un simple fichier JSON ou une config. Cela permet :

- une **interface d’administration** pour modifier les définitions ;
- une **source de vérité unique** pour les entités qui utilisent ces caractéristiques ;
- un **lien clair** entre la définition d’une caractéristique et la propriété (colonne ou champ) de l’entité qui la porte.

Les entités (monstres, objets, sorts, etc.) **utilisent** ces caractéristiques comme propriétés : le service définit *quoi* est une caractéristique (nom, colonne BDD, unité, etc.) et *comment* elle s’applique selon le type d’entité ; les modèles d’entités stockent les **valeurs** (niveau, PA, prix, etc.).

---

## 2. Groupes et types d’entités

Les caractéristiques sont organisées en **trois groupes**, chacun associé à des **types d’entités** précis.

| Groupe   | Types d’entités | Exemples d’usage |
|----------|-----------------|------------------|
| **object**  | item, resource, consumable, panoply | Niveau d’équipement, rareté, prix, poids, bonus (PA, PM, etc.) donnés par l’objet |
| **spell**   | spell, capability | Coût en PA, portée, zone, niveau du sort, etc. |
| **creature**| monster, npc (et **player** à venir) | Niveau, PA par tour, PM, PO, vie, initiative, attributs, etc. |

Une même **clé** de caractéristique (ex. `pa`, `level`) peut exister dans **plusieurs groupes** : chaque groupe a alors sa propre définition (nom affiché, description, bornes, formules) adaptée au contexte.

---

## 3. Même nom, sens différent

Une caractéristique peut porter un **nom commun** (ex. « PA ») tout en ayant un **sens différent** selon le groupe :

| Clé / nom | Groupe object | Groupe creature | Groupe spell |
|-----------|----------------|-----------------|--------------|
| **PA**    | Nombre de PA que l’objet **donne** (bonus d’équipement) | Nombre de PA que la créature **a par tour** | **Coût** en PA pour utiliser le sort |
| **Niveau**| Niveau de l’objet (équipement, ressource) | Niveau de la créature (monstre, PNJ, joueur) | Niveau du sort |
| **Portée**| — | Portée de base de la créature | Portée du sort (cases / distance) |

Ainsi, une **ligne de définition** est toujours associée à un **groupe** (et éventuellement à un type d’entité précis dans ce groupe). La clé technique (ex. `pa_object`, `pa_creature`, `pa_spell`) et la colonne BDD (ex. `pa`) permettent de faire le lien entre la caractéristique et la propriété de l’entité.

---

## 4. Lien définition ↔ propriété de l’entité

Pour qu’une entité puisse **utiliser** une caractéristique facilement, le service fournit :

- une **clé** (identifiant technique) qui correspond au **nom de la colonne** (ou du champ) en base de données pour ce groupe ;
- éventuellement un **alias de colonne** (`db_column`) si le nom de la colonne diffère de la clé (ex. clé `level_creature`, colonne `level`).

Les entités s’appuient sur cette définition pour : afficher le bon libellé, valider les valeurs (bornes), appliquer des formules (calcul, conversion Dofus → Krosmoz), etc.

### 4.1 Surcharge par entité dans un groupe

Dans chaque groupe (creature, object, spell), une définition peut être **commune** (entity = `*`, s’applique à toutes les entités du groupe) ou **spécifique** à une entité (ex. `monster`, `item`). On peut **affiner** les propriétés pour une entité précise sans dupliquer tout le reste : une ligne avec entity = `monster` et seulement la **formule** renseignée hérite des min, max, default_value, etc. de la ligne entity = `*`. Le getter fusionne ainsi base (`*`) et surcharge (entité) : toute propriété non vide de la surcharge l’emporte, les autres viennent de la base. Exemple : formule des points de vie pour toutes les créatures en `*`, formule spécifique pour les monstres en `monster`.

---

## 5. Propriétés communes : la « face visible »

Toutes les caractéristiques partagent un ensemble de propriétés qui constituent leur **face visible** (affichage, identification, unité). Ces propriétés sont portées par la **définition générale** de la caractéristique (table centrale `characteristics`) et peuvent être complétées ou surchargées par groupe/entité (tables `characteristic_creature`, `characteristic_object`, `characteristic_spell`).

| Propriété   | Rôle | Exemple |
|-------------|------|--------|
| **Clé**     | Identifiant technique ; correspond au nom de la colonne en BDD (ou à un suffixe par groupe : `_object`, `_creature`, `_spell`). | `pa_creature`, `level_object`, `pa_spell` |
| **Nom**     | Libellé affiché à l’utilisateur. | « Points d’action », « Niveau », « Coût en PA » |
| **Nom court** | Libellé abrégé (listes, tableaux, fiches compactes). | « PA », « Niv. », « PA » |
| **Icône**   | Icône associée (clé ou chemin) pour affichage dans l’UI. | `fa-bolt`, `fa-heart` |
| **Unité**   | Unité de mesure affichée (optionnelle). | « points », « cases », « kamas », « % » |
| **Description** | Texte explicatif. Peut **différer** selon que la caractéristique est utilisée pour les **creatures**, les **spells** ou les **objects** (décrit ailleurs : par entité ou par groupe). | « Points d’action par tour. » / « Coût en PA pour lancer le sort. » |

La **description** est la seule propriété commune dont le contenu peut varier selon le contexte (groupe ou type d’entité) ; les autres (clé, nom, nom court, icône, unité) peuvent aussi être surchargés par entité si besoin (selon l’implémentation et les tables de groupe).

---

## 6. Suite de la documentation

- **Types de valeurs et contenu JSON** (Boolean, Liste, String ; valeur fixe / formule / table par caractéristique) : [TYPES_VALEURS_ET_CONTENU_JSON.md](./TYPES_VALEURS_ET_CONTENU_JSON.md).
- **Propriétés spécifiques** (conversion Dofus → Krosmoz, forgemagie, etc.) : à documenter dans un ou plusieurs fichiers dédiés du même dossier.
- **Architecture technique** (tables, **4 sous-services** : Getter, Limit, Formula, Conversion) : [ARCHITECTURE_SOUS_SERVICES.md](./ARCHITECTURE_SOUS_SERVICES.md). Audit code (doublons, obsolète) : [AUDIT_CODE_CARACTERISTIQUES.md](./AUDIT_CODE_CARACTERISTIQUES.md). Syntaxe des formules : `docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md`.
- **Entités métier** qui consomment les caractéristiques : voir `docs/20-Content/21-Entities/` (monstres, objets, sorts, etc.).
