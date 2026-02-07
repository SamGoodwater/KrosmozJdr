# Spatie Laravel Media Library — Gestion des médias

## Vue d'ensemble

Le projet utilise [Spatie Laravel Media Library](https://spatie.be/docs/laravel-medialibrary/v11/introduction) pour associer des fichiers (images, documents) aux modèles Eloquent, générer des conversions (dont WebP) et des miniatures, et gérer le stockage sur le disque Laravel.

## WebP et conversions

**Oui, Spatie gère la conversion en WebP.** Les conversions sont définies dans chaque modèle via `registerMediaConversions()` en appelant `->format('webp')` sur une conversion.

Exemple (Section) :

```php
public function registerMediaConversions(?Media $media = null): void
{
    $this->addMediaConversion('thumb')
        ->performOnCollections('files')
        ->width(368)
        ->height(232)
        ->format('webp')  // sortie en WebP
        ->nonQueued();

    $this->addMediaConversion('webp')
        ->performOnCollections('files')
        ->format('webp')  // même image en WebP
        ->nonQueued();
}
```

- Par défaut les conversions sont en JPG ; `->format('webp')` impose le format WebP.
- L’original peut rester dans son format d’upload ; les **dérivés** (thumb, etc.) sont en WebP si configurés ainsi.
- Référence : [Defining conversions](https://spatie.be/docs/laravel-medialibrary/v11/converting-images/defining-conversions).

## Modèles concernés

Toutes les entités avec image/icône utilisent Media Library (une seule méthode, pas de rétrocompat).

| Modèle / usage              | Collection   | Usage                                    | Conversions      |
|-----------------------------|-------------|------------------------------------------|------------------|
| **Section**                 | `files`     | Fichiers attachés à une section          | `thumb`, `webp`  |
| **Scenario**                | `files`     | Fichiers liés au scénario (getMedia)     | `thumb`, `webp`  |
| **Campaign**                | `files`     | Fichiers liés à la campagne (getMedia)   | `thumb`, `webp`  |
| **Resource**                | `images`    | Image principale (singleFile)             | `thumb`, `webp`  |
| **EntityImageUpload**       | `images`    | Upload orphelin (bulk, sans resource_id) | `thumb`, `webp`  |
| **Characteristic**    | `icons`       | Icône (singleFile)                  | `webp`           |

Les modèles **Scenario** et **Campaign** n’ont plus de relation `files()` vers l’ancien modèle `File` : utiliser `$model->getMedia('files')`.  
L’upload d’icône de caractéristique requiert `characteristic_id` et attache le média à la caractéristique (Media Library).

## Répertoire et nommage (constantes sur les modèles)

Les modèles peuvent définir **où** et **comment** sont stockés/nommés les médias :

- **MEDIA_PATH** (string) : répertoire de stockage, lu par `ModelAwarePathGenerator`. Ex. : `images/entity/breeds` → les médias d’un Breed sont sous `images/entity/breeds/{id_media}/`. Si absent, Spatie utilise l’id du média seul.
- **MEDIA_FILE_PATTERN_{COLLECTION}** (string) : motif de nom de fichier pour une collection. Placeholders : `[name]` (slug du nom ou id), `[date]` (Y-m-d), `[id]` (id du modèle). Ex. : `MEDIA_FILE_PATTERN_ICONS = 'breed-icon-[name]-[date]'` → `breed-icon-eniripsa-2025-02-07.png`.
- **MEDIA_FILE_PATTERN** : motif par défaut pour toutes les collections si aucune constante par collection n’est définie.

Le trait `HasMediaCustomNaming` fournit `getMediaFileNameForCollection($collection, $extension)` ; il est utilisé par `HasEntityImageMedia` et par `Characteristic`. L’intégration scrapping et l’upload d’icône appellent ce nommage quand le modèle le définit.

## Installation et configuration

- Package : `spatie/laravel-medialibrary` (v11).
- Config : `config/media-library.php` (disque par défaut `public`, taille max 10 Mo, `path_generator` = `ModelAwarePathGenerator`).
- Table : `media` (migration fournie par le package).

## Scrapping

Le téléchargement d’images depuis des URLs externes (ex. DofusDB) est géré par **IntegrationService::attachImageFromUrl()** : après création/mise à jour de l'entité, si `download_images` est activé et l'URL autorisée (`scrapping.images.allowed_hosts`), le média est attaché via `addMediaFromUrl()->toMediaCollection('images')` et la colonne `image` est mise à jour.

## Références

- [Spatie Media Library v11 — Introduction](https://spatie.be/docs/laravel-medialibrary/v11/introduction)
- [Defining conversions](https://spatie.be/docs/laravel-medialibrary/v11/converting-images/defining-conversions)
- [Système d’upload frontend](../30-UI/INPUT%20SYSTEM/FILE_UPLOAD.md)
