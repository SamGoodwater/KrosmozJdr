# Fichiers (`files`)

## Rôle et description
Les fichiers regroupent tous les documents, images, médias ou ressources numériques associés aux entités du jeu (scénarios, campagnes, pages, etc.). Ils permettent d’enrichir le contenu, d’illustrer ou de stocker des données annexes.

## Relations principales
- **Scénarios, campagnes** : via les pivots `file_scenario`, `file_campaign`.
- **Sections** : via le pivot `file_section` (N:N avec `sections`).

## Exemples d’utilisation
- Ajout d’une image à un scénario ou une page.
- Stockage de documents de campagne.

## Liens utiles
- [ENTITY_SCENARIOS.md](ENTITY_SCENARIOS.md)
- [ENTITY_CAMPAIGNS.md](ENTITY_CAMPAIGNS.md)
- [ENTITY_SECTIONS.md](ENTITY_SECTIONS.md) 