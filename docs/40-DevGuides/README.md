# Guides de développement — KrosmozJDR

Ce dossier regroupe les scripts, automatisations et outils pour le développement, la maintenance et le déploiement du projet.

## Commande artisan universelle : `php artisan run`

La commande `run` centralise toutes les tâches de maintenance, de nettoyage, de mise à jour et de lancement du projet. Elle permet d’enchaîner plusieurs actions dans un ordre logique, avec des options unitaires ou composées.

### Principales options

| Option                | Description                                                                                 |
|---------------------- |-------------------------------------------------------------------------------------------|
| --kill                | Tuer les serveurs locaux (ports 8000, 8001, 8002, 5173)                                   |
| --clear:all           | Nettoyer tous les caches, CSS, debugbar, queue, schedule, events, optimisations            |
| --update:all          | Mise à jour complète (système, pnpm, composer, css, docs, dump-autoload)                   |
| --optimise:all        | Générer les fichiers IDE Helper et optimiser Laravel                                       |
| --migrate             | Exécuter les migrations                                                                    |
| --dev                 | Lancer le serveur en mode optimisé                                                        |
| --dev:watch           | Lancer le serveur en mode watch                                                           |
| --clean               | Enchaîner : kill, clear:all, update:base, optimise:all                                    |
| --all                 | Enchaîner : kill, clear:all, update:all, optimise:all, migrate, dev                       |
| --reset:pnpm          | Supprimer node_modules et pnpm-lock.yaml, puis pnpm install (jamais inclus dans --all)     |
| --reset:composer      | Supprimer vendor et composer.lock, puis composer install (jamais inclus dans --all)        |
| --install:pnpm        | Installer les dépendances pnpm (pnpm install)                                             |
| --install:composer    | Installer les dépendances composer (composer install)                                     |

> **Note :** Les options `--reset:pnpm` et `--reset:composer` sont à utiliser ponctuellement, en cas de corruption ou de problème de dépendances. Elles ne sont jamais incluses dans les options englobantes.

### Exemples d’utilisation

- Nettoyer et relancer le serveur :
  ```bash
  php artisan run --clean --dev
  ```
- Mise à jour complète (avec sudo pour pnpm/composer global) :
  ```bash
  sudo php artisan run --all
  ```
- Réinitialiser pnpm (en cas de souci de dépendances) :
  ```bash
  php artisan run --reset:pnpm
  ```
- Réinitialiser composer :
  ```bash
  php artisan run --reset:composer
  ```
- Installer les dépendances sans update global :
  ```bash
  php artisan run --install:pnpm --install:composer
  ```

### Bonnes pratiques

- **Mise à jour globale de pnpm/composer** : nécessite sudo, à faire manuellement si besoin, pas dans les scripts automatisés.
- **Utilisation de reset** : uniquement en cas de problème, jamais en routine.
- **Enchaînement d’options** : tu peux combiner autant d’options que tu veux, elles seront exécutées dans l’ordre logique (kill → clear → update/install → optimise → migrate → dev).
- **Voir le code source** : pour la liste complète des options et la logique, voir `app/Console/Commands/Run.php`.

---

Pour les scripts CSS, voir les fichiers du dossier `/scripts`. 