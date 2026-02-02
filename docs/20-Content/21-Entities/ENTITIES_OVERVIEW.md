# Vue d'ensemble des entités – Krosmoz JDR

## 1. Introduction

Ce document présente en détail toutes les entités du projet Krosmoz JDR, leur structure, leurs relations et leur rôle dans l'architecture globale du jeu. Il sert de référence centrale pour la modélisation des données, la génération des migrations, l'implémentation backend et la compréhension métier.

Pour la vision d'ensemble du projet, des règles, des droits et de l'architecture, se référer au document principal : [CONTENT_OVERVIEW.md](./CONTENT_OVERVIEW.md).

Les entités sont au cœur du système : elles structurent toutes les données du jeu (joueurs, objets, créatures, scénarios, etc.) et leurs interactions.

## 2. Champs communs à toutes les entités principales et typages

Toutes les entités principales et typages disposent des champs suivants :

| Champ       | Type     | Description                                                |
| ----------- | -------- | ---------------------------------------------------------- |
| state       | string   | État (raw, draft, playable, archived)                      |
| read_level  | tinyint  | Niveau minimal requis pour lire/voir (0..5)                |
| write_level | tinyint  | Niveau minimal requis pour modifier (0..5, >= read_level)  |
| created_by | FK users | Créateur (nullable, nullOnDelete ou cascade)     |
| created_at | datetime | Date de création                                 |
| updated_at | datetime | Date de modification                             |
| deleted_at | datetime | Suppression logique (soft delete)                |

## 3. Vue d'ensemble des entités

Le tableau ci-dessous synthétise les principales entités du projet, leur type et leur usage. Les sections suivantes détaillent chaque entité.

| Entité           | Type           | Usage principal / Description courte                         |
| ---------------- | -------------- | ------------------------------------------------------------ |
| users            | Métier         | Utilisateurs du site (joueurs, MJ, admins, etc.)             |
| breeds           | Métier         | Classes jouables (Féca, Iop, etc.)                          |
| monsters         | Métier         | Monstres du jeu                                              |
| npcs             | Métier         | Personnages non joueurs                                      |
| items            | Métier         | Objets et équipements (armes, armures, anneaux, etc.)        |
| resources        | Métier         | Ressources de base (minerais, plantes, peaux, etc.)          |
| spells           | Métier         | Sorts et magies                                              |
| capabilities     | Métier         | Compétences spéciales, pouvoirs                              |
| attributes       | Métier         | Attributs/caractéristiques (force, intelligence, etc.)       |
| consumables      | Métier         | Objets consommables (potions, nourritures, parchemins, etc.) |
| shops            | Métier         | Boutiques et vendeurs                                        |
| specializations  | Métier         | Spécialisations de classes (tank, soigneur, dps, etc.)       |
| scenarios        | Métier         | Scénarios de jeu (quêtes, donjons, aventures)                |
| campaigns        | Métier         | Campagnes de jeu (enchaînement de scénarios)                 |
| item_types       | Typage         | Types d'objets/équipements (arme, armure, anneau, etc.)      |
| resource_types   | Typage         | Types de ressources (minerai, plante, etc.)                  |
| consumable_types | Typage         | Types de consommables (potion, nourriture, etc.)             |
| spell_types      | Typage         | Types de sorts (attaque, soin, invocation, etc.)             |
| monster_races    | Typage         | Races de monstres (bouftou, tofu, etc.)                      |
| panoplies        | Typage         | Ensembles d'objets (sets/panoplies)                          |
| ...              | Pivot/Relation | Tables de relation entre entités (voir section 5)            |

**Schéma ERD** :

- Un schéma visuel des entités et de leurs relations est disponible ici : [Voir le schéma SQL](schema.sql)
- Ce schéma illustre les entités principales, leurs propriétés et les relations pivots du projet.

## 3. Entités métier principales

### 3.1. Utilisateurs (`users`)

