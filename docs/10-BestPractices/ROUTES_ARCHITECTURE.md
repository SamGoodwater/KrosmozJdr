# Architecture des routes — KrosmozJDR

Les routes sont découpées par thème pour éviter des fichiers monolithiques. Un seul point d'entrée par groupe (web, api) charge les sous-fichiers par `require`.

## Vue d'ensemble

| Fichier / Dossier | Rôle |
|-------------------|------|
| `routes/web.php` | Point d'entrée web ; charge auth, **web/**\*, admin, entities, services |
| `routes/api.php` | Point d'entrée API ; charge `routes/api/*.php` |
| `routes/auth.php` | Login, register, password, verification (convention Laravel, resté en racine) |
| `routes/web/statics.php` | Accueil, contribute (pages statiques) |
| `routes/web/user.php` | Profil utilisateur, gestion admin users |
| `routes/web/notifications.php` | Notifications |
| `routes/web/file.php` | Médias (images, thumbnails) |
| `routes/web/page.php` | Pages et sections (contenu éditable) |
| `routes/admin/*.php` | Admin (caractéristiques, formules, mappings, types d'effets) |
| `routes/entities/*.php` | CRUD web par entité (resource, spell, monster, etc.) |
| `routes/services/scrapping.php` | Pages et actions scrapping (web) |
| `routes/api/*.php` | API découpée par thème (auth, scrapping, types, tables, entities) |

## Routes Web

- **Publiques** : `web/statics.php` (home, contribuer), `web/page.php` (lecture pages), certaines routes dans `entities/*` (index, show).
- **Auth** : `auth.php`, `web/user.php`, `web/notifications.php`, `web/file.php` (media).
- **Admin** : `admin/*.php` (middleware auth/role selon besoin).
- **Entités** : un fichier par entité dans `entities/` (attribute, campaign, capability, breed, consumable, creature, item, item-type, monster, monster-race, npc, panoply, resource, resource-type, scenario, shop, specialization, spell, spell-type, consumable-type).
- **Services** : `services/scrapping.php` (dashboard scrapping, etc.).

## Routes API

Le préfixe `api` et le middleware `api` sont appliqués par `bootstrap/app.php`. Les fichiers dans `routes/api/` définissent uniquement le chemin sous ce préfixe.

| Fichier | Thème | Contenu principal |
|---------|--------|-------------------|
| `api/auth.php` | Authentification | `GET /user` (Sanctum) |
| `api/scrapping.php` | Scrapping | Tests, config, search, meta, preview, import, registries (resource-types, item-types, consumable-types), catalogue monster-races |
| `api/types.php` | Types internes | `types/monster-races`, `types/spell-types` (index, bulk, delete) |
| `api/entity-table.php` | Datasets client | `entity-table/resources`, `entity-table/resource-types` (TanStack Table mode client) |
| `api/tables.php` | Tables v2 | `tables/*` (resources, items, spells, monsters, npcs, campaigns, etc. — TableResponse typé) |
| `api/entities.php` | Entités (bulk, upload) | `entities/*/bulk`, `entities/resources/upload-image` |

## Conventions

- **Nommage** : fichiers en kebab-case (ex. `entity-table.php`, `spell-type.php`).
- **Un thème = un fichier** : pour ajouter des routes API, soit ajouter dans le fichier de thème existant, soit créer un nouveau fichier dans `routes/api/` et l’inclure dans `api.php`.
- **Noms de routes** : garder le préfixe cohérent (ex. `scrapping.*`, `api.tables.*`, `api.entities.*`).
- **Middleware** : préciser `web` + `auth` pour les endpoints consommés par l’UI Inertia (session, CSRF).

## Référence

- Laravel : [Routing](https://laravel.com/docs/routing).
- Projet : [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md).
