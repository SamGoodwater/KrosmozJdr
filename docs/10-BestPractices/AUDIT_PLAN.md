# Plan d'audit du code - KrosmozJDR

**Date de création** : 2025-01-27  
**Objectif** : Vérifier la qualité, la sécurité et identifier les redondances et mauvaises pratiques dans le code.

---

## 📋 Vue d'ensemble

Ce plan d'audit couvre les aspects suivants :
1. **Sécurité** : Validations, autorisations, injections, XSS, CSRF
2. **Qualité du code** : Redondances, DRY, cohérence
3. **Architecture** : Respect des conventions, structure modulaire
4. **Performance** : Requêtes N+1, optimisations
5. **Maintenabilité** : Documentation, tests, lisibilité

---

## 🔒 1. AUDIT DE SÉCURITÉ

### 1.1 Autorisations et Policies

#### Points à vérifier :
- [ ] **Cohérence des méthodes d'autorisation**
  - Vérifier l'utilisation uniforme de `authorize()` vs `authorizeForUser()`
  - Identifier les incohérences (ex: `authorizeForUser(auth()->user(), ...)` vs `authorize(...)`)
  - Localisation : `app/Http/Controllers/**/*.php`

- [ ] **Vérification des rôles dans les Policies**
  - Détecter les mélanges de formats (constantes, entiers, strings)
  - Exemple problématique : `in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin'])`
  - Localisation : `app/Policies/**/*.php`

- [ ] **Cohérence des règles d'accès**
  - Vérifier que les policies sont bien enregistrées dans `AuthServiceProvider`
  - Vérifier que toutes les routes protégées utilisent les policies
  - Localisation : `app/Providers/AuthServiceProvider.php`, `routes/**/*.php`

- [ ] **Gestion des utilisateurs non authentifiés**
  - Vérifier les policies qui acceptent `?User $user` vs `User $user`
  - S'assurer que les routes publiques sont bien configurées
  - Localisation : `app/Policies/**/*.php`, `routes/**/*.php`

#### Actions recommandées :
- Standardiser sur `authorize()` (plus simple et Laravel gère `auth()->user()` automatiquement)
- Créer une méthode helper dans `User` pour vérifier les rôles : `isAdmin()`, `isSuperAdmin()`, etc.
- Utiliser uniquement les constantes de `User` pour les rôles

---

### 1.2 Validations des entrées

#### Points à vérifier :
- [ ] **FormRequests complètes**
  - Vérifier que toutes les FormRequests ont des règles de validation complètes
  - Identifier les FormRequests vides (ex: `StoreItemRequest`, `UpdateItemRequest`)
  - Localisation : `app/Http/Requests/**/*.php`

- [ ] **Validation côté serveur**
  - Vérifier que toutes les routes POST/PUT/PATCH utilisent des FormRequests
  - Détecter les validations inline dans les controllers (ex: `$request->validate([...])`)
  - Localisation : `app/Http/Controllers/**/*.php`

- [ ] **Validation des types de fichiers**
  - Vérifier les validations d'upload (types MIME, extensions, taille)
  - S'assurer que les validations sont cohérentes entre frontend et backend
  - Localisation : `app/Http/Requests/**/*.php`, `app/Services/FileService.php`, `resources/js/Composables/form/useFileUpload.js`

- [ ] **Validation des relations**
  - Vérifier que les IDs de relations sont validés (existence en base)
  - Exemple à vérifier : `updateResources` dans `ItemController` (ligne 143-161)
  - Localisation : `app/Http/Controllers/**/*.php`

#### Actions recommandées :
- Compléter toutes les FormRequests vides
- Déplacer les validations inline vers des FormRequests dédiées
- Créer des règles de validation réutilisables pour les relations

---

### 1.3 Protection contre les injections

#### Points à vérifier :
- [ ] **Injection SQL**
  - Vérifier l'utilisation de `whereRaw()`, `DB::raw()`, concaténation dans les requêtes
  - S'assurer que toutes les requêtes utilisent les paramètres liés
  - Localisation : `app/**/*.php`

- [ ] **Requêtes LIKE avec paramètres**
  - Vérifier les requêtes avec `LIKE` (ex: `where('name', 'like', "%{$search}%")`)
  - S'assurer que Laravel échappe correctement (normalement oui, mais vérifier)
  - Localisation : `app/Http/Controllers/**/*.php`

- [ ] **XSS (Cross-Site Scripting)**
  - Vérifier l'échappement des données dans les vues (Inertia)
  - S'assurer que les données utilisateur sont échappées
  - Localisation : `resources/js/**/*.vue`, `resources/js/**/*.js`

- [ ] **CSRF Protection**
  - Vérifier que les routes POST/PUT/PATCH/DELETE sont protégées
  - S'assurer que les formulaires frontend incluent les tokens CSRF
  - Localisation : `routes/**/*.php`, `resources/js/**/*.vue`