| Champ             | Type      | Description                      |
| ----------------- | --------- | -------------------------------- |
| id                | integer   | Identifiant unique               |
| name              | string    | Nom ou pseudo                    |
| email             | string    | Adresse email (unique)           |
| email_verified_at | timestamp | Date de vérification email       |
| password          | string    | Mot de passe (hashé)             |
| remember_token    | string    | Token de session                 |
| role              | string    | Rôle (guest, user, player, etc.) |
| avatar            | string    | URL de l'avatar (nullable)       |
| deleted_at        | datetime  | Suppression logique              |
| created_at        | datetime  | Date de création                 |
| updated_at        | datetime  | Date de modification             |

### 3.2. Classes (`breeds`)

| Champ            | Type     | Description                       |
| ---------------- | -------- | --------------------------------- |
| id               | integer  | Identifiant unique                |
| official_id      | string   | Id officiel (nullable)            |
| dofusdb_id       | string   | Id DofusDB (nullable)             |
| name             | string   | Nom de la classe                  |
| description_fast | string   | Description courte (nullable)     |
| description      | string   | Description (nullable)            |
| life             | string   | Vie de base (nullable)            |
| life_dice        | string   | Dés de vie (nullable)             |
| specificity      | string   | Spécificité (nullable)            |
| dofus_version    | string   | Version Dofus (default: 3)        |
| state           | string   | État (raw, draft, playable, archived) |
| read_level      | tinyint  | Niveau min. lecture (0..5)            |
| write_level     | tinyint  | Niveau min. écriture (0..5)           |
| image            | string   | URL image (nullable)              |
| icon             | string   | URL icône (nullable)              |
| auto_update      | boolean  | MAJ auto (default: true)          |
| softDeletes      | datetime | Suppression logique               |
| created_by       | FK users | Créateur (nullable, nullOnDelete) |
| created_at       | datetime | Date de création                  |
| updated_at       | datetime | Date de modification              |

### 3.3. Monstres (`monsters`)

| Champ           | Type     | Description                       |
| --------------- | -------- | --------------------------------- |
| id              | integer  | Identifiant unique                |
| creature_id     | FK       | Référence à creatures (nullable)  |
| official_id     | string   | Id officiel (nullable)            |
| dofusdb_id      | string   | Id DofusDB (nullable)             |
| dofus_version   | string   | Version Dofus (default: 3)        |
| auto_update     | boolean  | MAJ auto (default: true)          |
| size            | integer  | Taille (default: 2)               |
| monster_race_id | FK       | Race (nullable, FK monster_races) |
| created_at      | datetime | Date de création                  |
| updated_at      | datetime | Date de modification              |

### 3.4. NPCs (`npcs`)

| Champ             | Type     | Description                      |
| ----------------- | -------- | -------------------------------- |
| id                | integer  | Identifiant unique               |
| creature_id       | FK       | Référence à creatures (nullable) |
| story             | string   | Histoire (nullable)              |
| historical        | string   | Historique (nullable)            |
| age               | string   | Âge (nullable)                   |
| size              | string   | Taille (nullable)                |
| classe_id         | FK       | Classe associée (nullable)       |
| specialization_id | FK       | Spécialisation (nullable)        |
| created_at        | datetime | Date de création                 |
| updated_at        | datetime | Date de modification             |

### 3.5. Objets/Équipements (`items`)

| Champ         | Type     | Description                        |
| ------------- | -------- | ---------------------------------- |
| id            | integer  | Identifiant unique                 |
| official_id   | string   | Id officiel (nullable)             |
| dofusdb_id    | string   | Id DofusDB (nullable)              |
| name          | string   | Nom de l'objet                     |
| level         | string   | Niveau requis (nullable)           |
| description   | string   | Description (nullable)             |
| effect        | string   | Effet (formule ou texte, nullable) |
| bonus         | string   | Bonus (nullable)                   |
| recipe        | string   | Recette (nullable)                 |
| price         | string   | Prix (nullable)                    |
| rarity        | integer  | Rareté (default: 0)                |
| dofus_version | string   | Version Dofus (default: 3)         |
| state        | string   | État (raw, draft, playable, archived) |
| read_level   | tinyint  | Niveau min. lecture (0..5)            |
| write_level  | tinyint  | Niveau min. écriture (0..5)           |
| image         | string   | URL image (nullable)               |
| auto_update   | boolean  | MAJ auto (default: true)           |
| item_type_id  | FK       | Type d'objet (nullable)            |
| softDeletes   | datetime | Suppression logique                |
| created_by    | FK users | Créateur (nullable, nullOnDelete)  |
| created_at    | datetime | Date de création                   |
| updated_at    | datetime | Date de modification               |

