# Rôles, droits et visibilité

## Rôles principaux
- guest, user, player, game_master, admin, super_admin

## Matrice des privilèges
- Voir tableau détaillé dans la doc (lecture, écriture, accès admin, gestion des privilèges)
- Les droits sont la source de vérité pour l’authentification et l’autorisation

## Gestion de la visibilité et activation
- Chaque entité exposée au public possède :
  - `state` : état de cycle de vie (`raw`, `draft`, `playable`, `archived`)
  - `read_level` : niveau de rôle minimal pour **lire/voir**
  - `write_level` : niveau de rôle minimal pour **modifier**
- Les rôles sont ordonnés (niveau minimal requis) : `guest, user, player, game_master, admin, super_admin` (stockés en entiers).
- Contrainte métier : `write_level >= read_level`.