# 🚀 Plan d'Implémentation - Système Pages/Sections

## 📋 Vue d'ensemble

Ce document détaille le plan d'implémentation complet du système de pages et sections modulaire pour KrosmozJDR.

**Durée estimée** : 2-3 semaines (selon la complexité des templates)

---

## 🎯 Objectifs

1. ✅ Système de rendu dynamique des pages avec leurs sections
2. ✅ Menu dynamique construit depuis les pages publiées
3. ✅ Éditeur de sections avec drag & drop
4. ✅ Système extensible de templates de sections
5. ✅ Gestion complète des états et visibilité

---

## 📦 Phase 1 : Backend - Fondations et améliorations

### 1.1 Créer les Enums PHP 8.1+

**Fichiers à créer :**
- `app/Enums/PageState.php`
- `app/Enums/Visibility.php`
- `app/Enums/SectionType.php`

**Exemple :**
```php
// app/Enums/PageState.php
namespace App\Enums;

enum PageState: string {
    case DRAFT = 'draft';
    case PREVIEW = 'preview';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
    
    public function label(): string {
        return match($this) {
            self::DRAFT => 'Brouillon',
            self::PREVIEW => 'Prévisualisation',
            self::PUBLISHED => 'Publié',
            self::ARCHIVED => 'Archivé',
        };
    }
}
```

**Actions :**
- [ ] Créer les 3 enums
- [ ] Mettre à jour les modèles pour utiliser les enums
- [ ] Créer une migration pour convertir les données existantes (si nécessaire)
- [ ] Mettre à jour les FormRequests

**Temps estimé :** 2-3h

---

### 1.2 Ajouter les Scopes Eloquent

**Fichier :** `app/Models/Page.php`

**Scopes à ajouter :**
```php
public function scopePublished($query) {
    return $query->where('state', PageState::PUBLISHED->value);
}

public function scopeInMenu($query) {
    return $query->where('in_menu', true);
}

public function scopeVisibleFor($query, ?User $user = null) {
    // Logique selon le rôle de l'utilisateur
}

public function scopeOrdered($query) {
    return $query->orderBy('menu_order');
}

public function scopeForMenu($query, ?User $user = null) {
    return $query->published()
        ->inMenu()
        ->visibleFor($user)
        ->ordered();
}
```

**Actions :**
- [ ] Ajouter les scopes dans `Page.php`
- [ ] Ajouter les scopes dans `Section.php`
- [ ] Tester les scopes avec des données de test

**Temps estimé :** 1-2h

---

### 1.3 Créer PageService

**Fichier :** `app/Services/PageService.php`

**Méthodes :**
```php
class PageService {
    public function getMenuPages(?User $user = null): Collection
    public function buildMenuTree(Collection $pages): array
    public function canViewPage(Page $page, ?User $user = null): bool
    public function getPublishedSections(Page $page, ?User $user = null): Collection
}
```

**Actions :**
- [ ] Créer le service
- [ ] Implémenter les méthodes
- [ ] Tester avec des données de test
- [ ] Documenter le service

**Temps estimé :** 2-3h

---

### 1.4 Ajouter les méthodes helper

**Fichiers :** `app/Models/Page.php`, `app/Models/Section.php`

**Méthodes :**
```php
// Page.php
public function isPublished(): bool
public function isVisibleFor(?User $user = null): bool
public function canBeViewedBy(?User $user = null): bool
public function publish(): void
public function archive(): void

// Section.php
public function isPublished(): bool
public function isVisibleFor(?User $user = null): bool
```

**Actions :**
- [ ] Ajouter les méthodes dans les modèles
- [ ] Tester les méthodes
- [ ] Utiliser dans les controllers

**Temps estimé :** 1-2h

---

### 1.5 Améliorer la validation des sections

**Fichier :** `app/Http/Requests/StoreSectionRequest.php`

**Améliorations :**
- Validation dynamique des `params` selon le `type`
- Liste des types autorisés
- Validation stricte des champs requis par type

**Actions :**
- [ ] Créer des règles de validation par type
- [ ] Mettre à jour `StoreSectionRequest`
- [ ] Mettre à jour `UpdateSectionRequest`
- [ ] Tester avec différents types

**Temps estimé :** 2-3h

---

### 1.6 Ajouter endpoint pour le menu

**Fichier :** `app/Http/Controllers/PageController.php`

**Nouvelle méthode :**
```php
public function menu() {
    $pages = PageService::getMenuPages(auth()->user());
    $menuTree = PageService::buildMenuTree($pages);
    return response()->json($menuTree);
}
```

**Route :**
```php
Route::get('/menu', [PageController::class, 'menu'])->name('menu');
```

**Actions :**
- [ ] Ajouter la méthode dans le controller
- [ ] Ajouter la route
- [ ] Tester l'endpoint

**Temps estimé :** 1h

---

**Total Phase 1 :** 9-14h

---

## 🎨 Phase 2 : Frontend - Rendu dynamique

### 2.1 Créer le système de registry des types de sections

**Fichier :** `resources/js/Utils/sections/sectionRegistry.js`