### 3.6. Ressources (`resources`)

| Champ            | Type     | Description                       |
| ---------------- | -------- | --------------------------------- |
| id               | integer  | Identifiant unique                |
| dofusdb_id       | string   | Id DofusDB (nullable)             |
| official_id      | integer  | Id officiel (nullable)            |
| name             | string   | Nom de la ressource               |
| description      | string   | Description (nullable)            |
| level            | string   | Niveau (default: 1)               |
| price            | string   | Prix (nullable)                   |
| weight           | string   | Poids (nullable)                  |
| rarity           | integer  | Rareté (default: 0)               |
| dofus_version    | string   | Version Dofus (default: 3)        |
| state            | string   | État (raw, draft, playable, archived) |
| read_level       | tinyint  | Niveau min. lecture (0..5)            |
| write_level      | tinyint  | Niveau min. écriture (0..5)           |
| image            | string   | URL image (nullable)              |
| auto_update      | boolean  | MAJ auto (default: true)          |
| resource_type_id | FK       | Type de ressource (nullable)      |
| softDeletes      | datetime | Suppression logique               |
| created_by       | FK users | Créateur (nullable, nullOnDelete) |
| created_at       | datetime | Date de création                  |
| updated_at       | datetime | Date de modification              |

### 3.7. Sorts (`spells`)

| Champ                            | Type     | Description                           |
| -------------------------------- | -------- | ------------------------------------- |
| id                               | integer  | Identifiant unique                    |
| official_id                      | string   | Id officiel (nullable)                |
| dofusdb_id                       | string   | Id DofusDB (nullable)                 |
| name                             | string   | Nom du sort                           |
| description                      | string   | Description                           |
| effect                           | string   | Effet (formule ou texte, nullable)    |
| area                             | integer  | Zone d'effet (default: 0)             |
| level                            | string   | Niveau requis (default: 1)            |
| po                               | string   | Portée (default: 1)                   |
| po_editable                      | boolean  | Portée éditable (default: true)       |
| pa                               | string   | Coût en PA (default: 3)               |
| cast_per_turn                    | string   | Lancers/tour (default: 1)             |
| cast_per_target                  | string   | Lancers/cible (default: 0)            |
| sight_line                       | boolean  | Ligne de vue (default: true)          |
| number_between_two_cast          | string   | Intervalle entre lancers (default: 0) |
| number_between_two_cast_editable | boolean  | Intervalle éditable (default: true)   |
| element                          | integer  | Élément (default: 0)                  |
| category                         | integer  | Catégorie (default: 0)                |
| is_magic                         | boolean  | Magique ? (default: true)             |
| powerful                         | integer  | Puissance (default: 0)                |
| state                            | string   | État (raw, draft, playable, archived) |
| read_level                       | tinyint  | Niveau min. lecture (0..5)            |
| write_level                      | tinyint  | Niveau min. écriture (0..5)           |
| image                            | string   | URL image (nullable)                  |
| auto_update                      | boolean  | MAJ auto (default: true)              |
| softDeletes                      | datetime | Suppression logique                   |
| created_by                       | FK users | Créateur (nullable, nullOnDelete)     |
| created_at                       | datetime | Date de création                      |
| updated_at                       | datetime | Date de modification                  |

### 3.8. Capacités (`capabilities`)

