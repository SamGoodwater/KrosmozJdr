# Système d'entités frontend — Documentation complète

**Version** : 2.0  
**Date** : 2026-01-XX  
**Statut** : ✅ Système en production

---

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Flux de données complet](#flux-de-données-complet)
3. [Architecture en couches](#architecture-en-couches)
4. [Composants principaux](#composants-principaux)
5. [Guides pratiques](#guides-pratiques)

---

## Vue d'ensemble

Le système d'entités frontend de KrosmozJDR transforme les données brutes du backend en interfaces utilisateur complètes (tableaux, vues d'affichage, formulaires d'édition).

### Principe fondamental

**Séparation stricte des responsabilités** :
- **Backend** : Source de vérité pour la sécurité et la validation
- **Frontend** : Gestion de l'UX (affichage, formatage, formulaires) via descriptors déclaratifs

### Formats de sortie

Une entité peut être affichée sous **7 formats différents** :

1. **Tableau** : Liste avec colonnes, tri, filtres, recherche
2. **Vue Large** : Page complète de détail
3. **Vue Compact** : Modal de détail condensé
4. **Vue Minimal** : Carte compacte
5. **Vue Text** : Ligne de texte simple
6. **Édition Large** : Formulaire complet d'édition
7. **Édition Compact** : Formulaire condensé d'édition
8. **QuickEdit** : Panneau d'édition en masse (sélection multiple)

---

## Flux de données complet

### Schéma global

```
Base de données (Backend)
   ↓
API Laravel (JSON)
   ↓
Adapter (createEntityAdapter)
   ↓
Mapper (optionnel, ex: ResourceMapper)
   ↓
Model (BaseModel + entités spécifiques)
   ↓
Formatter (FormatterRegistry)
   ↓
Descriptor (resource-descriptors.js)
   ↓
Config (TableConfig, BulkConfig, FormConfig)
   ↓
Renderer (EntityTanStackTable, EntityModal, EntityQuickEditPanel)
   ↓
Vue (Large, Compact, Minimal, Text, EditLarge, EditCompact, QuickEdit)
```

### Exemple concret : Affichage d'une ressource dans le tableau

```
1. Backend renvoie : { id: 1, name: "Bois", rarity: 2, level: 15 }

2. Adapter transforme :
   createEntityAdapter(Resource, ResourceMapper)
   → { meta: {...}, rows: [{ id: 1, cells: {}, rowParams: { entity: Resource instance } }] }

3. Tableau demande une cellule :
   entity.toCell('rarity', { size: 'md' })

4. BaseModel.toCell() :
   - Vérifie le cache (_cellCache)
   - Appelle getFormatter('rarity') → RarityFormatter
   - Appelle RarityFormatter.toCell(2, { size: 'md' })

5. RarityFormatter.toCell() :
   - Utilise RARITY_GRADIENT depuis SharedConstants
   - Retourne { type: 'badge', value: 'Rare', params: { color: 'success', icon: 'fa-circle' } }

6. Tableau affiche :
   <Badge color="success" icon="fa-circle">Rare</Badge>
```

---

## Architecture en couches

Voir [ARCHITECTURE.md](./ARCHITECTURE.md) pour les détails complets de chaque couche.

### Résumé des 7 couches

1. **Adapter & Mapper** : Transformation backend → frontend
2. **Models** : Logique métier + formatage via `toCell()`
3. **Formatters** : Formatage centralisé (rarity, level, etc.)
4. **Descriptors** : Configuration déclarative (source de vérité UX)
5. **Configs** : Génération de configurations depuis descriptors
6. **Renderers** : Composants Vue génériques (tableau, modal, quickedit)
7. **Vues** : Composants Vue spécifiques (Large, Compact, etc.)

---

## Composants principaux

### 1. Models
- **Fichier** : `Models/BaseModel.js`, `Models/Entity/*.js`
- **Rôle** : Encapsule la logique métier et le formatage
- **Méthode clé** : `toCell(fieldKey, options)` → génère les cellules formatées
- **Guide** : [MODELS.md](./MODELS.md)

### 2. Formatters
- **Fichier** : `Utils/Formatters/*.js`
- **Rôle** : Centralise le formatage des valeurs (rarity → badge, level → badge coloré, etc.)
- **Guide** : [FORMATTERS.md](./FORMATTERS.md)

### 3. Descriptors
- **Fichier** : `Entities/{entity}/{entity}-descriptors.js`
- **Rôle** : Source de vérité déclarative pour la configuration UX
- **Guide** : [DESCRIPTORS.md](./DESCRIPTORS.md)

### 4. Configs
- **Fichier** : `Utils/Entity/Configs/*.js`
- **Rôle** : Génère les configurations utilisables par les composants Vue
- **Guide** : [CONFIGS.md](./CONFIGS.md)

### 5. Renderers
- **Fichier** : `Pages/Organismes/entity/*.vue`
- **Rôle** : Composants Vue génériques qui utilisent les configs
- **Guide** : [RENDERERS.md](./RENDERERS.md)

### 6. Vues
- **Fichier** : `Pages/Molecules/entity/{entity}/*.vue`
- **Rôle** : Composants Vue spécifiques qui définissent le layout
- **Guide** : [VIEWS.md](./VIEWS.md)

---

## Guides pratiques

- [ARCHITECTURE.md](./ARCHITECTURE.md) — Architecture détaillée en 7 couches
- [MODELS.md](./MODELS.md) — Guide des modèles et formatage
- [FORMATTERS.md](./FORMATTERS.md) — Guide des formatters
- [DESCRIPTORS.md](./DESCRIPTORS.md) — Guide des descriptors
- [CONFIGS.md](./CONFIGS.md) — Guide des configurations
- [RENDERERS.md](./RENDERERS.md) — Guide des composants génériques
- [VIEWS.md](./VIEWS.md) — Guide des vues spécifiques
- [FLUX_COMPLETS.md](./FLUX_COMPLETS.md) — Flux détaillés pour chaque format

---

## Concepts clés

### Séparation des responsabilités
- **Models** : Logique métier et formatage
- **Formatters** : Formatage centralisé réutilisable
- **Descriptors** : Configuration déclarative (pas de logique)
- **Configs** : Génération de configurations depuis descriptors
- **Vues** : Layout manuel (pas de génération automatique)

### Source de vérité unique
- **Descriptors** : Source de vérité pour la configuration UX
- **SharedConstants** : Source de vérité pour les constantes partagées
- **FormatterRegistry** : Source de vérité pour le formatage

### Génération vs Manuel
- **Généré automatiquement** : Tableaux (headers, cellules), QuickEdit (champs), Formulaires (champs)
- **Manuel** : Vues (Large, Compact, Minimal, Text), Layout des vues d'édition

---

## Liens utiles

- [SharedConstants.js](../../resources/js/Utils/Entity/SharedConstants.js) — Constantes partagées
- [entity-registry.js](../../resources/js/Entities/entity-registry.js) — Registre centralisé