**Contenu :**
```javascript
const sectionTypes = {
  text: () => import('@/Pages/Organismes/section/templates/TextSection.vue'),
  image: () => import('@/Pages/Organismes/section/templates/ImageSection.vue'),
  // ... autres types
}

export function getSectionComponent(type) {
  return sectionTypes[type] || null
}

export function isSectionTypeValid(type) {
  return type in sectionTypes
}
```

**Actions :**
- [ ] Créer le fichier registry
- [ ] Documenter l'ajout de nouveaux types
- [ ] Tester le système

**Temps estimé :** 1h

---

### 2.2 Créer SectionRenderer

**Fichier :** `resources/js/Pages/Organismes/section/SectionRenderer.vue`

**Fonctionnalités :**
- Charge dynamiquement le template selon le type
- Passe les `params` au template
- Gère les erreurs (type invalide, template manquant)
- Affiche un fallback si le type n'existe pas

**Actions :**
- [ ] Créer le composant
- [ ] Implémenter le chargement dynamique
- [ ] Gérer les erreurs
- [ ] Tester avec différents types

**Temps estimé :** 2-3h

---

### 2.3 Créer le premier template : TextSection

**Fichier :** `resources/js/Pages/Organismes/section/templates/TextSection.vue`

**Props :**
- `params` : `{ content: string, align?: string, size?: string }`

**Fonctionnalités :**
- Affiche le contenu texte
- Support du HTML (sanitized)
- Options d'alignement et de taille

**Actions :**
- [ ] Créer le template
- [ ] Implémenter l'affichage
- [ ] Ajouter les styles
- [ ] Tester avec différents contenus

**Temps estimé :** 2h

---

### 2.4 Créer PageRenderer

**Fichier :** `resources/js/Pages/Organismes/page/PageRenderer.vue`

**Fonctionnalités :**
- Affiche le titre de la page
- Rend toutes les sections publiées et visibles
- Gère l'ordre des sections
- Affiche un message si aucune section

**Actions :**
- [ ] Créer le composant
- [ ] Intégrer `SectionRenderer`
- [ ] Gérer l'ordre des sections
- [ ] Ajouter les styles

**Temps estimé :** 2-3h

---

### 2.5 Mettre à jour Show.vue

**Fichier :** `resources/js/Pages/Pages/page/Show.vue`

**Modifications :**
- Utiliser `PageRenderer` pour afficher la page
- Gérer les permissions (bouton éditer si autorisé)
- Afficher les métadonnées (auteur, date, etc.)

**Actions :**
- [ ] Mettre à jour le template
- [ ] Intégrer `PageRenderer`
- [ ] Ajouter les actions (éditer, etc.)
- [ ] Tester l'affichage

**Temps estimé :** 1-2h

---

**Total Phase 2 :** 8-11h

---

## 🍔 Phase 3 : Menu dynamique

### 3.1 Créer usePageMenu composable

**Fichier :** `resources/js/Composables/page/usePageMenu.js`

**Fonctionnalités :**
- Récupère les pages du menu depuis l'API
- Construit l'arborescence (parent/children)
- Gère le cache (optionnel)
- Format pour le composant Menu

**Actions :**
- [ ] Créer le composable
- [ ] Implémenter la récupération des pages
- [ ] Construire l'arborescence
- [ ] Tester avec différentes structures

**Temps estimé :** 2-3h

---

### 3.2 Créer PageMenu

**Fichier :** `resources/js/Pages/Organismes/page/PageMenu.vue`

**Fonctionnalités :**
- Affiche le menu hiérarchique
- Support des dropdowns (parent/children)
- Indique la page active
- Style cohérent avec le design system

**Actions :**
- [ ] Créer le composant
- [ ] Utiliser `usePageMenu`
- [ ] Implémenter l'affichage hiérarchique
- [ ] Ajouter les styles

**Temps estimé :** 2-3h

---

### 3.3 Intégrer dans Aside.vue

**Fichier :** `resources/js/Pages/Layouts/Aside.vue`

**Modifications :**
- Ajouter `PageMenu` dans le menu principal
- Gérer l'ordre (avant/après les autres items)
- Gérer l'état ouvert/fermé

**Actions :**
- [ ] Intégrer `PageMenu`
- [ ] Tester l'affichage
- [ ] Ajuster les styles si nécessaire

**Temps estimé :** 1h

---

**Total Phase 3 :** 5-7h

---

## ✏️ Phase 4 : Éditeur de sections

### 4.1 Créer l'interface d'édition de sections

**Fichier :** `resources/js/Pages/Pages/page/Edit.vue`

**Fonctionnalités :**
- Liste des sections de la page
- Bouton pour ajouter une section
- Drag & drop pour réordonner
- Boutons éditer/supprimer par section

**Actions :**
- [ ] Créer l'interface
- [ ] Intégrer le drag & drop
- [ ] Ajouter les actions
- [ ] Tester l'interface

**Temps estimé :** 3-4h

---

### 4.2 Créer le formulaire dynamique de section

