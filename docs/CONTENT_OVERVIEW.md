# Krosmoz JDR – Présentation et Structure du Contenu

## 1. Introduction

Krosmoz JDR est un jeu de rôle en ligne inspiré de l'univers de Dofus. Le projet vise à offrir une expérience immersive, évolutive et collaborative, en s'appuyant sur une architecture multi-agents et une centralisation des entités métier.

## 2. Univers et Contexte

Plongez dans le monde des Douze, un univers riche et vibrant issu de l'imaginaire de Dofus, où l'aventure, la stratégie et la magie s'entrelacent pour créer une expérience unique de jeu de rôle. Les textes sont inclusifs et multilingues (français, anglais, espagnol).

## 3. Système de Comptes, Rôles et Droits

### 3.1. Rôles

- **guest** : Visiteur non connecté
- **user** : Utilisateur inscrit
- **player** : Joueur participant à une campagne/scénario
- **game_master** : Meneur de jeu
- **admin** : Administrateur
- **super_admin** : Administrateur suprême (unique)

### 3.2. Matrice des privilèges

|                          | guest | user | player | game_master | admin | super_admin |
| -----------------------: | :---: | :--: | :----: | :---------: | :---: | :---------: |
|          Pages de règles |   r   |  r   |   r    |    r&w\*    |  r&w  |     r&w     |
|            contributions |   r   |  r   |   r    |      r      |  r&w  |     r&w     |
|          Page des outils |   r   |  r   |   r    |      r      |  r&w  |     r&w     |
|        Gestion des pages |   -   |  -   |   -    |      -      |  r&w  |     r&w     |
| Gestion des utilisateurs |   -   |  -   |   -    |      -      |  r&w  |     r&w     |
|              Equipements |   r   |  r   |   r    |     r&w     |  r&w  |     r&w     |
|               Ressources |   r   |  r   |   r    |     r&w     |  r&w  |     r&w     |
|                  Classes |   r   |  r   |   r    |     r&w     |  r&w  |     r&w     |
|                 Monstres |   r   |  r   |   r    |     r&w     |  r&w  |     r&w     |
|                    Sorts |   r   |  r   |   r    |     r&w     |  r&w  |     r&w     |
|                Capacités |   r   |  r   |   r    |     r&w     |  r&w  |     r&w     |
|                Aptitudes |   r   |  r   |   r    |     r&w     |  r&w  |     r&w     |
|                    Etats |   r   |  r   |   r    |     r&w     |  r&w  |     r&w     |
|          Spécialisations |   r   |  r   |   r    |     r&w     |  r&w  |     r&w     |
|              Consomables |   r   |  r   |   r    |     r&w     |  r&w  |     r&w     |
|             Espace admin |   -   |  -   |   -    |      -      |  r&w  |     r&w     |
|   Gestion des privilèges |   -   |  -   |   -    |      -      |   r   |     r&w     |

\*Les game_master peuvent éditer les pages mais ne peuvent ni en créer ni en supprimer.
Il ne peut y avoir qu'un seul super_admin et c'est le seul à pouvoir gérer les privilèges.

**Remarques importantes :**

- Les droits sont : r = lecture, w = écriture, r&w = lecture et écriture, - = aucun accès.
- Les exceptions (game_master, super_admin) sont à respecter strictement dans la logique d'authentification et d'autorisation.
- Cette matrice est la source de vérité pour la gestion des droits dans le projet.

**Note sur la gestion des droits et visibilité**  
Toutes les entités principales et typages disposent d'un champ `is_visible` (contrôle d'accès par rôle) et `usable` (activation/désactivation). La gestion de la visibilité et de l'activation se fait donc au niveau de chaque enregistrement.

## 4. Référence centrale des entités

Les entités (utilisateurs, classes, monstres, objets, ressources, sorts, etc.) sont au cœur de l'architecture du projet. Leur structure, leurs propriétés, leurs relations et toutes les tables pivots sont désormais documentées dans un fichier dédié pour plus de clarté et d'exhaustivité.

👉 **Voir la documentation complète des entités et de la base de données : [ENTITIES_OVERVIEW.md](./ENTITIES_OVERVIEW.md)**

Ce document de référence détaille :

- La liste et la description de toutes les entités principales
- Les typages et listes évolutives
- Les tables pivots et de relation
- Les contraintes d'intégrité et la gestion de l'évolution
- Le glossaire des termes techniques
- Le lien vers le schéma SQL

## 5. Syntaxe des formules (Krosmoz JDR)

Une **formule** est une expression dynamique, toujours encadrée par des accolades `{ ... }`. Si la valeur n'est pas entre accolades, il s'agit d'une valeur fixe.

### 5.1. Règles de syntaxe

