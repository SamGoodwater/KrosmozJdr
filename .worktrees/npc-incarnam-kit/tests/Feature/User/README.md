# Tests de la chaîne de modification des utilisateurs

## Résumé

Les tests vérifient que la chaîne complète de modification des utilisateurs fonctionne correctement selon les règles suivantes :

### Règles d'autorisation

1. **Utilisateur standard** : Peut modifier uniquement son propre profil
2. **Admin (role 4)** : Peut modifier n'importe quel utilisateur
3. **Super_admin (role 5)** : Peut modifier n'importe quel utilisateur

### Tests implémentés

#### Tests de Policy (`UserPolicyTest.php`)
- ✅ Un utilisateur peut modifier son propre profil
- ✅ Un utilisateur ne peut pas modifier le profil d'un autre utilisateur
- ✅ Un admin peut modifier n'importe quel utilisateur
- ✅ Un super_admin peut modifier n'importe quel utilisateur
- ✅ Un utilisateur ne peut pas modifier le rôle d'un autre utilisateur
- ✅ Un admin peut modifier le rôle d'un utilisateur (mais pas admin/super_admin)
- ✅ Un admin ne peut pas modifier le rôle d'un admin
- ✅ Un admin ne peut pas modifier le rôle d'un super_admin
- ✅ Un super_admin peut modifier le rôle d'un utilisateur
- ✅ Un super_admin ne peut pas modifier le rôle d'un autre super_admin (logique métier dans le contrôleur)

#### Tests Unitaires du Contrôleur (`UserControllerUnitTest.php`)
- ✅ Un utilisateur peut modifier son propre mot de passe avec current_password
- ✅ Un utilisateur ne peut pas modifier son mot de passe sans current_password
- ✅ Un admin peut modifier le mot de passe d'un autre utilisateur sans current_password
- ✅ Un admin peut modifier le rôle d'un utilisateur (mais pas admin/super_admin)
- ✅ Un admin ne peut pas promouvoir un utilisateur en admin
- ✅ Un super_admin peut promouvoir un utilisateur en admin
- ✅ Personne ne peut promouvoir un utilisateur en super_admin

## Exécution des tests

```bash
# Tous les tests utilisateur
php artisan test --filter="User"

# Tests de policy uniquement
php artisan test --filter="UserPolicyTest"

# Tests unitaires du contrôleur uniquement
php artisan test --filter="UserControllerUnitTest"
```

## Couverture

Les tests couvrent :
- ✅ Les policies (autorisations)
- ✅ La logique métier du contrôleur (mise à jour de profil, mot de passe, rôle)
- ✅ Les validations (current_password requis pour soi-même, pas pour les admins)
- ✅ Les règles de promotion de rôle (seul super_admin peut promouvoir en admin, personne ne peut promouvoir en super_admin)

