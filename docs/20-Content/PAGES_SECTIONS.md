# Système de Pages et Sections - KrosmozJDR

## Vue d'ensemble

Le système de pages et sections permet de créer des pages dynamiques modulaires composées de blocs de contenu réutilisables. Chaque page peut contenir plusieurs sections ordonnées, chaque section utilisant un template spécifique pour afficher son contenu.

---

## Architecture

### Modèle de données

#### Table `pages`
- `id` : Identifiant unique
- `title` : Titre de la page
- `slug` : URL de la page (unique)
- `read_level` : Niveau minimal requis pour voir la page (entier, basé sur les rôles)
- `write_level` : Niveau minimal requis pour modifier la page (entier, basé sur les rôles)
- `in_menu` : Affichage dans le menu de navigation (booléen)
- `state` : État (raw, draft, playable, archived)
- `parent_id` : Page parente (pour menu hiérarchique, nullable)
- `menu_order` : Ordre dans le menu (entier)
- `created_by` : Utilisateur créateur (nullable)
- `created_at`, `updated_at`, `deleted_at` : Timestamps et soft delete

#### Table `sections`
- `id` : Identifiant unique
- `page_id` : Page parente (foreign key)
- `title` : Titre de la section (optionnel)
- `slug` : Slug de la section pour ancres (optionnel)
- `order` : Ordre d'affichage dans la page (entier)
- `template` : Type de template (text, image, gallery, video, entity_table)
- `settings` : Paramètres de configuration (JSON)
- `data` : Données de contenu (JSON)
- `read_level` : Niveau minimal requis pour voir la section (entier)
- `write_level` : Niveau minimal requis pour modifier la section (entier)
- `state` : État (raw, draft, playable, archived)
- `created_by` : Utilisateur créateur (nullable)
- `created_at`, `updated_at`, `deleted_at` : Timestamps et soft delete

### Relations

**Pages :**
- `sections` : HasMany (sections de la page, ordonnées par `order`)
- `parent` : BelongsTo (page parente, nullable)
- `children` : HasMany (pages enfants)
- `users` : BelongsToMany (utilisateurs associés via `page_user`)
- `campaigns` : BelongsToMany (campagnes associées via `campaign_page`)
- `scenarios` : BelongsToMany (scénarios associés via `scenario_page`)
- `createdBy` : BelongsTo (utilisateur créateur)

**Sections :**
- `page` : BelongsTo (page parente)
- `users` : BelongsToMany (utilisateurs associés via `section_user`)
- `files` : médias attachés via Spatie Media Library (`$section->getMedia('files')`)
- `createdBy` : BelongsTo (utilisateur créateur)

---

## Backend

### Modèles

#### `App\Models\Page`
- **Scopes** :
  - `playable()` : Pages jouables uniquement
  - `inMenu()` : Pages dans le menu
  - `visibleFor(?User $user)` : Pages visibles pour un utilisateur
  - `ordered()` : Triées par `menu_order`
  - `forMenu(?User $user)` : Pages du menu (jouables + dans menu + visibles + ordonnées)

- **Méthodes helper** :
  - `isPlayable()` : Vérifie si la page est jouable
  - `isVisibleFor(?User $user)` : Vérifie la visibilité
  - `canBeViewedBy(?User $user)` : Vérifie si la page peut être vue (état + visibilité)
  - `canBeEditedBy(?User $user)` : Vérifie si la page peut être modifiée
  - `setRaw()`, `setDraft()`, `setPlayable()`, `archive()` : Transitions d'état

#### `App\Models\Section`
- **Scopes** :
  - `playable()` : Sections jouables uniquement
  - `visibleFor(?User $user)` : Sections visibles pour un utilisateur
  - `ordered()` : Triées par `order`
  - `displayable(?User $user)` : Sections affichables (jouables + visibles + ordonnées)

