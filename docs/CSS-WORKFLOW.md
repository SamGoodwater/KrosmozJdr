# 🎨 Workflow CSS - KrosmozJDR

## Vue d'ensemble

Ce workflow gère la compilation Sass, l'injection des variables de thème et la minification CSS de manière coordonnée et optimisée.

## 📁 Structure des fichiers

```
resources/
├── scss/
│   ├── custom.scss      # Styles personnalisés
│   └── theme.scss       # Variables de thème
└── css/
    ├── custom.css       # CSS compilé et minifié
    ├── theme.css        # Variables de thème (non minifié)
    ├── app.css          # Fichier principal avec variables injectées et minifié
```

## 🛠️ Scripts disponibles

### Scripts principaux

| Script | Description | Usage |
|--------|-------------|-------|
| `css:process` | Minification + injection unique | `pnpm run css:process` |
| `css:process:watch` | Minification + injection en surveillance | `pnpm run css:process:watch` |
| `css:process:verbose` | Avec détails | `pnpm run css:process:verbose` |
| `css:process:watch:verbose` | Surveillance avec détails | `pnpm run css:process:watch:verbose` |

### Scripts de minification

| Script | Description | Usage |
|--------|-------------|-------|
| `css:minify` | Minification seule | `pnpm run css:minify` |
| `css:minify:watch` | Minification en surveillance | `pnpm run css:minify:watch` |

### Scripts de nettoyage

| Script | Description | Usage |
|--------|-------------|-------|
| `css:clean` | Nettoyer les fichiers générés | `pnpm run css:clean` |
| `css:clean:verbose` | Nettoyage avec détails | `pnpm run css:clean:verbose` |

### Scripts de build complet

| Script | Description | Usage |
|--------|-------------|-------|
| `build:css:full` | Build complet (nettoyage + compilation + injection + minification) | `pnpm run build:css:full` |
| `build:css:full:verbose` | Build complet avec détails | `pnpm run build:css:full:verbose` |

### Scripts de développement

| Script | Description | Usage |
|--------|-------------|-------|
| `dev:frontend` | Développement frontend avec traitement CSS | `pnpm run dev:frontend` |
| `dev:frontend:optimized` | Développement optimisé | `pnpm run dev:frontend:optimized` |
| `dev:frontend:watch` | Développement avec surveillance | `pnpm run dev:frontend:watch` |

## 🔧 Scripts techniques

### `scripts/css-processor.cjs`

Script principal qui coordonne la minification et l'injection des variables de thème.

**Options :**
- `--minify` : Minifier les fichiers CSS avec csso
- `--inject` : Injecter les variables de thème
- `--watch` : Mode surveillance continue
- `--verbose` : Afficher plus de détails
- `--help` : Afficher l'aide

**Exemples :**
```bash
# Minification + injection unique
node scripts/css-processor.cjs --minify --inject

# Mode surveillance
node scripts/css-processor.cjs --minify --inject --watch

# Minification seule avec détails
node scripts/css-processor.cjs --minify --verbose
```

### `scripts/inject-theme-vars.cjs`

Script d'injection des variables de thème dans `app.css`.

**Options :**
- `--watch` : Mode surveillance continue
- `--wait` : Attendre que les fichiers CSS soient générés
- `--verbose` : Afficher plus de détails
- `--help` : Afficher l'aide

### `scripts/clean-css.cjs`

Script de nettoyage des fichiers CSS générés.

**Options :**
- `--verbose` : Afficher plus de détails
- `--help` : Afficher l'aide

**Fichiers supprimés :**
- `*.css` (fichiers CSS compilés)
- `*.css.map` (fichiers de map)

### `scripts/build-css.cjs`

Script de build complet qui orchestre tout le processus.

**Options :**
- `--clean` : Nettoyer les fichiers CSS avant le build
- `--minify` : Minifier les fichiers CSS après compilation
- `--inject` : Injecter les variables de thème
- `--verbose` : Afficher plus de détails
- `--help` : Afficher l'aide

**Étapes du build :**
1. Nettoyage (optionnel)
2. Compilation Sass
3. Injection des variables de thème (optionnel)
4. Minification (optionnel)

## 🚀 Workflows recommandés

### Développement quotidien

```bash
# Démarrage du serveur de développement avec traitement CSS automatique
pnpm run dev:frontend:optimized
```

### Build pour production

```bash
# Build complet avec nettoyage, compilation, injection et minification
pnpm run build:css:full
```

### Nettoyage manuel

```bash
# Nettoyer tous les fichiers générés
pnpm run css:clean
```

### Surveillance en développement

```bash
# Mode surveillance avec minification et injection
pnpm run css:process:watch
```

## ⚙️ Configuration

### Sass

Les fichiers Sass sont compilés avec le style `expanded` pour la lisibilité en développement.

### Minification

La minification utilise `csso` pour une compression optimale :
- Suppression des espaces inutiles
- Fusion des règles CSS
- Optimisation des sélecteurs

### Injection des variables

Le script d'injection :
- Attend que Sass ait terminé la compilation
- Extrait les variables de `theme.css`
- Les injecte dans `app.css` entre les marqueurs
- Évite les boucles infinies avec des protections

## 🔍 Dépannage

### Problèmes courants

1. **Fichiers CSS manquants**
   ```bash
   # Vérifier que Sass fonctionne
   pnpm run sass:build
   ```

2. **Boucles infinies**
   ```bash
   # Arrêter tous les processus
   pkill -f "node scripts"
   # Relancer proprement
   pnpm run css:process
   ```

3. **Erreurs de minification**
   ```bash
   # Vérifier l'installation de csso
   npx csso --version
   ```

### Logs et debugging

Utilisez l'option `--verbose` pour obtenir plus de détails :
```bash
pnpm run css:process:verbose
pnpm run build:css:full:verbose
```

## 📊 Performance

### Tailles typiques

| Fichier | Taille finale | Gain par rapport à l'original |
|---------|---------------|--------------------------------|
| `custom.css` | ~7.1MB | ~20% |
| `app.css` | ~34KB | ~10% |

### Optimisations

- **Debounce** : 1 seconde entre les traitements en mode watch
- **Protection contre les boucles** : Ignore les fichiers minifiés
- **Attente intelligente** : Vérifie que les fichiers sont complets
- **Traitement conditionnel** : Ne traite que si nécessaire

## 🔄 Intégration avec Laravel

### Commande `clear:all`

La commande Laravel `php artisan clear:all` inclut maintenant le nettoyage des fichiers CSS minifiés.

### Scripts de développement

Les scripts de développement frontend intègrent automatiquement le traitement CSS :
- `dev:frontend` : Avec traitement CSS
- `dev:frontend:optimized` : Version optimisée
- `dev:frontend:watch` : Avec surveillance

## 📝 Notes importantes

1. **theme.css ne doit jamais être minifié** car il est utilisé par le script d'injection
2. **Les fichiers minifiés sont générés automatiquement** lors du développement
3. **Le mode watch est optimisé** pour éviter les boucles infinies
4. **Les scripts sont robustes** avec gestion d'erreurs et timeouts
5. **La coordination est automatique** entre Sass, injection et minification 