- Parenthèses pour l'ordre des opérations : `{ ([level] + 2) * 3 }`
- Opérateurs autorisés : +, -, \*, /, %
- Variables entre crochets `[variable]` (ex : `{ [level] + 2 }`)
- Listes : `[a-b]`, `[XdY]` (ex : `{ [1-5] + 2 }`, `{ [2d6] + [bonus] }`)
- Fonctions : min, max, floor, ceil, random
- Conditions : `condition ? valeur_si_vrai : valeur_si_faux` (ex : `{ [level] > 5 ? 10 : 5 }`)
- Min/Max en fin de formule : `{ [level] - 5 }(min: 2)(max: 4)`
- Une seule liste par formule

### 5.2. Exemples

- `{ [level] + 2 }`
- `{ random(1, 10) }`
- `{ [level] > 5 ? 10 : 5 }`
- `{ ([level] > 5 ? [level] : 5) * random(1, 6) }(max: 20)`

**Résumé des règles**

- Toujours utiliser `{ ... }` pour une formule.
- Une seule liste par formule, notée `[a-b]`, `[XdY]` ou variable-liste.
- Les variables sont notées `[variable]`.
- Les fonctions sont notées `nom_fonction(...)`.
- Les conditions sont de la forme `condition ? valeur1 : valeur2`.
- Les fonctions spéciales `(min: ...)`, `(max: ...)` se placent à la fin de la formule.
- Pour l'arrondi, utiliser uniquement `floor()` ou `ceil()` dans la formule.

## 6. Système de pages dynamiques (pages & sections)

Le projet utilise un système de pages dynamiques composées de sections, permettant de construire des pages riches, évolutives et personnalisables.

### 6.1. Table `pages`

| Champ      | Type     | Description                                     |
| ---------- | -------- | ----------------------------------------------- |
| id         | integer  | Identifiant unique                              |
| title      | string   | Titre de la page                                |
| slug       | string   | Identifiant unique (unique)                     |
| is_visible | string   | Rôle minimum pour voir la page (default: guest) |
| in_menu    | boolean  | Affichage dans le menu (default: true)          |
| state      | string   | État de la page (default: draft)                |
| parent_id  | FK pages | Page parente (nullable, nullOnDelete)           |
| menu_order | integer  | Ordre dans le menu (default: 0)                 |
| created_by | FK users | Créateur (nullable, nullOnDelete)               |
| created_at | datetime | Date de création                                |
| updated_at | datetime | Date de modification                            |
| deleted_at | datetime | Suppression logique (soft delete)               |

### 6.2. Table `sections`

| Champ      | Type     | Description                                        |
| ---------- | -------- | -------------------------------------------------- |
| id         | integer  | Identifiant unique                                 |
| page_id    | FK pages | Référence à la page (cascadeOnDelete)              |
| order      | integer  | Ordre d'affichage (default: 0)                     |
| type       | string   | Type de section (composant Vue)                    |
| params     | json     | Paramètres de la section                           |
| is_visible | string   | Rôle minimum pour voir la section (default: guest) |
| state      | string   | État de la section (default: draft)                |
| created_by | FK users | Créateur (nullable, nullOnDelete)                  |
| created_at | datetime | Date de création                                   |
| updated_at | datetime | Date de modification                               |
| deleted_at | datetime | Suppression logique (soft delete)                  |

### 6.3. Fonctionnement

- Une page est composée d'une ou plusieurs sections, ordonnées par le champ `order`.
- Chaque section référence un composant Vue (organisme) via le champ `type`.
- Les paramètres de la section sont stockés dans `params` (JSON), ce qui permet une grande flexibilité.
- Le menu du site est généré dynamiquement à partir des pages où `in_menu=1` et `state='published'`.
- Les pages peuvent être organisées en menu déroulant grâce à `parent_id` et `menu_order`.
- Les états (`state`) permettent de gérer le cycle de vie des pages et sections (brouillon, en attente, publié).

### 6.4. Exemples de types de section et params

- `text` : `{ "html": "<p>Contenu riche...</p>" }`
- `entity_table` : `{ "entity": "classes", "filters": { "is_visible": "guest" } }`
- `file` : `{ "url": "/storage/files/monfichier.pdf", "label": "Télécharger le PDF" }`

### 6.5. Association de pages aux scénarios et campagnes

- `scenario_page` (pivot) : Lie une page à un scénario
- `campaign_page` (pivot) : Lie une page à une campagne

### 6.6. Tableau des transitions d'état (pages/sections)

| État actuel | Action possible        | Nouvel état   |
| ----------- | ---------------------- | ------------- |
| draft       | Soumettre à validation | pending       |
| pending     | Publier                | published     |
| published   | Dépublier              | draft         |
| draft       | Publier directement    | published     |
| published   | Archiver               | archived (\*) |

(\*) L'état « archived » est optionnel et peut être ajouté selon les besoins.

## 7. Structure du layout

Pour la structure détaillée du layout (aside, header, footer, responsive, etc.) et les principes de design, voir le guide dédié : [DESIGN_GUIDE.md](./DESIGN_GUIDE.md)

## 8. Annexes

- [Convention des agents](./AGENTS_CONVENTION.md)
- [Documentation technique](./TECHNOLOGIES.md)
- Schéma : [schema.sql]
