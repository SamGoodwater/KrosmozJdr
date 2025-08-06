# Rôles, droits et visibilité

## Rôles principaux
- guest, user, player, game_master, admin, super_admin

## Matrice des privilèges
- Voir tableau détaillé dans la doc (lecture, écriture, accès admin, gestion des privilèges)
- Les droits sont la source de vérité pour l’authentification et l’autorisation

## Gestion de la visibilité et activation
- Chaque entité a un champ `is_visible` (contrôle d'accès par rôle) et `usable` (activation/désactivation)
- Gestion fine des accès et de l’activation au niveau de chaque enregistrement 