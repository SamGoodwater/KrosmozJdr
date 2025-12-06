# Sections (`sections`)

## Rôle et description
Les sections représentent les blocs de contenu qui composent une page (texte, image, tableau, formulaire, etc.). Elles permettent de structurer l’affichage, la navigation et la personnalisation des pages dynamiques.

## Relations principales
- **Pages** : chaque section appartient à une page (`page_id`).
- **Fichiers** : via le pivot `file_section` (N:N avec `files`).
- **Utilisateurs** : via le pivot `section_user` (N:N avec `users`).

## Exemples d’utilisation
- Ajout d’une section de texte ou d’image à une page de campagne.
- Gestion des droits d’accès à une section.

## Liens utiles
- [Système de Pages et Sections](../PAGES_SECTIONS.md) - Documentation complète du système
- [ENTITY_PAGES.md](ENTITY_PAGES.md)
- [ENTITY_FILES.md](ENTITY_FILES.md) 