| Champ                 | Type     | Description                            |
| --------------------- | -------- | -------------------------------------- |
| id                    | integer  | Identifiant unique                     |
| name                  | string   | Nom de la capacité                     |
| description           | string   | Description (nullable)                 |
| effect                | string   | Effet (formule ou texte, nullable)     |
| level                 | string   | Niveau requis (default: 1)             |
| pa                    | string   | Coût en PA (default: 3)                |
| po                    | string   | Portée (default: 0)                    |
| po_editable           | boolean  | Portée éditable (default: true)        |
| time_before_use_again | string   | Temps avant réutilisation (default: 0) |
| casting_time          | string   | Temps d'incantation (default: 0)       |
| duration              | string   | Durée (default: 0)                     |
| element               | string   | Élément (default: neutral)             |
| is_magic              | boolean  | Magique ? (default: true)              |
| ritual_available      | boolean  | Rituel dispo (default: true)           |
| powerful              | string   | Puissance (nullable)                   |
| state                 | string   | État (raw, draft, playable, archived)  |
| read_level            | tinyint  | Niveau min. lecture (0..5)             |
| write_level           | tinyint  | Niveau min. écriture (0..5)            |
| image                 | string   | URL image (nullable)                   |
| softDeletes           | datetime | Suppression logique                    |
| created_by            | FK users | Créateur (nullable, nullOnDelete)      |
| created_at            | datetime | Date de création                       |
| updated_at            | datetime | Date de modification                   |

### 3.9. Attributs (`attributes`)

| Champ       | Type     | Description                       |
| ----------- | -------- | --------------------------------- |
| id          | integer  | Identifiant unique                |
| name        | string   | Nom de l'attribut                 |
| description | string   | Description (nullable)            |
| state       | string   | État (raw, draft, playable, archived) |
| read_level  | tinyint  | Niveau min. lecture (0..5)            |
| write_level | tinyint  | Niveau min. écriture (0..5)           |
| image       | string   | URL image (nullable)              |
| softDeletes | datetime | Suppression logique               |
| created_by  | FK users | Créateur (nullable, nullOnDelete) |
| created_at  | datetime | Date de création                  |
| updated_at  | datetime | Date de modification              |

### 3.10. Consommables (`consumables`)

| Champ              | Type     | Description                        |
| ------------------ | -------- | ---------------------------------- |
| id                 | integer  | Identifiant unique                 |
| official_id        | string   | Id officiel (nullable)             |
| dofusdb_id         | string   | Id DofusDB (nullable)              |
| name               | string   | Nom du consommable                 |
| description        | string   | Description (nullable)             |
| effect             | string   | Effet (formule ou texte, nullable) |
| level              | string   | Niveau requis (nullable)           |
| recipe             | string   | Recette (nullable)                 |
| price              | string   | Prix (nullable)                    |
| rarity             | integer  | Rareté (default: 0)                |
| state              | string   | État (raw, draft, playable, archived) |
| read_level         | tinyint  | Niveau min. lecture (0..5)            |
| write_level        | tinyint  | Niveau min. écriture (0..5)           |
| dofus_version      | string   | Version Dofus (default: 3)         |
| image              | string   | URL image (nullable)               |
| auto_update        | boolean  | MAJ auto (default: true)           |
| softDeletes        | datetime | Suppression logique                |
| consumable_type_id | FK       | Type de consommable (nullable)     |
| created_by         | FK users | Créateur (nullable, nullOnDelete)  |
| created_at         | datetime | Date de création                   |
| updated_at         | datetime | Date de modification               |

### 3.11. Boutiques (`shops`)

| Champ       | Type     | Description                          |
| ----------- | -------- | ------------------------------------ |
| id          | integer  | Identifiant unique                   |
| name        | string   | Nom de la boutique                   |
| description | string   | Description (nullable)               |
| location    | string   | Localisation (nullable)              |
| price       | integer  | Prix de base (default: 0)            |
| state       | string   | État (raw, draft, playable, archived) |
| read_level  | tinyint  | Niveau min. lecture (0..5)            |
| write_level | tinyint  | Niveau min. écriture (0..5)           |
| image       | string   | URL image (nullable)                 |
| softDeletes | datetime | Suppression logique                  |
| created_by  | FK users | Créateur (nullable, nullOnDelete)    |
| npc_id      | FK       | NPC associé (nullable, nullOnDelete) |
| created_at  | datetime | Date de création                     |
| updated_at  | datetime | Date de modification                 |

### 3.12. Spécialisations (`specializations`)

