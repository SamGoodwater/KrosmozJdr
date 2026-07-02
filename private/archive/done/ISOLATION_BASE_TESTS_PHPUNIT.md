# Isolation de la base de données pour les tests PHPUnit

**Contexte** : La base de données (MySQL de développement) se vidait régulièrement sans action volontaire (déconnexion, utilisateurs disparus après un `db:seed`).

**Cause identifiée** : Les tests PHP (PHPUnit) utilisent le trait `RefreshDatabase`. Lorsque `phpunit.xml` ne définit pas de base dédiée aux tests, PHPUnit utilise la connexion et la base du fichier `.env` (MySQL, base de dev). À chaque exécution de `php artisan test` (ou des tests depuis l’IDE), `RefreshDatabase` effectue un reset des tables (migrate fresh) sur **cette** base, ce qui supprime toutes les données (utilisateurs, etc.).

**Solution appliquée** : Dans `phpunit.xml`, activation d’une base dédiée aux tests :

- `DB_CONNECTION=sqlite`
- `DB_DATABASE=:memory:`

Les tests s’exécutent désormais sur une base SQLite en mémoire, sans toucher à la base MySQL de développement.

**Référence** : [TESTING_PRACTICES.md](../10-BestPractices/TESTING_PRACTICES.md) (section « Base de données »).
