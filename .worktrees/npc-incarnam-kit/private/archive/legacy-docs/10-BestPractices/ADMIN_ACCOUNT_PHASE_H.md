# Admin & compte — Phase H (release 1.3.2)

**Objectif** : scinder gestion du contenu et espace administration, tableaux de bord, zone sensible, compte utilisateur.

**Voir aussi** : [CHECKLIST release 1.3.2](../110-%20To%20Do/CHECKLIST-release-1.3.2.md)

## Menus compte

| Entrée | Rôle | Route |
| --- | --- | --- |
| Gestion du contenu | game_master+ | `admin.content.dashboard.index` |
| Espace administration | admin+ | `admin.recap.index` (mot de passe) |
| Pages | `pagesManager` | `pages.index` |

## Zones

- **Gestion du contenu** (`content.area`) : `/admin/content` — camemberts entités × statuts, pages/sections ; nav dédiée (caractéristiques, langues, effets, sous-effets).
- **Administration** (`admin.area`) : `/admin/recap` — courbe inscriptions + camembert rôles ; nav scrapping, utilisateurs, maintenance, planning cron (super_admin).
- **Zone sensible** : middleware `password.confirm` sur `/admin/recap` ; cadenas vert sur l’avatar si session confirmée (< 1 h inactivité).

## Mon compte

- Raccourcis entités (déjà présents), bloc **Règles & légal** (CGU, confidentialité, cookies, changelog).
- Paramètres : `user.settings` (notifications).

## Accueil

- Texte d’introduction enrichi dans `CriticalPagesSeeder` (projet + jeu, ton règles).

## Tests

- `AdminDashboardControllerTest`
- `AdminOverviewStatsServiceTest`
