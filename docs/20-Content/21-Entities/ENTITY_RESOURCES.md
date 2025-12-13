# Ressources (`resources`)

## Rôle et description
Les ressources regroupent tous les matériaux de base du jeu : minerais, plantes, peaux, ingrédients, etc. Elles servent à la fabrication d’objets, de consommables, à l’accomplissement de quêtes ou à l’économie du jeu.

## Relations principales
- **Type de ressource** : chaque ressource référence un type (`resource_type_id`).
- **Créatures** : via le pivot `creature_resource` (N:N avec `creatures`).
- **Objets** : via le pivot `item_resource` (N:N avec `items`).
- **Consommables** : via le pivot `consumable_resource` (N:N avec `consumables`).
- **Boutiques** : via le pivot `resource_shop` (N:N avec `shops`).
- **Scénarios, campagnes** : via les pivots `resource_scenario`, `resource_campaign`.

## Exemples d’utilisation
- Récolte d’une ressource par un joueur.
- Utilisation d’une ressource dans une recette d’objet ou de consommable.

## Typage
Chaque ressource possède un type (`resource_type_id`) qui permet de regrouper les ressources par famille (minerai, plante, peau, bois, etc.).
Exemples de types : minerai, plante, peau, bois, poisson, tissu, etc.

## Implémentation (niveau 1)
- **Backend**
  - CRUD complet via `ResourceController` (index/show/create/store/edit/update/delete).
  - Gestion des relations (pivots) via endpoints dédiés :
    - `updateItems` (pivot `quantity`)
    - `updateConsumables` (pivot `quantity`)
    - `updateCreatures` (pivot `quantity`)
    - `updateShops` (pivots `quantity`, `price`, `comment`)
    - `updateScenarios` (sans pivot)
    - `updateCampaigns` (sans pivot)
- **Frontend**
  - Liste: tableau avec recherche/tri/filtres (niveau + type).
  - Pages: `Show` (lecture) et `Edit` (édition) avec affichage des pivots au minimum (quantités).
  - Modale de création: utilise le composant générique `CreateEntityModal` + configuration des champs Ressource.
- **Scrapping**
  - Les ressources sont importées via le flux “item” (DofusDB `/items`).
  - Les `typeId` DofusDB sont gérés via un registre en base (`resource_types` + décision allow/blocked/pending)
    et une UI de revue dans le dashboard scrapping.

## Liens utiles
- [ENTITY_ITEMS.md](ENTITY_ITEMS.md)
- [ENTITY_CONSUMABLES.md](ENTITY_CONSUMABLES.md)
- [ENTITY_SHOPS.md](ENTITY_SHOPS.md)
- [ENTITY_RESOURCE_TYPES.md](ENTITY_RESOURCE_TYPES.md) 