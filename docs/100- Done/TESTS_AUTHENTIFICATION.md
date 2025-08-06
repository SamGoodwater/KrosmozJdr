# Tests d'authentification - Documentation

## Vue d'ensemble

Cette documentation décrit les tests d'authentification mis en place pour le projet Krosmoz-JDR. Les tests couvrent les fonctionnalités principales d'authentification : inscription, connexion, déconnexion, validation des formulaires et middlewares.

## Tests créés

### 1. Tests Feature - Routes d'authentification

#### `tests/Feature/Auth/RegistrationTest.php`
Tests complets pour l'inscription des utilisateurs :

- **Navigation** : Affichage de la page d'inscription, redirection des utilisateurs connectés
- **Validation** : Nom requis, email requis et unique, mot de passe requis et confirmé
- **Fonctionnalités** : Création d'utilisateur, connexion automatique, hashage du mot de passe
- **Événements** : Vérification du déclenchement de l'événement `Registered`
- **Valeurs par défaut** : Rôle, notifications, canaux de notification

**Méthodes de test :**
- `test_registration_page_can_be_rendered()`
- `test_authenticated_user_is_redirected_from_registration_page()`
- `test_new_users_can_register()`
- `test_name_is_required_for_registration()`
- `test_name_cannot_exceed_255_characters()`
- `test_email_is_required_for_registration()`
- `test_email_must_be_valid_format()`
- `test_email_must_be_unique()`
- `test_email_is_converted_to_lowercase()`
- `test_password_is_required_for_registration()`
- `test_password_confirmation_must_match()`
- `test_password_must_meet_complexity_requirements()`
- `test_user_is_automatically_logged_in_after_registration()`
- `test_password_is_hashed_when_stored()`
- `test_registered_event_is_dispatched()`
- `test_default_values_are_set_on_registration()`

#### `tests/Feature/Auth/LoginTest.php`
Tests complets pour la connexion des utilisateurs :

- **Navigation** : Affichage de la page de connexion, redirection des utilisateurs connectés
- **Authentification** : Connexion par email ou pseudo, gestion des identifiants invalides
- **Fonctionnalités** : "Se souvenir de moi", redirection intended, régénération de session
- **Sécurité** : Rate limiting, gestion des utilisateurs supprimés
- **Robustesse** : Caractères spéciaux, différents formats d'identifiants

**Méthodes de test :**
- `test_login_page_can_be_rendered()`
- `test_authenticated_user_is_redirected_from_login_page()`
- `test_users_can_authenticate_using_email()`
- `test_users_can_authenticate_using_username()`
- `test_email_authentication_is_case_insensitive()`
- `test_users_cannot_authenticate_with_invalid_credentials()`
- `test_users_cannot_authenticate_with_nonexistent_email()`
- `test_users_cannot_authenticate_with_nonexistent_username()`
- `test_identifier_is_required()`
- `test_password_is_required()`
- `test_users_can_authenticate_with_remember_me()`
- `test_users_can_authenticate_without_remember_me()`
- `test_users_are_redirected_to_intended_page_after_login()`
- `test_session_is_regenerated_after_login()`
- `test_login_is_rate_limited()`
- `test_rate_limiting_is_cleared_after_successful_login()`
- `test_deleted_users_cannot_authenticate()`
- `test_authentication_works_with_various_identifier_formats()`
- `test_authentication_handles_special_characters()`

#### `tests/Feature/Auth/LogoutTest.php`
Tests pour la déconnexion des utilisateurs :

- **Fonctionnalités** : Déconnexion réussie, invalidation de session, régénération du token CSRF
- **Sécurité** : Gestion des utilisateurs non connectés, déconnexions multiples
- **Robustesse** : Sessions expirées, déconnexion depuis différentes pages

**Méthodes de test :**
- `test_users_can_logout()`
- `test_guest_users_cannot_logout()`
- `test_session_is_invalidated_after_logout()`
- `test_csrf_token_is_regenerated_after_logout()`
- `test_logout_clears_remember_token()`
- `test_user_is_redirected_to_home_after_logout()`
- `test_logout_works_from_any_page()`
- `test_multiple_logout_requests_are_handled_gracefully()`
- `test_logout_with_expired_session()`

#### `tests/Feature/Auth/MiddlewareTest.php`
Tests pour les middlewares d'authentification :

- **Middleware auth** : Protection des routes, redirection des utilisateurs non connectés
- **Middleware guest** : Redirection des utilisateurs connectés, accès aux routes publiques
- **Fonctionnalités** : URL de destination, protection des routes POST/GET

**Méthodes de test :**
- `test_auth_middleware_redirects_guest_users()`
- `test_auth_middleware_allows_authenticated_users()`
- `test_guest_middleware_redirects_authenticated_users()`
- `test_guest_middleware_allows_guest_users()`
- `test_guest_middleware_allows_access_to_register_page()`
- `test_guest_middleware_redirects_from_register_page()`
- `test_guest_middleware_allows_access_to_forgot_password_page()`
- `test_guest_middleware_redirects_from_forgot_password_page()`
- `test_auth_middleware_protects_routes()`
- `test_auth_middleware_allows_access_to_protected_routes()`
- `test_auth_middleware_redirects_with_intended_url()`
- `test_auth_middleware_redirects_to_intended_url_after_login()`
- `test_auth_middleware_redirects_to_home_if_no_intended_url()`
- `test_guest_middleware_allows_access_to_public_routes()`
- `test_guest_middleware_allows_access_to_auth_routes()`
- `test_auth_middleware_protects_logout_route()`
- `test_auth_middleware_allows_access_to_logout_route()`
- `test_auth_middleware_protects_email_verification_routes()`
- `test_auth_middleware_allows_access_to_email_verification_routes()`
- `test_auth_middleware_protects_post_routes()`
- `test_auth_middleware_allows_access_to_post_routes()`

