# Utilisateur système (imports automatiques)

## Objectif
Le projet utilise un **utilisateur système** pour attribuer (tracer) les opérations automatiques, notamment les imports du scrapping.

Contrainte sécurité : cet utilisateur **ne peut pas se connecter**.

## Implémentation (référence)
- **Migration** : `database/migrations/2025_11_27_145006_add_is_system_to_users_table.php`
  - champ : `is_system` (boolean)
- **Modèle** : `app/Models/User.php`
  - `User::getSystemUser()`
  - `User::canLogin()` (retourne `false` si `is_system=true`)
- **Seeder** : `database/seeders/UserSeeder.php`
  - crée l’utilisateur `system@krosmozjdr.local` avec `is_system=true`

## Utilisation
Les services qui créent/modifient des entités automatiquement doivent privilégier `User::getSystemUser()` pour renseigner `created_by`.

