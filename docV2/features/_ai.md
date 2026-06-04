# Features — carte IA (degré 1)

> Grosses fonctionnalités transverses du site. Chaque feature a sa propre carte `_ai.md` ; descends-y pour le détail.

## Quand lire ce nœud

- Tâche fonctionnelle (ajouter/modifier une feature métier) plutôt que purement structurelle.
- Tu cherches « comment marche X » côté produit (entités, import, CMS, droits…).

## Features

| Feature | En une ligne | Carte | Statut doc |
| --- | --- | --- | --- |
| **Entités JDR** | CRUD générique de 15+ types de jeu : modèle commun, vues, tables, droits. | [entities/_ai.md](./entities/_ai.md) | migré |
| **Scrapping** | Import DofusDB → KrosmozJDR (pipeline config-driven + jobs async). | [scrapping/_ai.md](./scrapping/_ai.md) | migré |
| **CMS / Pages** | Pages hiérarchiques + sections typées (Tiptap, krefs), menu dynamique. | [cms/_ai.md](./cms/_ai.md) | migré |
| **Permissions / Auth** | Rôles 0-5, policies entités, matrice de visibilité, OAuth. | [permissions/_ai.md](./permissions/_ai.md) | migré |
| **Caractéristiques** | Caractéristiques paramétrables (formules, limites, conversion Dofus). | _à migrer_ → `docs/50-Fonctionnalités/Characteristics-DB/` | stub |
| **Effets** | Moteur d'effets de sorts/objets (degrés, sous-effets, mapping DofusDB). | _à migrer_ → `docs/50-Fonctionnalités/Spell-Effects/` | stub |
| **Recherche globale** | Recherche multi-entités. | _à migrer_ → `app/Http/Controllers/Api/GlobalSearchController.php` | stub |
| **Médias** | Images entités/users/sections (Spatie Media Library). | _à migrer_ → `app/Services/Media/` | stub |
| **Notifications** | Notifications DB/email + digests. | _à migrer_ → `app/Support/Notifications/` | stub |
| **RGPD / Privacy** | Export et effacement des données utilisateur. | _à migrer_ → `app/Services/Privacy/` | stub |
| **Admin / Ops** | Dashboard admin, maintenance projet, review, planning. | _à migrer_ → `docs/40-DevGuides/PROJECT_CLI.md` | stub |

## Note

Les features marquées « stub » ne sont pas encore migrées dans `docV2` : la colonne « Carte » pointe vers la doc `docs/` existante en attendant.