- **Méthodes helper** :
  - `isPlayable()` : Vérifie si la section est jouable
  - `isVisibleFor(?User $user)` : Vérifie la visibilité
  - `canBeViewedBy(?User $user)` : Vérifie si la section peut être vue
  - `canBeEditedBy(?User $user)` : Vérifie si la section peut être modifiée
  - `setRaw()`, `setDraft()`, `setPlayable()`, `archive()` : Transitions d'état

### Services

#### `App\Services\PageService`
- `getMenuPages(?User $user)` : Récupère les pages du menu avec cache
- `buildMenuTree(Collection $pages)` : Construit l'arborescence du menu
- `canViewPage(Page $page, ?User $user)` : Vérifie si une page peut être vue
- `getPlayableSections(Page $page, ?User $user)` : Récupère les sections affichables
- `clearMenuCache(?User $user)` : Invalide le cache du menu

### Controllers

#### `App\Http\Controllers\PageController`
- `index()` : Liste paginée des pages
- `create()` : Formulaire de création
- `store()` : Création d'une page
- `show(Page $page)` : Affichage d'une page avec ses sections
- `edit(Page $page)` : Formulaire d'édition
- `update()` : Mise à jour d'une page
- `delete(Page $page)` : Suppression (soft delete)
- `restore(Page $page)` : Restauration
- `forceDelete(Page $page)` : Suppression définitive
- `menu()` : API pour récupérer le menu (JSON)
- `reorder()` : Réorganisation de l'ordre des pages (drag & drop)

#### `App\Http\Controllers\SectionController`
- `index()` : Liste paginée des sections
- `create()` : Formulaire de création
- `store()` : Création d'une section
- `show(Section $section)` : Affichage d'une section
- `edit(Section $section)` : Formulaire d'édition
- `update()` : Mise à jour d'une section
- `delete(Section $section)` : Suppression (soft delete)
- `restore(Section $section)` : Restauration
- `forceDelete(Section $section)` : Suppression définitive
- `reorder()` : Réorganisation de l'ordre des sections (drag & drop)

### Routes

**Pages :**
- `GET /pages` : Liste des pages
- `GET /pages/menu` : Menu dynamique (JSON)
- `GET /pages/{page:slug}` : Affichage d'une page (public)
- `GET /pages/create` : Formulaire de création (auth)
- `POST /pages` : Création (auth)
- `GET /pages/{page}/edit` : Formulaire d'édition (auth)
- `PATCH /pages/{page}` : Mise à jour (auth)
- `PATCH /pages/reorder` : Réorganisation (auth)
- `DELETE /pages/{page}` : Suppression (auth)

**Sections :**
- `GET /sections` : Liste des sections (auth)
- `GET /sections/create` : Formulaire de création (auth)
- `POST /sections` : Création (auth)
- `GET /sections/{section}` : Affichage (auth)
- `GET /sections/{section}/edit` : Formulaire d'édition (auth)
- `PATCH /sections/{section}` : Mise à jour (auth)
- `PATCH /sections/reorder` : Réorganisation (auth)
- `DELETE /sections/{section}` : Suppression (auth)

---

## Frontend

### Composants de rendu

#### `PageRenderer.vue` (Organisme)
Affiche une page avec ses sections :
- Titre de la page dans le header
- Bouton d'édition à côté du titre (visible seulement si droits d'écriture)
- Rendu des sections via `SectionRenderer`
- Bouton d'ajout de section en bas à droite (mode glass, carré, avec icône) si droits d'écriture
- Modal d'édition de la page (`EditPageModal`)
- Modal de création de section (`CreateSectionModal`)

#### `SectionRenderer.vue` (Organisme)
Rend dynamiquement une section selon son type :
- Charge le template approprié selon `section.template`
- Passe les paramètres (`settings` + `data`) au template
- Au hover : icônes en haut à droite :
  - Copier le lien de la section (avec ancre `#section-{id}`)
  - Paramétrage (ouvre `SectionParamsModal`) si droits d'écriture
  - Édition (ouvre WYSIWYG ou modal selon le type) si droits d'écriture

