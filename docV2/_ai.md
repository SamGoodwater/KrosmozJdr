# KrosmozJDR — carte maître IA (degré 0)

> Point d'entrée unique pour un agent. KrosmozJDR = application web JDR (univers Dofus/Krosmoz) : référentiel d'entités de jeu, CMS de règles, import depuis DofusDB, gestion fine des droits.
> Stack : **Laravel 12 / PHP 8.4** + **Inertia.js** + **Vue 3 (JS, Atomic Design)** + **Tailwind/DaisyUI** + **MySQL**.

## Protocole de navigation (lis ceci d'abord)

1. Identifie le ou les domaines concernés par la tâche dans la carte ci-dessous.
2. Ouvre le `_ai.md` du domaine (et descends de `_ai.md` en `_ai.md` si besoin).
3. N'ouvre le `README.md` humain ou le code **que** si tu as besoin du détail.
4. Ne charge pas des domaines non concernés.

## Carte des domaines (degré 1)

| Domaine | En une ligne | Carte |
| --- | --- | --- |
| **Backend** | Structure Laravel : modèles, contrôleurs, services, jobs, console, config. | [backend/_ai.md](./backend/_ai.md) |
| **Frontend** | Vue 3 + Inertia, Atomic Design, état (Pinia minimal + composables), Ziggy. | [frontend/_ai.md](./frontend/_ai.md) |
| **Database** | Schéma MySQL, tables, pivots (voir `docs/20-Content/SCHEMA.md`). | [database/_ai.md](./database/_ai.md) |
| **Features** | Grosses fonctionnalités transverses (entités, scrapping, CMS, droits…). | [features/_ai.md](./features/_ai.md) |
| **Best practices** | Conventions, nommage, sécurité, tests. | [best-practices/_ai.md](./best-practices/_ai.md) |
| **Dev guides** | CLI `project:*`, performance, génération d'images. | [dev-guides/_ai.md](./dev-guides/_ai.md) |
| **Project** | Vision, mission, stack détaillée, OAuth, emails. | [project/_ai.md](./project/_ai.md) |

## Fonctionnalités (degré 1a, sous `features/`)

| Feature | En une ligne | Carte |
| --- | --- | --- |
| **Entités JDR** | CRUD de 15+ types de jeu (sorts, objets, monstres, classes…) + vues + tables. | [features/entities/_ai.md](./features/entities/_ai.md) |
| **Scrapping** | Import DofusDB : Collecte → Conversion → Validation → Intégration. | [features/scrapping/_ai.md](./features/scrapping/_ai.md) |
| Autres | CMS, caractéristiques, effets, permissions, recherche, médias, notifications, RGPD, admin/ops, OAuth. | [features/_ai.md](./features/_ai.md) |

## Contenu de jeu (hors code)

Le livre de règles et le lore JDR sont du **contenu**, pas du code. Ne pas le réécrire ; juste s'y référer.

- [game-content/_ai.md](./game-content/_ai.md) → pointe vers `docs/400- Jeu/`.

## Conventions transverses à connaître (très court)

- **Rôles utilisateur** : entier 0→5 = guest, user, player, game_master, admin, super_admin (`app/Models/User.php`).
- **Entités de jeu** : champs communs `state` (`raw`/`draft`/`playable`/`archived`), `read_level`, `write_level`, `created_by`. Droits via `app/Policies/Entity/BaseEntityPolicy.php`.
- **Inertia** : `Inertia::render('Pages/...')` → fichier `resources/js/Pages/Pages/...vue`.
- **i18n** : `vue-i18n` documenté mais **non branché** côté `resources/js` — libellés FR en dur.
- **Atomic Design** : `resources/js/Pages/{Atoms,Molecules,Organismes}` ; index machine `*.index.json`.

## Légende des degrés

- **Degré 0** : ce fichier (racine).
- **Degré 1** : domaines (backend, frontend, features…).
- **Degré 1a, 1b…** : sous-domaines / features individuelles.
- **Feuille** : `README.md` humain sans `_ai.md`.