**Fichier :** `resources/js/Pages/Organismes/section/SectionForm.vue`

**Fonctionnalités :**
- Formulaire dynamique selon le type
- Validation côté client
- Prévisualisation en temps réel
- Gestion des params JSON

**Actions :**
- [ ] Créer le composant
- [ ] Implémenter les formulaires par type
- [ ] Ajouter la validation
- [ ] Tester avec différents types

**Temps estimé :** 4-5h

---

### 4.3 Ajouter la gestion des états

**Fichier :** `resources/js/Pages/Pages/page/Edit.vue`

**Fonctionnalités :**
- Sélecteur d'état (draft, preview, published, archived)
- Workflow de transitions
- Badges visuels pour les états
- Actions (publier, archiver, etc.)

**Actions :**
- [ ] Ajouter le sélecteur d'état
- [ ] Implémenter les transitions
- [ ] Ajouter les badges
- [ ] Tester les transitions

**Temps estimé :** 2-3h

---

**Total Phase 4 :** 9-12h

---

## 🎨 Phase 5 : Templates supplémentaires

### 5.1 ImageSection

**Fichier :** `resources/js/Pages/Organismes/section/templates/ImageSection.vue`

**Params :**
```json
{
  "src": "string",
  "alt": "string",
  "caption": "string?",
  "align": "left|center|right",
  "size": "sm|md|lg|xl|full"
}
```

**Temps estimé :** 2h

---

### 5.2 GallerySection

**Fichier :** `resources/js/Pages/Organismes/section/templates/GallerySection.vue`

**Params :**
```json
{
  "images": [
    { "src": "string", "alt": "string", "caption": "string?" }
  ],
  "columns": 2|3|4,
  "gap": "sm|md|lg"
}
```

**Temps estimé :** 3h

---

### 5.3 VideoSection

**Fichier :** `resources/js/Pages/Organismes/section/templates/VideoSection.vue`

**Params :**
```json
{
  "src": "string",
  "type": "youtube|vimeo|direct",
  "autoplay": boolean,
  "controls": boolean
}
```

**Temps estimé :** 2h

---

**Total Phase 5 :** 7h (selon le nombre de templates)

---

## ⚡ Phase 6 : Optimisations

### 6.1 Cache

**Backend :**
- Cache Redis pour les pages du menu
- Cache des sections publiées
- Invalidation lors des modifications

**Temps estimé :** 2-3h

---

### 6.2 Lazy loading

**Frontend :**
- Lazy loading des templates de sections
- Lazy loading des images dans les sections
- Intersection Observer pour les sections

**Temps estimé :** 2-3h

---

**Total Phase 6 :** 4-6h

---

## 📚 Phase 7 : Documentation

### 7.1 Documenter les types de sections

**Fichier :** `docs/20-Content/SECTION_TYPES.md`

**Contenu :**
- Liste des types disponibles
- Structure des params pour chaque type
- Exemples d'utilisation
- Guide de création de nouveaux types

**Temps estimé :** 2-3h

---

### 7.2 Guide d'utilisation

**Fichier :** `docs/20-Content/PAGES_SECTIONS_GUIDE.md`

**Contenu :**
- Comment créer une page
- Comment ajouter des sections
- Comment gérer les états
- Comment créer un nouveau type de section

**Temps estimé :** 2-3h

---

**Total Phase 7 :** 4-6h

---

## 📊 Résumé

| Phase | Description | Temps estimé |
|-------|------------|--------------|
| **Phase 1** | Backend - Fondations | 9-14h |
| **Phase 2** | Frontend - Rendu dynamique | 8-11h |
| **Phase 3** | Menu dynamique | 5-7h |
| **Phase 4** | Éditeur de sections | 9-12h |
| **Phase 5** | Templates supplémentaires | 7h+ |
| **Phase 6** | Optimisations | 4-6h |
| **Phase 7** | Documentation | 4-6h |
| **TOTAL** | | **46-63h** |

**Estimation :** 2-3 semaines de développement (selon disponibilité)

---

## 🎯 Priorités

### Critique (Doit être fait en premier)
1. ✅ Phase 1 : Backend - Fondations
2. ✅ Phase 2 : Frontend - Rendu dynamique (au moins TextSection)

### Haute (Important pour l'utilisation)
3. ✅ Phase 3 : Menu dynamique
4. ✅ Phase 4 : Éditeur de sections

### Moyenne (Améliore l'expérience)
5. ✅ Phase 5 : Templates supplémentaires
6. ✅ Phase 7 : Documentation

### Basse (Optimisations)
7. ✅ Phase 6 : Optimisations

---

## ✅ Checklist de démarrage

Avant de commencer :
- [ ] Valider le plan avec l'équipe
- [ ] Vérifier la version PHP (8.1+ pour les enums)
- [ ] Vérifier les données existantes (migration nécessaire ?)
- [ ] Configurer le cache (Redis/Memcached)
- [ ] Préparer l'environnement de test

---

*Plan créé le : {{ date('Y-m-d H:i:s') }}*
*Système Pages/Sections - Plan d'implémentation*