### Templates de sections

Chaque template est un composant Vue dans `resources/js/Pages/Organismes/section/templates/` :

#### `SectionText.vue`
- **Description** : Section de texte riche avec éditeur WYSIWYG
- **Édition** : Directe via modal de paramètres (WYSIWYG)
- **Paramètres** :
  - `data.html` : Contenu HTML du texte

#### `SectionImage.vue`
- **Description** : Affiche une image unique avec légende optionnelle
- **Édition** : Modal de paramètres
- **Paramètres** :
  - `data.src` : URL de l'image
  - `data.alt` : Texte alternatif
  - `data.caption` : Légende (optionnel)
  - `settings.align` : Alignement (left, center, right)
  - `settings.size` : Taille (sm, md, lg, xl, full)

#### `SectionGallery.vue`
- **Description** : Galerie d'images avec éditeur intégré
- **Édition** : Directe via modal de paramètres
- **Paramètres** :
  - `data.images` : Tableau d'images `[{src, alt, caption?}]`
  - `settings.columns` : Nombre de colonnes (2, 3, 4)
  - `settings.gap` : Espacement (sm, md, lg)

#### `SectionVideo.vue`
- **Description** : Affiche une vidéo (YouTube, Vimeo ou fichier direct)
- **Édition** : Modal de paramètres
- **Paramètres** :
  - `data.src` : URL de la vidéo
  - `settings.type` : Type (youtube, vimeo, direct)
  - `settings.autoplay` : Lecture automatique (booléen)
  - `settings.controls` : Afficher les contrôles (booléen)

#### `SectionEntityTable.vue`
- **Description** : Affiche un tableau d'entités avec filtres et options de tri
- **Édition** : Modal de paramètres
- **Paramètres** :
  - `settings.entity` : Type d'entité à afficher
  - `settings.filters` : Filtres à appliquer (JSON)

### Composants d'édition

#### `EditPageModal.vue`
Modal d'édition d'une page avec deux onglets :
- **Onglet "Général"** :
  - Titre, slug
  - Niveaux `read_level` / `write_level`
  - État (`state`)
  - Page parente (pour menu hiérarchique)
  - Affichage dans le menu, ordre dans le menu
- **Onglet "Sections"** :
  - Liste des sections avec drag & drop pour réordonner
  - Boutons d'édition/suppression par section
  - Utilise `PageSectionEditor`

#### `CreateSectionModal.vue`
Modal de création d'une section :
- Titre optionnel
- Sélection du type de template avec descriptifs
- Ouvre automatiquement le modal de paramètres après sélection
- Après création, ouvre automatiquement en mode édition :
  - Templates `text` et `gallery` : modal de paramètres (WYSIWYG)
  - Autres templates : redirection vers la page d'édition

#### `PageSectionEditor.vue`
Éditeur de sections avec drag & drop :
- Liste des sections de la page
- Drag & drop pour réordonner
- Boutons d'édition/suppression par section
- Sauvegarde l'ordre via `sections.reorder`

#### `SectionParamsModal.vue`
Modal de paramétrage d'une section :
- Formulaire dynamique selon le type de template
- Validation côté client
- Gère les paramètres `settings` et `data`

### Menu dynamique

#### `DynamicMenu.vue` (Organisme)
Affiche le menu dynamique des pages :
- Récupère les pages via `useDynamicMenu` composable
- Affiche l'arborescence (pages parentes/enfants)
- Supporte les menus déroulants
- Indique la page active

#### `useDynamicMenu.js` (Composable)
- Récupère les pages du menu depuis l'API `/pages/menu`
- Construit l'arborescence
- Gère le cache (optionnel)
- Format pour le composant Menu

---

## États et accès

### États (`state`)

**Pages et sections :**
- `raw` : Brut
- `draft` : Brouillon
- `playable` : Jouable (affichable selon `read_level`)
- `archived` : Archivé

