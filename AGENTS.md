# AGENTS.md

## Git

Une branche courte par sujet, puis commit, merge dans `main` et suppression. PR optionnelle en solo. Rule : `.cursor/rules/git-github.mdc`.

## Cursor Cloud specific instructions

Contexte : KrosmozJDR est une application web monolithique **Laravel 13 / PHP 8.4** (backend + API hybride Inertia)
et **Vue 3 / Inertia / Vite** (frontend, JS, Atomic Design). Base de données relationnelle, sessions et queue
stockées en base. Détails stack : `docs/_ai.md`, `docs/project/README.md`.

### Base de données : MariaDB, pas MySQL (important)

- Le service de base de données installé en Cloud est **MariaDB**, **pas** MySQL, même si `.env` et la CI mentionnent MySQL.
  **MySQL 8.x refuse** un `DEFAULT` SQL sur JSON/TEXT/BLOB (`ERROR 1101`). Les défauts concernés sont applicatifs
  (`User::$attributes`, `Creature::$attributes`). La CI GitHub tourne sur `mysql:8.4` ; le dev local Cloud reste MariaDB.
- `DB_CONNECTION` reste `mysql` dans `.env` : le driver MySQL de Laravel se connecte à MariaDB et détecte automatiquement
  la grammaire MariaDB. Ne pas changer cette valeur.
- Démarrer la base : `sudo service mariadb start` (pas de systemd dans le conteneur ; `service` fonctionne).
- Bases : `krosmoz` (dev) et `krosmoz_testing` (tests, cf. `phpunit.xml`). Utilisateur applicatif : `user` sans mot de passe
  (aligné sur `.env`). Accès admin local : `sudo mariadb`.

### Configuration `.env` à connaître

- `SESSION_SECURE_COOKIE` doit être **`false`** en dev HTTP local, sinon le cookie de session n'est pas posé et le login
  échoue en boucle (l'app tourne sur `http://localhost:8000`).
- `APP_KEY` est vide dans `.env.example` : `php artisan key:generate` est requis (fait au setup).

### Démarrer / lancer les services (dev)

Commandes : liste dans `app/Console/COMMANDS.md`. Entrée officielle serveur : `php artisan project:dev` (`--queue` pour la file).

- Helper concurrently : `composer run dev` (`php artisan serve` + `queue:listen` + CSS). Variante réseau : `composer run dev:network`.
- Ou séparément : `php artisan serve --host=0.0.0.0 --port=8000`, `pnpm run dev` (Vite HMR, port 5173),
  `php artisan queue:listen --tries=1` (driver queue = `database`).
- Le pipeline CSS custom (`pnpm run css`) génère `resources/css/*` ; nécessaire pour un rendu complet en dev.

### Seed / données de jeu (réseau externe)

- Utiliser `php artisan project:seed` pour peupler la base **sans** scrapping externe (données locales + capacités).
- `php artisan project:init` (et `project:refresh`) déclenchent en fin de pipeline le **scrapping de DofusDB.fr**
  (réseau externe). À éviter si l'egress est restreint ; utiliser `--skip-scrapping` / `--skip-types` sinon.
- Conséquence attendue sans scrapping : les images/icônes d'entités affichent « image non disponible » et renvoient des
  403 sur les assets. C'est **cosmétique** (aucune donnée média seedée), pas une panne fonctionnelle.

### Comptes de test seedés

Créés par `UserSeeder` (mot de passe : `password`) : `superadmin@test.fr` (rôle 5), `admin@test.fr` (4),
`gm@test.fr` (3), `player@test.fr` (2), `test-user@test.fr` (1).

### Lint / tests / build

- Lint PHP : `./vendor/bin/pint` (le repo a des écarts de style **préexistants** ; `pint --test` sort en échec sur du
  code non modifié — ne pas « corriger » ces fichiers sans demande).
- Lint JS : `pnpm lint` (ESLint, sous-ensemble ciblé de fichiers).
- Tests back : `php artisan test` (utilise MariaDB `krosmoz_testing`). Suite complète Feature ≈ longue (~25 min).
- Tests front : `pnpm test:run` (Vitest). Un test **préexistant** échoue (`tests/unit/composables/useEntityActions.test.js`,
  ordre des actions) indépendamment de l'environnement.
- Build front : `pnpm build`.
