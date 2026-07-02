# Système de Gestion d'Images

## Architecture

### Refonte (2026-05)

- **Spatie Media Library** : source de vérité pour les médias des entités (`HasEntityImageMedia`, conversions `webp` / `thumb`, URLs via `getUrl()`).
- **Upload applicatif** : `App\Services\Media\EntityImageMediaService` centralise validation, pièce jointe Spatie et payload JSON (`url`, `thumb_url`, `webp_url`, …).
- **Miniatures dynamiques** : routes `GET /media/thumbnails/{path}` + `App\Services\ImageService` (Intervention v3, conversions `cover` / `contain`, écriture sous `thumbnails/` avec création des répertoires).
- **Frontend** : atome `Image.vue` (skeleton non bloquant jusqu’au `@load`), `ImageService.getThumbnailUrl()` aligné sur les query params `w`, `h`, `fit`, `q`, `fm`.

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
// Afficher une image (fichier sur le disque `public`, chemin relatif)
GET /media/images/{path}

// Générer un thumbnail dynamique (Intervention Image v3 + Imagick)
GET /media/thumbnails/{path}?w=&h=&fit=&q=&fm=

// Nettoyer les thumbnails
POST /media/clean-thumbnails
```

### Paramètres `GET /media/thumbnails/{path}`

| Paramètre | Description | Défaut |
|-----------|-------------|--------|
| `w` | Largeur | 300 |
| `h` | Hauteur | 300 |
| `fit` | `cover` ou `contain` | `cover` |
| `q` | Qualité 1–100 | 80 |
| `fm` | Format de sortie : `jpg`, `jpeg`, `png`, `gif`, `webp` | `webp` |

Le frontend construit ces URLs via `ImageService.getThumbnailUrl()` (`resources/js/Utils/file/ImageService.js`), aligné sur ce contrat — **plus** d’URL du type `/storage/thumbnails/...` pour ce flux.

Les miniatures générées sont mises en cache disque sous `thumbnails/…` sur le disque `public` ; le cache applicatif utilise les tags seulement si le store le permet (ex. Redis), sinon `Cache::remember` sans tags (compatible tests / driver `array`).

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

Le composant `Image.vue` permet d'afficher des images avec des options avancées. Un **skeleton** peut rester visible en overlay jusqu’au chargement réseau réel (`@load`), sans bloquer le rendu du reste de la page (`loading="lazy"`, `decoding="async"`, transition d’opacité).

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
