# Résultats de l'audit initial - KrosmozJDR

**Date** : 2025-01-27  
**Statut** : Audit initial - Problèmes identifiés

---

## 🔴 PROBLÈMES CRITIQUES (Priorité 1)

### 1. Incohérences dans les méthodes d'autorisation

**Problème** : Mélange de `authorizeForUser()` et `authorize()` dans les controllers.

**Exemples** :
```php
// ItemController.php ligne 19
$this->authorizeForUser(auth()->user(), 'viewAny', Item::class);

// ItemController.php ligne 92
$this->authorize('update', $item);
```

**Impact** : Incohérence, code plus verbeux, risque d'erreurs.

**Recommandation** : Standardiser sur `authorize()` qui gère automatiquement `auth()->user()`.

**Fichiers concernés** :
- `app/Http/Controllers/Entity/ItemController.php`
- `app/Http/Controllers/Entity/CreatureController.php`
- `app/Http/Controllers/Entity/NpcController.php`
- `app/Http/Controllers/Entity/MonsterController.php`
- `app/Http/Controllers/Entity/CampaignController.php`
- `app/Http/Controllers/Entity/ScenarioController.php`
- Et probablement d'autres...

---

### 2. Vérifications de rôles incohérentes dans les Policies

**Problème** : Mélange de formats pour vérifier les rôles (constantes, entiers, strings).

**Exemple problématique** :
```php
// ItemPolicy.php ligne 35
return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin']);
```

**Impact** : Code fragile, difficile à maintenir, risque d'erreurs si les constantes changent.

**Recommandation** : 
- Utiliser uniquement les constantes de `User`
- Créer des méthodes helper : `$user->isAdmin()`, `$user->isSuperAdmin()`

**Fichiers concernés** :
- `app/Policies/Entity/ItemPolicy.php`
- `app/Policies/Entity/CapabilityPolicy.php` (utilise seulement strings)
- `app/Policies/PagePolicy.php`
- Probablement toutes les policies d'entités

---

### 3. FormRequests incomplètes ou vides

**Problème** : Plusieurs FormRequests sont vides ou incomplètes.

**Exemple** :
```php
// StoreItemRequest.php
public function authorize(): bool
{
    return false; // ❌ Bloque toutes les créations
}

public function rules(): array
{
    return [
        // ❌ Aucune règle de validation
    ];
}
```

**Impact** : Pas de validation, risque de sécurité, données invalides en base.

**Fichiers concernés** :
- `app/Http/Requests/Entity/StoreItemRequest.php`
- `app/Http/Requests/Entity/UpdateItemRequest.php`
- Probablement d'autres FormRequests d'entités

**Recommandation** : Compléter toutes les FormRequests avec les règles de validation appropriées.

---

### 4. Validations inline dans les controllers

**Problème** : Des validations sont faites directement dans les controllers au lieu d'utiliser des FormRequests.

**Exemple** :
```php
// ItemController.php ligne 137
$request->validate([
    'resources' => 'array',
]);
```

**Impact** : Code dupliqué, moins réutilisable, plus difficile à tester.

**Recommandation** : Créer des FormRequests dédiées pour ces validations.

**Fichiers concernés** :
- `app/Http/Controllers/Entity/ItemController.php` (méthode `updateResources`)
- Probablement d'autres controllers avec des méthodes similaires

---

## 🟡 PROBLÈMES IMPORTANTS (Priorité 2)

### 5. Redondances dans les Policies

**Problème** : Code dupliqué entre les policies d'entités.

**Exemple** : Toutes les policies d'entités ont le même pattern :
```php
public function create(User $user): bool
{
    return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin']);
}

public function update(User $user, $model): bool
{
    return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin']);
}

public function delete(User $user, $model): bool
{
    return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin']);
}
```

**Impact** : Code dupliqué, maintenance difficile, risque d'incohérences.

**Recommandation** : Créer une `BaseEntityPolicy` avec les méthodes communes.

