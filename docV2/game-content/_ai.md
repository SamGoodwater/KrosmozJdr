# Contenu de jeu (lore + règles JDR) — pointeur [HORS CODE]

> Le livre de règles et le lore du JDR sont du **contenu**, pas du code applicatif. Ils ne sont **pas migrés** dans `docV2` et ne doivent **pas être réécrits** par un agent (risque d'altérer le game design).

## Où c'est

- Règles JDR : `docs/400- Jeu/420- Règles/` (chapitres 1 à 5). Entrée : `docs/400- Jeu/420- Règles/TABLE_DES_MATIERES.md`.
- Ressources design / cohérence : `docs/400- Jeu/410- Ressources/`.
- Audits règles PDF ↔ seeders : `docs/500- Ressources/`.

## Lien avec le code

Ces règles sont parfois publiées en pages du site (CMS) via des commandes Artisan d'import. Le pont « données » est côté code : voir [features/entities/_ai.md](../features/entities/_ai.md) et le CMS.

## Règle pour l'agent

- Lecture autorisée pour comprendre une mécanique de jeu.
- Modification du **texte des règles** : seulement sur demande explicite de l'utilisateur.