| Champ       | Type     | Description                       |
| ----------- | -------- | --------------------------------- |
| id          | integer  | Identifiant unique                |
| name        | string   | Nom de la spécialisation          |
| description | string   | Description (nullable)            |
| state       | string   | État (raw, draft, playable, archived) |
| read_level  | tinyint  | Niveau min. lecture (0..5)            |
| write_level | tinyint  | Niveau min. écriture (0..5)           |
| image       | string   | URL image (nullable)              |
| softDeletes | datetime | Suppression logique               |
| created_by  | FK users | Créateur (nullable, nullOnDelete) |
| created_at  | datetime | Date de création                  |
| updated_at  | datetime | Date de modification              |

### 3.13. Scénarios (`scenarios`)

| Champ       | Type     | Description                |
| ----------- | -------- | -------------------------- |
| id          | integer  | Identifiant unique         |
| name        | string   | Nom du scénario            |
| description | string   | Description (nullable)     |
| slug        | string   | Identifiant URL            |
| keyword     | string   | Mot-clé (nullable)         |
| is_public   | boolean  | Public ? (default: false)  |
| progress_state | integer | État d’avancement (default: 0) |
| state       | string   | État (raw, draft, playable, archived) |
| read_level  | tinyint  | Niveau min. lecture (0..5) |
| write_level | tinyint  | Niveau min. écriture (0..5) |
| image       | string   | URL image (nullable)       |
| softDeletes | datetime | Suppression logique        |
| created_by  | FK users | Créateur (cascadeOnDelete) |
| created_at  | datetime | Date de création           |
| updated_at  | datetime | Date de modification       |

### 3.14. Campagnes (`campaigns`)

| Champ       | Type     | Description                |
| ----------- | -------- | -------------------------- |
| id          | integer  | Identifiant unique         |
| name        | string   | Nom de la campagne         |
| description | string   | Description (nullable)     |
| slug        | string   | Identifiant URL            |
| keyword     | string   | Mot-clé (nullable)         |
| is_public   | boolean  | Public ? (default: false)  |
| progress_state | integer | État d’avancement (default: 0) |
| state       | string   | État (raw, draft, playable, archived) |
| read_level  | tinyint  | Niveau min. lecture (0..5) |
| write_level | tinyint  | Niveau min. écriture (0..5) |
| image       | string   | URL image (nullable)       |
| softDeletes | datetime | Suppression logique        |
| created_by  | FK users | Créateur (cascadeOnDelete) |
| created_at  | datetime | Date de création           |
| updated_at  | datetime | Date de modification       |

## 4. Typages et listes évolutives

### 4.1. Types d'objets (`item_types`)

| Champ       | Type     | Description                  |
| ----------- | -------- | ---------------------------- |
| id          | integer  | Identifiant unique           |
| name        | string   | Nom du type d'objet          |
| state       | string   | État (raw, draft, playable, archived) |
| read_level  | tinyint  | Niveau min. lecture (0..5)           |
| write_level | tinyint  | Niveau min. écriture (0..5)          |
| softDeletes | datetime | Suppression logique          |
| created_by  | FK users | Créateur (nullable, cascade) |
| created_at  | datetime | Date de création             |
| updated_at  | datetime | Date de modification         |

### 4.2. Types de ressources (`resource_types`)

| Champ       | Type     | Description                  |
| ----------- | -------- | ---------------------------- |
| id          | integer  | Identifiant unique           |
| name        | string   | Nom du type de ressource     |
| state       | string   | État (raw, draft, playable, archived) |
| read_level  | tinyint  | Niveau min. lecture (0..5)           |
| write_level | tinyint  | Niveau min. écriture (0..5)          |
| softDeletes | datetime | Suppression logique          |
| created_by  | FK users | Créateur (nullable, cascade) |
| created_at  | datetime | Date de création             |
| updated_at  | datetime | Date de modification         |

### 4.3. Types de consommables (`consumable_types`)

| Champ       | Type     | Description                  |
| ----------- | -------- | ---------------------------- |
| id          | integer  | Identifiant unique           |
| name        | string   | Nom du type de consommable   |
| state       | string   | État (raw, draft, playable, archived) |
| read_level  | tinyint  | Niveau min. lecture (0..5)           |
| write_level | tinyint  | Niveau min. écriture (0..5)          |
| softDeletes | datetime | Suppression logique          |
| created_by  | FK users | Créateur (nullable, cascade) |
| created_at  | datetime | Date de création             |
| updated_at  | datetime | Date de modification         |

