# Rapport d'audit —

 Validation & XSS (Pages/Sections)

**Date** : 2025-01-13  
**Périmètre** : Module Pages/Sections (CRUD, FormRequests, Policies, SectionService, templates Vue)

---

## ✅ Phase 1 : Validation (FormRequests + JSON)

### **État actuel : SOLIDE**

#### ✓ Forces

1. **FormRequests dédiées** avec validation dynamique par template :
   - `StoreSectionRequest` / `UpdateSectionRequest` : règles dynamiques selon `SectionType`
   - `StorePageRequest` / `UpdatePageRequest` : enums `Visibility` / `PageState`, slugs validés
   - Toutes les mutations passent par `validated()` (pas de `request->all()` sauvage)

2. **Casts Eloquent** :
   - `Page/Section` : casts `json` pour `settings/data`, enums pour `state/is_visible/can_edit_role`
   - Pas d'exposition de champs sensibles (`created_by` auto-rempli côté controller)

3. **Validation inline limitée** :
   - Seulement 2 endroits : `attachUser/detachUser/syncUsers` (1-2 lignes, cohérent)
   - `reorder()` Pages/Sections : validation inline mais cohérente (`id` + `order/menu_order`)

#### ⚠️ Points d'amélioration mineurs

1. **Limites de taille manquantes** sur JSON :
   - `settings/data` : pas de `max:` explicite → risque de payload XXL
   - **Reco** : ajouter `max:65535` (limite TEXT en DB) ou `max:16777215` (MEDIUMTEXT)

2. **Reorder** : validation inline pourrait être une FormRequest dédiée pour la cohérence
   - **Reco** : créer `ReorderPagesRequest` / `ReorderSectionsRequest` (facultatif, gain marginal)

3. **StoreFileRequest/UpdateFileRequest** : `authorize()` retourne `true`
   - **Reco** : vérifier via policy si l'utilisateur peut uploader (actuellement vérifié dans le controller `SectionController@storeFile`, redondant mais OK)

---

## ✅ Phase 2 : XSS & Sanitization

### **État actuel : EXCELLENT (défense en profondeur)**

#### ✓ Protections en place

1. **Backend** : sanitization avant persistance
   - `SectionService::sanitizeSectionPayload()` → `Purifier::clean($content, 'section_text')`
   - Config `config/purifier.php` : profil `section_text` strict (pas de `style`, schémas http/https seulement, tags autorisés limités)
   - Appliqué à **chaque création/mise à jour** de section TEXT

2. **Frontend** : défense en profondeur
   - `sanitizeHtml()` (DOMPurify) appliqué avant `v-html` dans `SectionTextRead.vue`
   - Commentaire ESLint inline : `eslint-disable-next-line vue/no-v-html -- contenu sanitizé`

3. **Occurrences de `v-html`** : seulement **5 fichiers** identifiés
   - ✅ `SectionTextRead.vue` : protégé (DOMPurify)
   - ⚠️ `DateCore.vue`, `EntityTable.vue`, `Index.vue` : à vérifier (hors scope Pages/Sections mais à auditer séparément)

#### 📋 Actions recommandées

1. **Documenter la stratégie** :
   - Ajouter dans `PAGES_SECTIONS_SURFACE_MAP.md` : "Sanitization : backend (Purifier) + frontend (DOMPurify)"
   - Créer un guide `docs/20-Content/XSS_PREVENTION_GUIDE.md` pour les nouveaux templates

2. **Règle ESLint** :
   - Interdire `v-html` sauf avec commentaire `-- contenu sanitizé` + import de `sanitizeHtml`
   - Ajouter dans `.eslintrc.js` (cf. phase tooling)

3. **Tests XSS** :
   - Feature test : envoyer `<script>alert('XSS')</script>` → vérifier que le backend le neutralise
   - Vitest : tester `sanitizeHtml()` avec payloads XSS classiques

---

## 📊 Résumé sécurité

| Aspect | État | Score |
|--------|------|-------|
| Validation FormRequests | ✅ Solide | 9/10 |
| Casts Eloquent | ✅ Correct | 9/10 |
| Mass assignment | ✅ Protégé | 10/10 |
| Sanitization backend | ✅ Actif | 10/10 |
| Sanitization frontend | ✅ Actif | 10/10 |
| Limites JSON | ⚠️ À renforcer | 6/10 |
| Tests XSS | ❌ Absents | 0/10 |

**Score global** : **8.5/10** (très bon, quelques renforcements mineurs)

---

## 🎯 Backlog priorisé

### Critique (faire maintenant)
- ✅ **Aucun** (tout est déjà en production OK)

### Important (planifier)
1. **Tests XSS** : feature tests + unit tests `sanitizeHtml()` (effort : 2h)
2. **Limites JSON** : ajouter `max:` sur `settings/data` dans FormRequests (effort : 30min)

### Nice-to-have
1. **ReorderRequest** dédiées : refactor validation inline → FormRequest (effort : 1h, gain marginal)
2. **Guide XSS** : documenter la stratégie pour les futurs templates (effort : 1h)
3. **FileRequest authorize** : remplacer `true` par policy check (effort : 15min, redondant car déjà vérifié dans controller)

---

## 🔗 Fichiers clés

- Backend sanitization : `app/Services/SectionService.php` (L39-54)
- Config Purifier : `config/purifier.php` (L41-78)
- Frontend sanitization : `resources/js/Utils/security/sanitizeHtml.js`
- Template protégé : `resources/js/Pages/Organismes/section/templates/text/SectionTextRead.vue` (L50)
- FormRequests : `app/Http/Requests/Store*Request.php`, `app/Http/Requests/Update*Request.php`

