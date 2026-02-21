# Passage de MySQL à PostgreSQL — KrosmozJDR

## En bref

Le projet utilise **MySQL par défaut**. Il reste **compatible PostgreSQL** sans modification de code : si vous souhaitez repasser à PostgreSQL (base vide), un simple changement de configuration suffit.

## Étapes pour basculer

1. **Installer PostgreSQL** (si besoin) et créer une base + utilisateur. Vérifier que l’extension PHP `pdo_pgsql` est activée (`php -m | grep pdo_pgsql`).
2. **Configurer `.env`** :
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=krosmoz_db
   DB_USERNAME=krosmoz_user
   DB_PASSWORD=votre_mot_de_passe
   ```
3. **Setup** (vérifie PostgreSQL, crée user/base si besoin avec DB_USERNAME/DB_PASSWORD, migrations + seeders) :
   ```bash
   php artisan setup
   ```
   Pour la création auto, le compte `postgres` doit avoir pour mot de passe la valeur de `DB_PASSWORD` (le temps du setup). Avec `--db` : migrations + seeders par défaut ; ajouter `--no-seed` pour ne pas lancer les seeders.
4. **Vider le cache config** (optionnel) :
   ```bash
   php artisan config:clear
   ```

## Points vérifiés dans le projet

- Aucun SQL brut spécifique MySQL dans l’application (le seul `DB::raw` utilise `COALESCE`, standard SQL).
- Les migrations utilisent uniquement le Schema Builder Laravel ; les types `unsigned*` sont gérés par Laravel pour PostgreSQL.
- La méthode `->after()` dans certaines migrations est **ignorée** sous PostgreSQL (colonnes ajoutées en fin de table), sans erreur.

## Références

- [TECHNOLOGIES.md](TECHNOLOGIES.md) — stack et base de données.
- [Laravel — Database](https://laravel.com/docs/database) — connexions et migrations.
