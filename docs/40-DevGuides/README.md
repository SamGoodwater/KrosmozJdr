# Guides de développement — KrosmozJDR

Ce dossier regroupe les scripts, automatisations et outils pour le développement, la maintenance et le déploiement du projet.

## Interface unifiée `project:*`

Voir **[PROJECT_CLI.md](./PROJECT_CLI.md)** : `project:deps`, `project:dev`, `project:clear`, `project:optimize`, `project:reset`, `project:effects`, `project:refresh`, `project:data`, `project:data:sync`, `project:super-admin`, `setup`.

## Commande `php artisan setup`

Setup centralise la vérification/installation des logiciels et librairies et la base de données. Utilisée seule ou via `ProjectRunService` / les commandes `project:*`. Options : `--install` (paquets apt dont MySQL + composer/pnpm), `--update` (apt, pnpm, composer), `--db` (MySQL par défaut : création user et base si besoin via root/DB_PASSWORD, puis migrations + seeders ; `--no-seed` pour sans seeders), `--clean` (supprimer node_modules, vendor, locks ; clear config), `--refresh` (clean puis réinstall). Liste des paquets apt dans `app/Console/Commands/Project/SetupCommand.php`.

---

Pour les scripts CSS, voir les fichiers du dossier `/scripts`.
