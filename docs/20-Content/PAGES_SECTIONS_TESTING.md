# Guide de test pour les composants migrés

## Vue d'ensemble

Ce guide liste les tests à effectuer pour vérifier que tous les composants migrés fonctionnent correctement avec la nouvelle architecture.

## Checklist de test

### 1. Création de section

- [ ] **Modal de création** : Ouvrir le modal de création de section
- [ ] **Sélection de template** : Sélectionner un template (text, image, gallery, video, entity_table)
- [ ] **Création directe** : Vérifier que la section se crée directement sans modal de paramètres
- [ ] **Mode édition automatique** : Vérifier que la nouvelle section s'ouvre en mode édition
- [ ] **Valeurs par défaut** : Vérifier que les valeurs par défaut sont correctement appliquées

**Tests à effectuer** :
1. Créer une section "text" → Vérifier qu'elle apparaît avec `content: ''`
2. Créer une section "image" → Vérifier qu'elle apparaît avec `src: '', alt: '', caption: ''`
3. Créer une section "video" → Vérifier qu'elle apparaît avec `src: '', type: 'youtube'`
4. Créer une section "gallery" → Vérifier qu'elle apparaît avec `images: []`
5. Créer une section "entity_table" → Vérifier qu'elle apparaît avec `entity: ''`

### 2. Affichage des sections

- [ ] **SectionRenderer** : Vérifier l'affichage correct de chaque type de section
- [ ] **Badges d'état** : Vérifier que les badges s'affichent avec les bonnes couleurs
  - Draft → Badge warning (jaune)
  - Preview → Badge info (bleu)
  - Published → Badge success (vert)
  - Archived → Badge neutral (gris)
- [ ] **Icônes de template** : Vérifier que les icônes s'affichent correctement
- [ ] **Classes CSS** : Vérifier que les classes CSS sont appliquées selon l'état et le template

**Tests à effectuer** :
1. Afficher une section en mode lecture → Vérifier le template Read
2. Afficher une section en mode édition → Vérifier le template Edit
3. Vérifier les classes CSS sur le conteneur de section
4. Vérifier l'affichage des badges d'état

### 3. Édition de section

- [ ] **Mode édition** : Basculer en mode édition depuis le header
- [ ] **Auto-save** : Modifier le contenu et vérifier l'auto-save (debounce 500ms)
- [ ] **Templates Edit** : Vérifier que tous les templates Edit fonctionnent
  - SectionTextEdit : Éditeur de texte riche
  - SectionImageEdit : Formulaire image
  - SectionVideoEdit : Formulaire vidéo
  - SectionGalleryEdit : Éditeur de galerie
  - SectionEntityTableEdit : Formulaire tableau d'entités

**Tests à effectuer** :
1. Modifier le contenu d'une section text → Vérifier l'auto-save après 500ms
2. Modifier les données d'une section image → Vérifier l'auto-save
3. Basculer entre mode lecture/édition → Vérifier la transition
4. Vérifier que les modifications sont persistées après rechargement

### 4. Paramètres de section

- [ ] **Modal de paramètres** : Ouvrir le modal depuis le header
- [ ] **Settings uniquement** : Vérifier que le modal gère uniquement les settings (pas le contenu)
- [ ] **Validation** : Vérifier la validation des paramètres
- [ ] **Suppression** : Vérifier la suppression avec confirmation

**Tests à effectuer** :
1. Ouvrir le modal de paramètres → Vérifier l'affichage
2. Modifier les settings (align, size, etc.) → Vérifier la sauvegarde
3. Tenter de supprimer une section → Vérifier le modal de confirmation
4. Confirmer la suppression → Vérifier que la section est supprimée

### 5. Réorganisation (drag & drop)

- [ ] **Drag & drop** : Déplacer une section dans la liste
- [ ] **Auto-save** : Vérifier que l'ordre est sauvegardé automatiquement après 500ms
- [ ] **Bouton de sauvegarde** : Vérifier l'affichage du bouton "Enregistrer l'ordre"
- [ ] **Badges et icônes** : Vérifier l'affichage des badges d'état et icônes de template

**Tests à effectuer** :
1. Déplacer une section dans PageSectionEditor → Vérifier le drag & drop
2. Attendre 500ms → Vérifier l'auto-save
3. Vérifier l'affichage des badges d'état dans la liste
4. Vérifier l'affichage des icônes de template dans la liste

### 6. Styles dynamiques

- [ ] **SectionTextRead** : Vérifier les classes d'alignement et de taille
- [ ] **SectionImageRead** : Vérifier les classes d'alignement et de taille d'image
- [ ] **SectionGalleryRead** : Vérifier les classes de colonnes et d'espacement

**Tests à effectuer** :
1. Modifier l'alignement d'une section text → Vérifier l'application des classes
2. Modifier la taille d'une section text → Vérifier l'application des classes
3. Modifier les colonnes d'une galerie → Vérifier l'application des classes
4. Modifier l'espacement d'une galerie → Vérifier l'application des classes

