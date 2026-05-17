# Classes (`breeds`)

## Rôle et description

Les classes (entité technique `breed` / `breeds`) représentent les archétypes jouables du jeu (ex. Féca, Iop, Eniripsa…). Elles définissent les capacités de base, la progression, les spécificités et les données liées (sorts, capacités, langues, orientations élémentaires, etc.) pour les personnages ou PNJ qui portent une **classe**.

## Relations principales

- **NPC** : chaque NPC peut référencer une classe (`breed_id`, relation `breed`).
- **Créatures** : les personnages joueurs ou les PNJ passent par une créature ; la **classe** est portée au niveau du **NPC** (ou des données de fiche associées), pas par une FK directe générique sur toutes les créatures dans ce modèle documentaire simplifié.

## Différence avec les spécialisations (`specializations`)

Les **spécialisations** sont une **autre entité** (table `specializations`). Il n’existe **pas** de clé `breed_id` sur `specializations` : on ne modélise pas « une spécialisation appartient à une classe » en base. Un **NPC** peut avoir à la fois un `breed_id` et un `specialization_id` selon les besoins du contenu. Les fiches spécialisation ont **moins de propriétés métier** que les fiches classe (voir [ENTITY_SPECIALIZATIONS.md](ENTITY_SPECIALIZATIONS.md)).

## Exemples d’utilisation

- Création d’un personnage joueur.
- Attribution d’une classe à un PNJ.

## Liens utiles

- [ENTITY_NPCS.md](ENTITY_NPCS.md)
- [ENTITY_SPECIALIZATIONS.md](ENTITY_SPECIALIZATIONS.md)