#### Actions recommandées :
- Utiliser `where()` avec paramètres plutôt que `whereRaw()` quand possible
- Vérifier que Inertia échappe automatiquement (normalement oui)
- Documenter les cas où l'échappement manuel est nécessaire

---

### 1.4 Gestion des fichiers et uploads

#### Points à vérifier :
- [ ] **Validation des fichiers uploadés**
  - Vérifier les validations de type MIME, extensions, taille
  - S'assurer que les validations sont cohérentes entre frontend et backend
  - Localisation : `app/Http/Controllers/ImageController.php`, `app/Services/FileService.php`

- [ ] **Stockage sécurisé**
  - Vérifier que les fichiers sont stockés en dehors du webroot public
  - Vérifier les permissions des fichiers uploadés
  - Localisation : `app/Services/FileService.php`, `config/filesystems.php`

- [ ] **Noms de fichiers**
  - Vérifier que les noms de fichiers sont sanitizés
  - Éviter les noms de fichiers prévisibles
  - Localisation : `app/Services/FileService.php`, `app/Services/ImageService.php`

#### Actions recommandées :
- Centraliser la validation des fichiers dans `FileService`
- Utiliser des noms de fichiers hashés ou UUID
- Vérifier les permissions de stockage

---

## 🔄 2. AUDIT DE QUALITÉ DU CODE

### 2.1 Redondances et DRY (Don't Repeat Yourself)

#### Points à vérifier :
- [ ] **Redondances dans les Policies**
  - Identifier le code dupliqué entre les policies d'entités
  - Exemple : vérification des rôles répétée dans chaque policy
  - Localisation : `app/Policies/Entity/**/*.php`

- [ ] **Redondances dans les Controllers**
  - Identifier les patterns répétés (index, show, store, update, delete)
  - Vérifier si un Controller de base pourrait être créé
  - Localisation : `app/Http/Controllers/Entity/**/*.php`

- [ ] **Redondances dans les FormRequests**
  - Identifier les règles de validation répétées
  - Vérifier si des règles personnalisées pourraient être créées
  - Localisation : `app/Http/Requests/**/*.php`

- [ ] **Redondances dans les Models Frontend**
  - Vérifier les patterns répétés dans les modèles Vue
  - Exemple : `toFormData()` répété dans chaque modèle
  - Localisation : `resources/js/Models/**/*.js`

#### Actions recommandées :
- Créer une `BaseEntityPolicy` avec les méthodes communes
- Créer un `BaseEntityController` si le pattern est vraiment identique
- Créer des règles de validation réutilisables
- Améliorer `BaseModel` pour inclure les méthodes communes

---

### 2.2 Cohérence du code

#### Points à vérifier :
- [ ] **Conventions de nommage**
  - Vérifier le respect des conventions (kebab-case fichiers, PascalCase classes, etc.)
  - Localisation : Tous les fichiers

- [ ] **Structure des méthodes**
  - Vérifier la cohérence de l'ordre des méthodes dans les classes
  - Vérifier la cohérence des docBlocks
  - Localisation : `app/**/*.php`, `resources/js/**/*.js`

- [ ] **Gestion des erreurs**
  - Vérifier la cohérence de la gestion des erreurs (try/catch, exceptions)
  - Localisation : `app/**/*.php`

- [ ] **Retours de méthodes**
  - Vérifier la cohérence des types de retour
  - Vérifier l'utilisation des Resources vs Collections
  - Localisation : `app/Http/Controllers/**/*.php`

#### Actions recommandées :
- Créer un guide de style de code si nécessaire
- Standardiser la gestion des erreurs
- Documenter les patterns de retour

---

### 2.3 Documentation

#### Points à vérifier :
- [ ] **DocBlocks PHP**
  - Vérifier que toutes les méthodes publiques ont des docBlocks
  - Vérifier la qualité des docBlocks (description, paramètres, retour)
  - Localisation : `app/**/*.php`

- [ ] **Documentation JSDoc**
  - Vérifier la documentation des composants Vue et fonctions JS
  - Localisation : `resources/js/**/*.js`, `resources/js/**/*.vue`

- [ ] **Documentation des services**
  - Vérifier la documentation des services complexes (scrapping, etc.)
  - Localisation : `app/Services/**/*.php`

#### Actions recommandées :
- Compléter les docBlocks manquants
- Standardiser le format des docBlocks
- Vérifier que les exemples sont à jour

---

## 🏗️ 3. AUDIT D'ARCHITECTURE

### 3.1 Structure modulaire

#### Points à vérifier :
- [ ] **Respect de l'Atomic Design**
  - Vérifier l'organisation des composants Vue selon Atomic Design
  - Localisation : `resources/js/Pages/**/*.vue`

- [ ] **Séparation des responsabilités**
  - Vérifier que les controllers sont légers (logique dans les services)
  - Vérifier que les services sont bien utilisés
  - Localisation : `app/Http/Controllers/**/*.php`, `app/Services/**/*.php`