### 7. Permissions

- [ ] **CanEdit** : Vérifier l'affichage des boutons selon les permissions
- [ ] **CanDelete** : Vérifier l'affichage du bouton de suppression
- [ ] **Mode édition** : Vérifier que le mode édition n'est disponible que si canEdit

**Tests à effectuer** :
1. Connecter un utilisateur avec droits limités → Vérifier les permissions
2. Vérifier que les boutons d'édition ne s'affichent pas si canEdit = false
3. Vérifier que le mode édition ne peut pas être activé si canEdit = false

### 8. Métadonnées et URL

- [ ] **URL de section** : Vérifier la génération de l'URL
- [ ] **Métadonnées** : Vérifier l'accès aux métadonnées (createdAt, updatedAt, etc.)
- [ ] **hasContent** : Vérifier la détection de contenu
- [ ] **isEmpty** : Vérifier la détection de section vide

**Tests à effectuer** :
1. Vérifier l'URL générée pour une section
2. Vérifier l'accès aux métadonnées via `useSectionUI`
3. Vérifier que `hasContent` retourne true pour une section avec contenu
4. Vérifier que `isEmpty` retourne true pour une section vide

## Tests d'intégration

### Scénario 1 : Création et édition complète

1. Créer une nouvelle section "text"
2. Vérifier qu'elle s'ouvre en mode édition
3. Ajouter du contenu
4. Vérifier l'auto-save
5. Basculer en mode lecture
6. Vérifier l'affichage du contenu
7. Modifier les paramètres (align, size)
8. Vérifier la sauvegarde
9. Vérifier l'affichage avec les nouveaux paramètres

### Scénario 2 : Réorganisation et suppression

1. Créer plusieurs sections
2. Réorganiser les sections par drag & drop
3. Vérifier l'auto-save de l'ordre
4. Ouvrir le modal de paramètres d'une section
5. Supprimer la section avec confirmation
6. Vérifier que la section est supprimée
7. Vérifier que l'ordre des autres sections est préservé

### Scénario 3 : Multi-templates

1. Créer une section de chaque type (text, image, gallery, video, entity_table)
2. Vérifier l'affichage correct de chaque type
3. Vérifier les badges d'état pour chaque section
4. Vérifier les icônes de template pour chaque section
5. Vérifier l'édition de chaque type
6. Vérifier l'auto-save pour chaque type

## Tests de régression

### Vérifier que rien n'est cassé

- [ ] **Anciennes fonctionnalités** : Vérifier que toutes les anciennes fonctionnalités fonctionnent toujours
- [ ] **Compatibilité** : Vérifier la compatibilité avec les données existantes
- [ ] **Performance** : Vérifier qu'il n'y a pas de régression de performance
- [ ] **Erreurs console** : Vérifier qu'il n'y a pas d'erreurs dans la console

## Points d'attention

### 1. Auto-save

- Vérifier que l'auto-save fonctionne avec le debounce de 500ms
- Vérifier qu'il n'y a pas de sauvegardes multiples inutiles
- Vérifier que l'auto-save fonctionne pour tous les templates

### 2. Mode édition automatique

- Vérifier que les nouvelles sections s'ouvrent en mode édition
- Vérifier que le mode édition peut être désactivé
- Vérifier que le mode édition est préservé lors du rechargement (si nécessaire)

### 3. Classes CSS

- Vérifier que les classes CSS sont correctement appliquées
- Vérifier que les classes personnalisées (settings.classes) sont appliquées
- Vérifier que les classes ne se chevauchent pas

### 4. Badges et icônes

- Vérifier que les badges s'affichent avec les bonnes couleurs
- Vérifier que les icônes s'affichent correctement
- Vérifier que les labels sont corrects

## Résolution des problèmes

### Problème : La section ne s'ouvre pas en mode édition

**Solution** : Vérifier que `autoEdit` est passé à `SectionRenderer` et que `useSectionMode` fonctionne correctement.

### Problème : L'auto-save ne fonctionne pas

**Solution** : Vérifier que `useSectionSave` est utilisé et que le debounce est correctement configuré.

### Problème : Les classes CSS ne s'appliquent pas

**Solution** : Vérifier que `useSectionStyles` est utilisé et que les settings sont correctement passés.

### Problème : Les badges/icônes ne s'affichent pas

**Solution** : Vérifier que `useSectionUI` est utilisé et que les données sont correctement adaptées.

## Rapport de test

Après avoir effectué tous les tests, créer un rapport avec :
- ✅ Tests réussis
- ❌ Tests échoués
- ⚠️ Tests avec warnings
- 📝 Notes et observations

## Support

Pour toute question ou problème lors des tests :
- Consulter `docs/20-Content/PAGES_SECTIONS_ARCHITECTURE.md`
- Consulter `docs/20-Content/PAGES_SECTIONS_MIGRATION.md`
- Consulter `docs/20-Content/PAGES_SECTIONS_COMPOSABLES.md`

