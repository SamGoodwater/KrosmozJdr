# Pages (`pages`)

## Rôle et description
Les pages représentent les contenus dynamiques du site ou du jeu : pages de campagne, de scénario, d’aide, etc. Elles structurent l’information, la navigation et l’accès aux différentes fonctionnalités.

## Relations principales
- **Sections** : chaque page est composée de sections (`sections`).
- **Scénarios, campagnes** : via les pivots `scenario_page`, `campaign_page`.
- **Fichiers** : via le pivot `file_section` (N:N avec `files`).
- **Utilisateurs** : via le pivot `page_user` (N:N avec `users`).
- **Parent/enfant** : une page peut avoir une page parente (`parent_id`).

## Exemples d’utilisation
- Création d’une page de campagne personnalisée.
- Ajout de sections dynamiques à une page.

## Liens utiles
- [Système de Pages et Sections](../PAGES_SECTIONS.md) - Documentation complète du système
- [ENTITY_CAMPAIGNS.md](ENTITY_CAMPAIGNS.md)
- [ENTITY_SECTIONS.md](ENTITY_SECTIONS.md) 