### 4.4. Types de sorts (`spell_types`)

| Champ       | Type     | Description                       |
| ----------- | -------- | --------------------------------- |
| id          | integer  | Identifiant unique                |
| name        | string   | Nom du type de sort               |
| description | string   | Description (nullable)            |
| color       | string   | Couleur (default: zinc)           |
| icon        | string   | Icône (nullable)                  |
| state       | string   | État (raw, draft, playable, archived) |
| read_level  | tinyint  | Niveau min. lecture (0..5)           |
| write_level | tinyint  | Niveau min. écriture (0..5)          |
| softDeletes | datetime | Suppression logique               |
| created_by  | FK users | Créateur (nullable, nullOnDelete) |
| created_at  | datetime | Date de création                  |
| updated_at  | datetime | Date de modification              |

### 4.5. Races de monstres (`monster_races`)

| Champ         | Type     | Description                    |
| ------------- | -------- | ------------------------------ |
| id            | integer  | Identifiant unique             |
| name          | string   | Nom de la race                 |
| state         | string   | État (raw, draft, playable, archived) |
| read_level    | tinyint  | Niveau min. lecture (0..5)            |
| write_level   | tinyint  | Niveau min. écriture (0..5)           |
| softDeletes   | datetime | Suppression logique            |
| created_by    | FK users | Créateur (nullable, cascade)   |
| id_super_race | FK       | Super race (nullable, cascade) |
| created_at    | datetime | Date de création               |
| updated_at    | datetime | Date de modification           |

### 4.6. Panoplies (`panoplies`)

| Champ       | Type     | Description                  |
| ----------- | -------- | ---------------------------- |
| id          | integer  | Identifiant unique           |
| name        | string   | Nom de la panoplie           |
| description | string   | Description (nullable)       |
| bonus       | string   | Bonus accordé (nullable)     |
| state       | string   | État (raw, draft, playable, archived) |
| read_level  | tinyint  | Niveau min. lecture (0..5)           |
| write_level | tinyint  | Niveau min. écriture (0..5)          |
| softDeletes | datetime | Suppression logique          |
| created_by  | FK users | Créateur (nullable, cascade) |
| created_at  | datetime | Date de création             |
| updated_at  | datetime | Date de modification         |

## 5. Tables pivots et de relation

**Toutes les tables pivots utilisent des clés primaires composites, sauf indication contraire.**

- Les FK sont en général en `cascadeOnDelete` sauf indication contraire.
- Les tables pivots n'utilisent pas la suppression logique sauf si explicitement précisé.

### Exemples de tables pivots :

| Table pivot               | Champs principaux / Spéciaux                          |
| ------------------------- | ----------------------------------------------------- |
| capability_class          | capability_id, class_id                               |
| breed_spell               | breed_id, spell_id                                    |
| attribute_class           | attribute_id, class_id                                |
| capability_specialization | capability_id, specialization_id                      |
| spell_invocation          | spell_id, monster_id                                  |
| spell_type                | spell_id, spell_type_id                               |
| attribute_creature        | attribute_id, creature_id                             |
| capability_creature       | capability_id, creature_id                            |
| consumable_creature       | consumable_id, creature_id, quantity (default: 1)     |
| creature_item             | creature_id, item_id, quantity (default: 1)           |
| creature_spell            | creature_id, spell_id                                 |
| creature_resource         | creature_id, resource_id, quantity (default: 1)       |
| consumable_resource       | consumable_id, resource_id, quantity (default: 1)     |
| item_resource             | item_id, resource_id, quantity (default: 1)           |
| item_panoply              | item_id, panoply_id                                   |
| file_scenario             | id, scenario_id, file                                 |
| file_campaign             | id, campaign_id, file                                 |
| scenario_page             | scenario_id, page_id                                  |
| campaign_page             | campaign_id, page_id                                  |
| campaign_scenario         | campaign_id, scenario_id, order (nullable)            |
| scenario_link             | id, scenario_id, next_scenario_id, condition          |
| item_shop                 | item_id, shop_id, quantity, price, comment (nullable) |
| resource_shop             | resource_id, shop_id, quantity, price, comment        |
| consumable_shop           | consumable_id, shop_id, quantity, price, comment      |
| ...                       | ...                                                   |