### 2. Tests Unitaires - Formulaires de validation

#### `tests/Unit/Auth/LoginRequestTest.php`
Tests unitaires pour la classe `LoginRequest` :

- **Validation** : Règles de validation, messages d'erreur
- **Authentification** : Méthode `authenticate()`, gestion des identifiants
- **Sécurité** : Rate limiting, clé de throttling
- **Robustesse** : Différents formats d'identifiants, utilisateurs supprimés

**Méthodes de test :**
- `test_validation_passes_with_valid_data()`
- `test_validation_fails_without_identifier()`
- `test_validation_fails_without_password()`
- `test_authenticate_succeeds_with_email()`
- `test_authenticate_succeeds_with_username()`
- `test_authenticate_fails_with_invalid_credentials()`
- `test_authenticate_fails_with_nonexistent_email()`
- `test_authenticate_fails_with_nonexistent_username()`
- `test_authenticate_with_remember_me()`
- `test_authenticate_without_remember_me()`
- `test_rate_limiting_on_failed_authentication()`
- `test_rate_limiting_cleared_after_successful_authentication()`
- `test_throttle_key_generation()`
- `test_authenticate_fails_with_deleted_user()`
- `test_authenticate_with_case_insensitive_email()`
- `test_authenticate_with_various_identifier_formats()`
- `test_authorize_method_returns_true()`

#### `tests/Unit/Auth/RegisterRequestTest.php`
Tests unitaires pour la classe `RegisterRequest` :

- **Validation** : Règles de validation, messages d'erreur personnalisés
- **Robustesse** : Caractères spéciaux, longueurs limites, formats d'email
- **Sécurité** : Complexité des mots de passe, unicité des emails

**Méthodes de test :**
- `test_validation_passes_with_valid_data()`
- `test_validation_fails_without_name()`
- `test_validation_fails_without_email()`
- `test_validation_fails_without_password()`
- `test_validation_fails_with_name_too_long()`
- `test_validation_fails_with_invalid_email_format()`
- `test_validation_fails_with_duplicate_email()`
- `test_validation_fails_with_password_confirmation_mismatch()`
- `test_validation_fails_with_weak_password()`
- `test_email_is_converted_to_lowercase()`
- `test_custom_error_messages()`
- `test_validation_passes_with_special_characters_in_name()`
- `test_validation_passes_with_special_characters_in_email()`
- `test_validation_passes_with_complex_password()`
- `test_validation_passes_with_short_name()`
- `test_validation_passes_with_name_at_limit()`
- `test_validation_passes_with_long_email()`
- `test_authorize_method_returns_true()`
- `test_validation_fails_with_empty_data()`
- `test_validation_passes_with_spaces_in_name()`
- `test_validation_passes_with_numbers_in_name()`

## Couverture des tests

### Fonctionnalités testées

✅ **Inscription**
- Validation des données
- Création d'utilisateur
- Connexion automatique
- Événements déclenchés
- Valeurs par défaut

✅ **Connexion**
- Authentification par email/pseudo
- Gestion des identifiants invalides
- Rate limiting
- "Se souvenir de moi"
- Redirection intended

✅ **Déconnexion**
- Invalidation de session
- Régénération du token CSRF
- Redirection

✅ **Middlewares**
- Protection des routes
- Redirection des utilisateurs
- Gestion des URL de destination

✅ **Validation**
- Règles de validation
- Messages d'erreur personnalisés
- Gestion des cas limites

### Sécurité testée

✅ **Rate limiting** : Limitation des tentatives de connexion
✅ **Session management** : Régénération de session, invalidation
✅ **CSRF protection** : Régénération des tokens
✅ **Input validation** : Validation stricte des entrées
✅ **Password security** : Hashage, complexité requise
✅ **Access control** : Protection des routes sensibles

## Exécution des tests

### Tous les tests d'authentification
```bash
php artisan test tests/Feature/Auth/ tests/Unit/Auth/
```

### Tests Feature uniquement
```bash
php artisan test tests/Feature/Auth/
```

### Tests Unitaires uniquement
```bash
php artisan test tests/Unit/Auth/
```

### Tests spécifiques
```bash
# Tests d'inscription
php artisan test tests/Feature/Auth/RegistrationTest.php

# Tests de connexion
php artisan test tests/Feature/Auth/LoginTest.php

# Tests de déconnexion
php artisan test tests/Feature/Auth/LogoutTest.php

# Tests des middlewares
php artisan test tests/Feature/Auth/MiddlewareTest.php
```

## Statistiques

- **Tests Feature** : 4 fichiers, ~60 méthodes de test
- **Tests Unitaires** : 2 fichiers, ~40 méthodes de test
- **Total** : 6 fichiers, ~100 méthodes de test
- **Couverture** : Inscription, connexion, déconnexion, validation, middlewares

## Prochaines étapes

Les tests de haute priorité sont maintenant en place. Les prochaines étapes recommandées :

1. **Tests de réinitialisation de mot de passe** (moyenne priorité)
2. **Tests de vérification d'email** (moyenne priorité)
3. **Tests d'intégration des scénarios complets** (moyenne priorité)
4. **Tests de sécurité avancés** (basse priorité)
5. **Tests de performance** (basse priorité)

## Maintenance

- Les tests utilisent `RefreshDatabase` pour garantir l'isolation
- Les tests sont documentés avec des commentaires clairs
- Les assertions sont spécifiques et vérifient le comportement attendu
- Les tests couvrent les cas d'erreur et les cas limites
- Les tests suivent les conventions Laravel et PHPUnit 