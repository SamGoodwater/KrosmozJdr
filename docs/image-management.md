# Système de Gestion d'Images

## Architecture

Le système de gestion d'images est composé de trois composants principaux :

1. **ImageService** (`app/Services/ImageService.php`)

    - Gestion des images et des thumbnails
    - Conversion en WebP
    - Support des icônes FontAwesome
    - Cache des thumbnails

2. **FileService** (`app/Services/FileService.php`)

    - Constantes pour les extensions autorisées
    - Validation des fichiers
    - Gestion des disques de stockage

3. **ImageController** (`app/Http/Controllers/ImageController.php`)
    - Routes pour l'affichage des images
    - Génération des thumbnails
    - Nettoyage du cache

## Routes

```php
// Afficher une image
GET /media/images/{path}

// Générer un thumbnail
GET /media/thumbnails/{path}

// Nettoyer les thumbnails
POST /media/clean-thumbnails
```

## Utilisation

### Génération de Thumbnails

```php
$imageService = new ImageService();

// Options de base
$options = [
    'width' => 300,
    'height' => 300,
    'fit' => 'cover',
    'quality' => 80,
    'format' => 'webp'
];

$thumbnailPath = $imageService->generateThumbnail('images/photo.jpg', $options);
```

### Conversion en WebP

```php
$webpPath = $imageService->convertToWebp('images/photo.jpg');
```

### Nettoyage du Cache

```php
// Nettoyer les thumbnails plus vieux que 24h
$imageService->cleanThumbnails(86400);
```

## Composant Vue

Le composant `Image.vue` permet d'afficher des images avec des options avancées :

```vue
<Image
    source="images/photo.jpg"
    alt="Description"
    size="lg"
    ratio="16/9"
    fit="cover"
    position="center"
    filter="grayscale"
    rounded="lg"
    mask="mask-squircle"
    :transform="{
        width: 800,
        height: 600,
        quality: 80,
    }"
/>
```

### Props

- `src` : URL directe de l'image
- `source` : Chemin source pour ImageService
- `alt` : Texte alternatif (obligatoire)
- `size` : Taille prédéfinie (xs, sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl)
- `width` : Largeur personnalisée
- `height` : Hauteur personnalisée
- `ratio` : Ratio d'aspect (square, video, 16/9, 4/3, 3/2, 2/1, etc.)
- `fit` : object-fit (cover, contain, fill, none, scale-down)
- `position` : object-position (center, top, right, bottom, left, etc.)
- `filter` : Filtre(s) CSS (grayscale, sepia, blur, etc.)
- `rounded` : Arrondi (none, sm, md, lg, xl, 2xl, 3xl, full, circle)
- `mask` : Classe DaisyUI mask-\*
- `transform` : Options de transformation pour ImageService

## Tests

Les tests unitaires sont disponibles dans `tests/Unit/Services/ImageServiceTest.php`.

Pour exécuter les tests :

```bash
php artisan test --filter=ImageServiceTest
```

## Maintenance

### Nettoyage Automatique

Un nettoyage automatique des thumbnails est configuré pour s'exécuter tous les jours via la tâche planifiée :

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('media:clean-thumbnails')->daily();
}
```

### Logs

Les erreurs et les opérations importantes sont enregistrées dans les logs :

- `storage/logs/laravel.log`

## Sécurité

- Validation des extensions de fichiers
- Limitation de la taille des fichiers
- Protection contre les injections de chemin
- Conversion automatique en WebP pour les images
- Cache des thumbnails pour les performances
