# Utilisateur système pour les imports automatiques

## 📋 Vue d'ensemble

Un utilisateur système a été créé pour gérer les imports automatiques du système de scrapping. Cet utilisateur ne peut **pas se connecter** pour des raisons de sécurité.

## 🔧 Implémentation

### Migration
- **Fichier** : `database/migrations/2025_11_27_145006_add_is_system_to_users_table.php`
- **Champ ajouté** : `is_system` (boolean, default: false)

### Modèle User
- **Constantes** :
  - `SYSTEM_USER_ID = 0` : ID théorique (non utilisé car auto-increment)
  - `SYSTEM_USER_EMAIL = 'system@krosmozjdr.local'` : Email unique pour identifier l'utilisateur système
- **Méthodes** :
  - `canLogin()` : Retourne `false` si `is_system = true`
  - `getSystemUser()` : Méthode statique pour récupérer l'utilisateur système

### Seeder
- **Fichier** : `database/seeders/UserSeeder.php`
- L'utilisateur système est créé automatiquement lors du seeding avec :
  - Email : `system@krosmozjdr.local`
  - Rôle : `ROLE_SUPER_ADMIN` (5)
  - `is_system` : `true`
  - Mot de passe : Aléatoire de 128 caractères (impossible à deviner)
  - Notifications : Désactivées

### Authentification
- **Fichier** : `app/Http/Requests/Auth/LoginRequest.php`
- Vérification ajoutée après `Auth::attempt()` pour empêcher la connexion des utilisateurs système
- Si un utilisateur système tente de se connecter, l'authentification échoue même avec le bon mot de passe

### Service d'intégration
- **Fichier** : `app/Services/Scrapping/DataIntegration/DataIntegrationService.php`
- **Méthode** : `getSystemUserId()`
- Utilise maintenant `User::getSystemUser()` en priorité pour les imports automatiques

## 🔒 Sécurité

1. **Mot de passe aléatoire** : Le mot de passe est généré aléatoirement (128 caractères), rendant toute tentative de connexion impossible
2. **Vérification dans LoginRequest** : Même si quelqu'un connaissait le mot de passe, la méthode `canLogin()` empêche la connexion
3. **Email unique** : L'email `system@krosmozjdr.local` est réservé et ne peut pas être utilisé par un autre utilisateur

## 📝 Utilisation

L'utilisateur système est automatiquement utilisé lors des imports automatiques :

```php
// Dans DataIntegrationService
$userId = $this->getSystemUserId(); // Retourne l'ID de l'utilisateur système
```

Pour récupérer l'utilisateur système manuellement :

```php
use App\Models\User;

$systemUser = User::getSystemUser();
if ($systemUser) {
    echo "ID: " . $systemUser->id;
    echo "Peut se connecter: " . ($systemUser->canLogin() ? 'Oui' : 'Non'); // Toujours 'Non'
}
```

## ✅ Vérification

Pour vérifier que l'utilisateur système fonctionne correctement :

```bash
# Créer l'utilisateur système
php artisan db:seed --class=UserSeeder

# Vérifier dans tinker
php artisan tinker
```

Puis :
```php
$sys = User::getSystemUser();
$sys->canLogin(); // Doit retourner false
```

## 🎯 Avantages

1. **Traçabilité** : Tous les imports automatiques sont associés à l'utilisateur système
2. **Sécurité** : L'utilisateur système ne peut pas se connecter
3. **Simplicité** : Pas besoin de gérer un utilisateur admin par défaut
4. **Cohérence** : Tous les imports utilisent le même utilisateur système