## 6. Contraintes d'intégrité et gestion de l'évolution

### 6.1. Intégrité référentielle

- Toutes les relations de type clé étrangère sont en `ON DELETE CASCADE` sauf indication contraire, afin d'assurer la cohérence des données lors de la suppression d'une entité liée.
- Les champs obligatoires sont notés `NOT NULL` dans le schéma SQL.
- Les tables pivots utilisent des clés primaires composites pour garantir l'unicité des relations.
- Les valeurs des listes définies (éléments, rareté, rôles, etc.) doivent correspondre à la documentation : toute valeur non conforme est rejetée.

### 6.2. Suppression logique (soft delete)

- Les champs `deleted_at` permettent la suppression logique des entités et des relations (soft delete).
- Une entité ou une valeur de liste référencée ne doit jamais être supprimée physiquement : elle doit être désactivée (soft delete) pour préserver l'intégrité des relations.

### 6.3. Listes évolutives

- Certaines listes (types de consommables, d'objets, de ressources, etc.) sont dites « évolutives » :
  - Leur contenu peut être modifié via l'interface d'administration (ajout, édition, suppression).
  - Les migrations et seeders doivent permettre l'ajout de nouvelles valeurs sans casser les relations existantes.
  - Les valeurs supprimées doivent être désactivées (soft delete) plutôt que supprimées physiquement si elles sont référencées.

### 6.4. Bonnes pratiques

- Toujours valider la cohérence des données lors de l'ajout, la modification ou la suppression d'une entité ou d'une relation.
- Documenter toute contrainte spécifique ou exception dans le schéma ou la documentation technique.
- Prévoir des tests d'intégrité réguliers pour détecter d'éventuelles incohérences ou orphelins.

## 7. Glossaire spécifique aux entités

- **Entité** : Objet métier principal du jeu (ex : utilisateur, classe, monstre, objet, ressource, sort, etc.).
- **Typage** : Table listant des catégories ou types utilisés par d'autres entités (ex : item_types, spell_types).
- **Pivot (table pivot)** : Table de relation entre deux entités principales (ex : item_resource, breed_spell).
- **Clé étrangère** : Champ d'une table référant à la clé primaire d'une autre table pour assurer l'intégrité référentielle.
- **Soft delete (suppression logique)** : Suppression d'une donnée en la marquant comme supprimée (champ `deleted_at`), sans la retirer physiquement de la base.
- **Liste évolutive** : Liste dont les valeurs peuvent être modifiées via l'interface d'administration (ajout, édition, désactivation).
- **Formule** : Expression dynamique (ex : `{ [level] * 2 }`) permettant de calculer une valeur à partir de variables, opérateurs, fonctions, etc.
- **Campagne** : Suite de scénarios joués par un ou plusieurs joueurs.
- **Scénario** : Unité de jeu (quête, donjon, aventure) pouvant contenir des monstres, objets, ressources, etc.
- **Spécialisation** : Sous-type ou orientation d'une classe ou d'un personnage (ex : tank, soigneur, dps).
- **Attribut** : Caractéristique d'une créature ou d'une classe (ex : force, intelligence).
- **Capacité** : Compétence spéciale ou pouvoir utilisable par une créature ou une classe.
- **Rareté** : Niveau de rareté d'un objet, ressource ou consommable (0 : commun, 4 : légendaire).
- **Panoplie** : Ensemble d'objets qui, équipés ensemble, confèrent des bonus spécifiques.
- **Relation** : Lien logique ou physique entre deux entités (ex : un monstre possède des sorts, un objet est composé de ressources).

> **Note** : La table des classes jouables est nommée `breeds` et la clé étrangère `breed_id` est utilisée dans la base de données et le code (npcs, pivot breed_spell), afin d'éviter tout conflit avec le mot réservé `class` dans la plupart des langages de programmation.
