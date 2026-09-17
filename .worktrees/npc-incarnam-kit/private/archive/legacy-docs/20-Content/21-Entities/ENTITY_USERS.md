# Utilisateurs (`users`)

## Rôle et description
Les utilisateurs représentent toutes les personnes ayant un compte sur la plateforme : joueurs, MJ (maîtres du jeu), administrateurs, invités, etc. Ils sont au cœur de l’interaction avec le jeu, la gestion des campagnes, scénarios, personnages, et la personnalisation de l’expérience.

## Relations principales
- **Création d’entités** : de nombreuses entités (créatures, objets, scénarios, etc.) référencent l’utilisateur qui les a créées (`created_by`).
- **Campagnes, pages, scénarios, sections** : relations N:N via les pivots `campaign_user`, `page_user`, `scenario_user`, `section_user` (participation, droits, progression).
- **Sessions, notifications, mots de passe** : tables techniques associées à la gestion de compte.

## Exemples d’utilisation
- Connexion, gestion du profil, choix d’avatar.
- Attribution de droits sur des campagnes ou scénarios.
- Création de contenu (personnages, objets, etc.).

## Liens utiles
- [Pivots campagne, page, scénario, section](../pivots/)
- [entity_creatures.md](ENTITY_CREATURES.md) 