**Fichiers concernés** :
- Toutes les policies dans `app/Policies/Entity/`

---

### 6. Incohérence dans la gestion des utilisateurs non authentifiés

**Problème** : Certaines policies acceptent `?User $user`, d'autres `User $user`.

**Exemple** :
```php
// ItemPolicy.php
public function viewAny(?User $user): bool // ✅ Accepte null
public function view(?User $user, Item $item): bool // ✅ Accepte null
public function create(User $user): bool // ❌ Nécessite User

// PagePolicy.php
public function viewAny(?User $user): bool // ✅ Accepte null
public function view(User $user, Page $page): bool // ❌ Nécessite User
```

**Impact** : Comportement incohérent, erreurs potentielles.

**Recommandation** : Standardiser selon les besoins métier (routes publiques vs protégées).

---

### 7. Requêtes potentiellement non optimisées

**Problème** : Certaines requêtes pourraient être optimisées avec eager loading.

**Exemple** :
```php
// ItemController.php ligne 94
$item->load(['itemType', 'createdBy', 'resources']); // ✅ Bon
```

Mais ailleurs, il pourrait y avoir des requêtes N+1 non détectées.

**Recommandation** : Auditer toutes les requêtes pour identifier les N+1.

---

### 8. Validation des relations dans les controllers

**Problème** : Validation manuelle des IDs de relations dans les controllers.

**Exemple** :
```php
// ItemController.php ligne 151-160
$resourceIds = array_keys($syncData);
$existingResources = \App\Models\Entity\Resource::whereIn('id', $resourceIds)->pluck('id')->toArray();
$invalidIds = array_diff($resourceIds, $existingResources);

if (!empty($invalidIds)) {
    return redirect()->back()
        ->withErrors(['resources' => 'Certaines ressources n\'existent pas.'])
        ->withInput();
}
```

**Impact** : Code dupliqué, logique métier dans les controllers.

**Recommandation** : Utiliser des règles de validation Laravel (`exists:resources,id`) ou créer une FormRequest.

---

## 🟢 AMÉLIORATIONS SUGGÉRÉES (Priorité 3)

### 9. Documentation incomplète

**Problème** : Certaines méthodes n'ont pas de docBlocks ou des docBlocks incomplets.

**Recommandation** : Compléter la documentation selon les standards du projet.

---

### 10. Gestion des erreurs

**Problème** : Pas de standardisation visible dans la gestion des erreurs.

**Recommandation** : Standardiser la gestion des erreurs (exceptions personnalisées, logging).

---

### 11. Tests manquants

**Problème** : Pas de tests visibles pour les nouvelles fonctionnalités.

**Recommandation** : Ajouter des tests pour les fonctionnalités critiques (policies, validations, services).

---

## 📊 RÉSUMÉ PAR CATÉGORIE

### Sécurité
- ✅ **Autorisations** : Incohérences à corriger
- ✅ **Validations** : FormRequests incomplètes
- ✅ **Uploads** : À vérifier en détail

### Qualité du code
- ✅ **Redondances** : Policies, validations
- ✅ **Cohérence** : Méthodes d'autorisation, vérifications de rôles
- ✅ **Documentation** : À compléter

### Architecture
- ✅ **Séparation des responsabilités** : Validations dans controllers
- ✅ **Requêtes** : À optimiser (N+1)

---

## 🎯 PROCHAINES ÉTAPES

1. **Phase 1** : Corriger les problèmes critiques (Priorité 1)
   - Standardiser les autorisations
   - Corriger les vérifications de rôles
   - Compléter les FormRequests

2. **Phase 2** : Améliorer la qualité (Priorité 2)
   - Éliminer les redondances
   - Déplacer les validations
   - Optimiser les requêtes

3. **Phase 3** : Améliorations (Priorité 3)
   - Documentation
   - Tests
   - Performance

---

**Note** : Ce document sera mis à jour au fur et à mesure de l'audit détaillé.