- [ ] **Utilisation des Resources**
  - Vérifier que toutes les réponses API utilisent des Resources
  - Localisation : `app/Http/Controllers/**/*.php`, `app/Http/Resources/**/*.php`

#### Actions recommandées :
- Déplacer la logique métier des controllers vers les services
- Vérifier l'utilisation cohérente des Resources

---

### 3.2 Relations et requêtes

#### Points à vérifier :
- [ ] **Requêtes N+1**
  - Identifier les requêtes N+1 dans les controllers
  - Vérifier l'utilisation de `with()` pour eager loading
  - Localisation : `app/Http/Controllers/**/*.php`

- [ ] **Relations Eloquent**
  - Vérifier que toutes les relations sont bien définies dans les models
  - Vérifier la cohérence des noms de relations
  - Localisation : `app/Models/**/*.php`

- [ ] **Scopes et Query Builders**
  - Vérifier l'utilisation des scopes pour les requêtes récurrentes
  - Localisation : `app/Models/**/*.php`

#### Actions recommandées :
- Ajouter `with()` pour éviter les requêtes N+1
- Créer des scopes pour les requêtes complexes récurrentes

---

## ⚡ 4. AUDIT DE PERFORMANCE

### 4.1 Requêtes base de données

#### Points à vérifier :
- [ ] **Pagination**
  - Vérifier que toutes les listes utilisent la pagination
  - Localisation : `app/Http/Controllers/**/*.php`

- [ ] **Index de base de données**
  - Vérifier que les colonnes fréquemment recherchées/triées ont des index
  - Localisation : `database/migrations/**/*.php`

- [ ] **Requêtes optimisées**
  - Identifier les requêtes lourdes (joins multiples, sous-requêtes)
  - Localisation : `app/**/*.php`

#### Actions recommandées :
- Ajouter des index sur les colonnes de recherche/tri
- Optimiser les requêtes lourdes

---

### 4.2 Cache

#### Points à vérifier :
- [ ] **Utilisation du cache**
  - Vérifier l'utilisation du cache pour les données statiques
  - Localisation : `app/Services/**/*.php`

- [ ] **Invalidation du cache**
  - Vérifier que le cache est invalidé lors des mises à jour
  - Localisation : `app/**/*.php`

#### Actions recommandées :
- Implémenter le cache pour les données fréquemment accédées
- Mettre en place l'invalidation automatique

---

## 🧪 5. AUDIT DES TESTS

### 5.1 Couverture de tests

#### Points à vérifier :
- [ ] **Tests unitaires**
  - Vérifier la présence de tests pour les services
  - Localisation : `tests/Unit/**/*.php`

- [ ] **Tests d'intégration**
  - Vérifier la présence de tests pour les controllers
  - Localisation : `tests/Feature/**/*.php`

- [ ] **Tests des Policies**
  - Vérifier la présence de tests pour les policies
  - Localisation : `tests/**/*.php`

#### Actions recommandées :
- Augmenter la couverture de tests
- Créer des tests pour les fonctionnalités critiques

---

## 📝 6. PLAN D'ACTION PRIORITAIRE

### Priorité 1 (Critique - Sécurité)
1. ✅ Standardiser les méthodes d'autorisation
2. ✅ Corriger les vérifications de rôles dans les policies
3. ✅ Compléter les FormRequests vides
4. ✅ Valider les uploads de fichiers

### Priorité 2 (Important - Qualité)
1. ✅ Éliminer les redondances dans les policies
2. ✅ Déplacer les validations inline vers des FormRequests
3. ✅ Vérifier et corriger les requêtes N+1
4. ✅ Compléter la documentation

### Priorité 3 (Amélioration - Performance)
1. ✅ Optimiser les requêtes lourdes
2. ✅ Ajouter des index de base de données
3. ✅ Implémenter le cache où nécessaire

---

## 🔍 OUTILS DE VÉRIFICATION

### Commandes utiles
```bash
# Analyser le code PHP
./vendor/bin/phpstan analyse
./vendor/bin/phpcs --standard=PSR12 app/

# Analyser le code JS
npm run lint

# Vérifier les routes
php artisan route:list

# Vérifier les policies
php artisan route:list --path=entities
```

### Outils recommandés
- **PHPStan** : Analyse statique du code PHP
- **PHP_CodeSniffer** : Vérification des standards de code
- **ESLint** : Analyse du code JavaScript
- **Laravel Debugbar** : Profiling des requêtes

---

## 📊 RAPPORT D'AUDIT

Après l'audit, générer un rapport avec :
- Liste des problèmes identifiés par catégorie
- Priorités et recommandations
- Exemples de code problématique
- Exemples de corrections proposées

---

**Note** : Ce plan d'audit est un document vivant qui peut être mis à jour selon les besoins du projet.