**Transitions (exemples) :**
- `raw` → `draft`
- `draft` → `playable`
- `playable` → `archived`
- `archived` → `draft`

### Accès en lecture/écriture

**Niveaux (entiers) :**
- `0` : guest
- `1` : user
- `2` : player
- `3` : game_master
- `4` : admin
- `5` : super_admin

**Logique :**
- Les admins peuvent toujours voir toutes les pages/sections
- Les auteurs peuvent voir leurs pages/sections même en brouillon
- La visibilité est vérifiée via les scopes `visibleFor()` et les méthodes `isVisibleFor()`

---

## Permissions

### Droits d'édition

**Pages :**
- `write_level` : Niveau minimal requis pour modifier
- Les super_admin peuvent toujours modifier
- Les auteurs peuvent modifier leurs pages
- Les utilisateurs associés via `page_user` peuvent modifier

**Sections :**
- `write_level` : Niveau minimal requis pour modifier
- Les super_admin peuvent toujours modifier
- Les auteurs peuvent modifier leurs sections
- Les utilisateurs associés via `section_user` peuvent modifier

### Policies

**PagePolicy :**
- `viewAny()` : Voir la liste (admin/super_admin)
- `view()` : Voir une page (selon visibilité et état)
- `create()` : Créer (admin/super_admin)
- `update()` : Modifier (selon `canBeEditedBy()`)
- `delete()` : Supprimer (admin/super_admin ou utilisateur associé)
- `restore()` : Restaurer (admin/super_admin)
- `forceDelete()` : Supprimer définitivement (admin/super_admin)

**SectionPolicy :**
- Similaire à PagePolicy

---

## Utilisation

### Créer une page

1. Accéder à `/pages/create` (ou cliquer sur "Créer une page")
2. Remplir le formulaire :
   - Titre (génère automatiquement le slug)
   - `read_level` / `write_level`
   - `state`, page parente, affichage dans le menu
3. Sauvegarder
4. La page est créée en état `draft`

### Ajouter une section à une page

1. Sur la page, cliquer sur le bouton d'ajout de section (en bas à droite)
2. Choisir un type de template (avec descriptif)
3. Remplir les paramètres dans le modal
4. La section est créée et ouverte automatiquement en mode édition

### Réordonner les sections

1. Ouvrir le modal d'édition de la page
2. Aller dans l'onglet "Sections"
3. Glisser-déposer les sections pour les réordonner
4. L'ordre est sauvegardé automatiquement

### Réordonner les pages dans le menu

1. Dans le tableau des pages (`/pages`)
2. Glisser-déposer les pages pour les réordonner
3. L'ordre est sauvegardé via `pages.reorder`

### Modifier une section

**Templates avec édition directe (text, gallery) :**
- Cliquer sur l'icône d'édition au hover
- Le modal de paramètres s'ouvre avec l'éditeur WYSIWYG

**Autres templates (image, video, entity_table) :**
- Cliquer sur l'icône d'édition au hover
- Redirection vers la page d'édition de la section

---

## Exemples de paramètres

### Section texte
```json
{
  "data": {
    "html": "<p>Contenu riche avec <strong>formatage</strong>.</p>"
  }
}
```

### Section image
```json
{
  "data": {
    "src": "/storage/images/example.jpg",
    "alt": "Description de l'image",
    "caption": "Légende optionnelle"
  },
  "settings": {
    "align": "center",
    "size": "lg"
  }
}
```

### Section tableau d'entités
```json
{
  "settings": {
    "entity": "classes",
    "filters": {
      "read_level": 0
    }
  }
}
```

---

## Liens utiles

- [Entité Page](../21-Entities/ENTITY_PAGES.md)
- [Entité Section](../21-Entities/ENTITY_SECTIONS.md)
- [Enums SectionType](../../app/Enums/SectionType.php)

