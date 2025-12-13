# Ressource — Chaîne Niveau 1 (CRUD + pivots essentiels + scrapping types)

## Contexte
L’objectif était de livrer une première chaîne complète autour de l’entité **Ressource** afin de valider le “pattern” du projet (scrapping → base → backend → frontend), tout en évitant de devoir finaliser toutes les autres entités en même temps.

## Ce qui a été livré
- **CRUD Ressource complet** : création, lecture (page dédiée), édition (page dédiée), suppression, liste avec recherche/tri/filtre.
- **Pivots niveau 1** : gestion minimale et stable des relations clés (quantités et pivots simples) sans dépendre d’une refonte globale.
- **Typage Ressource pilotable** : prise en compte des types de ressources et exposition côté UI (sélection/affichage).
- **Scrapping (types DofusDB)** : mise en place d’un registre en base pour gérer les nouveaux `typeId` détectés (autoriser / blacklister / remettre en attente) sans toucher au code.

## Pourquoi c’est utile
- Permet de valider la cohérence de la stack (Laravel + Inertia + Vue + composants génériques).
- Donne un premier “module complet” utilisable par les admins.
- Prépare la suite : amélioration progressive des pivots (niveau 2) et enrichissement des workflows métiers (recettes/drops).

## Prochaine étape suggérée
Passer au **niveau 2** uniquement quand la chaîne Ressource est utilisée :
- rattrapage / recalcul des pivots lors des imports,
- écrans dédiés à la gestion avancée des relations,
- amélioration de l’affichage “Large/Compact/Text” pour les détails des pivots.


