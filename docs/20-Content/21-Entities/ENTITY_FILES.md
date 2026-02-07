# Fichiers et médias (Media Library)

## Rôle et description

Les fichiers et images sont gérés par **Spatie Laravel Media Library**. Les documents, images et médias sont attachés aux modèles via des collections (`files`, `images`, `icons` selon le modèle).

## Où sont les fichiers

- **Sections** : `$section->getMedia('files')` — fichiers attachés à une section (collection `files`).
- **Scénarios** : `$scenario->getMedia('files')` et image principale `getMedia('images')`.
- **Campagnes** : `$campaign->getMedia('files')` et image principale `getMedia('images')`.
- **Entités (items, sorts, créatures, etc.)** : image principale via collection `images` (trait `HasEntityImageMedia`).
- **Caractéristiques** : icône via collection `icons`.

Il n’y a plus de modèle `File` ni de tables pivots `file_section`, `file_scenario`, `file_campaign` : tout passe par la table `media` et les collections.

## Exemples d’utilisation

- Ajout d’un fichier à une section : `$section->addMediaFromRequest('file')->toMediaCollection('files');`
- Récupérer l’URL de l’image d’une entité : `$entity->image` (colonne synchronisée) ou `$entity->getFirstMediaUrl('images')`.

## Liens utiles

- [SPATIE_MEDIA_LIBRARY.md](../../50-Fonctionnalités/Medias/SPATIE_MEDIA_LIBRARY.md)
- [ENTITY_SCENARIOS.md](ENTITY_SCENARIOS.md)
- [ENTITY_CAMPAIGNS.md](ENTITY_CAMPAIGNS.md)
- [ENTITY_SECTIONS.md](ENTITY_SECTIONS.md)
