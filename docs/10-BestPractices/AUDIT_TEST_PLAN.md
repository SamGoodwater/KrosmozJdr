# Plan de tests — Pages/Sections (Non-régression)

**Date** : 2025-01-13  
**Périmètre** : Tests ciblés Pages/Sections (Policies, Validation, XSS, Renderer)

---

## 🎯 Objectifs

1. **Couvrir les points critiques** identifiés dans l'audit (AuthZ, Validation, XSS)
2. **Prévenir les régressions** lors des refactors futurs (DRY, tooling)
3. **Documenter le comportement attendu** (tests = spécification vivante)

---

## 📋 Pack de tests (priorisé)

### **P1 - Critique : Policies (AuthZ)**

#### Feature Tests (`tests/Feature/Policies/`)

| Test | Description | Effort |
|------|-------------|--------|
| `PagePolicyTest::test_guest_can_view_public_page()` | Invité peut voir page `is_visible=guest` | 15min |
| `PagePolicyTest::test_guest_cannot_view_admin_page()` | Invité ne peut pas voir page `is_visible=admin` | 15min |
| `PagePolicyTest::test_admin_can_create_page()` | Admin peut créer page | 10min |
| `PagePolicyTest::test_user_cannot_create_page()` | User ne peut pas créer page | 10min |
| `PagePolicyTest::test_author_can_update_own_page()` | Auteur peut modifier sa page | 15min |
| `PagePolicyTest::test_user_cannot_update_others_page()` | User ne peut pas modifier page d'autrui | 15min |
| `PagePolicyTest::test_associated_user_can_update_page()` | User associé (via `page_user`) peut modifier | 20min |
| `SectionPolicyTest::test_create_section_requires_page_update()` | Créer section = droit `update` sur page | 15min |
| `SectionPolicyTest::test_author_can_update_own_section()` | Auteur peut modifier sa section | 15min |
| `SectionPolicyTest::test_delete_section_requires_page_update()` | Supprimer section = droit `update` sur page | 15min |

**Total P1** : ~2h30

---

### **P2 - Important : Validation (FormRequests)**

#### Feature Tests (`tests/Feature/Requests/`)

| Test | Description | Effort |
|------|-------------|--------|
| `StorePageRequestTest::test_title_required()` | Titre obligatoire | 10min |
| `StorePageRequestTest::test_slug_unique()` | Slug unique | 15min |
| `StorePageRequestTest::test_slug_format()` | Slug regex `^[a-z0-9-]+$` | 10min |
| `StorePageRequestTest::test_is_visible_enum()` | `is_visible` = enum Visibility | 10min |
| `StoreSectionRequestTest::test_page_id_required()` | `page_id` obligatoire | 10min |
| `StoreSectionRequestTest::test_template_enum()` | `template` = enum SectionType | 10min |
| `StoreSectionRequestTest::test_data_validation_text()` | Validation dynamique `data.content` (TEXT) | 20min |
| `StoreSectionRequestTest::test_data_validation_image()` | Validation dynamique `data.src/alt` (IMAGE) | 20min |
| `UpdateSectionRequestTest::test_settings_merge()` | Settings fusionnés (pas écrasés) | 20min |

**Total P2** : ~2h

---

### **P3 - Critique : XSS (Sanitization)**

#### Feature Tests (`tests/Feature/Security/`)

| Test | Description | Effort |
|------|-------------|--------|
| `XssPreventionTest::test_section_text_sanitizes_script()` | `<script>` neutralisé dans section TEXT | 20min |
| `XssPreventionTest::test_section_text_sanitizes_onclick()` | `onclick=` neutralisé | 15min |
| `XssPreventionTest::test_section_text_allows_safe_html()` | `<p><strong>` autorisés | 15min |
| `XssPreventionTest::test_section_text_strips_style_attr()` | `style=` retiré (config Purifier) | 15min |

#### Unit Tests (`tests/Unit/Utils/`)

| Test | Description | Effort |
|------|-------------|--------|
| `SanitizeHtmlTest::test_removes_script_tags()` | DOMPurify retire `<script>` | 10min |
| `SanitizeHtmlTest::test_removes_event_handlers()` | DOMPurify retire `onerror=` | 10min |
| `SanitizeHtmlTest::test_allows_safe_tags()` | DOMPurify garde `<p><a>` | 10min |

**Total P3** : ~1h35

---

### **P4 - Important : Renderer (Vue)**

#### Component Tests (`tests/Vitest/Organismes/`)

| Test | Description | Effort |
|------|-------------|--------|
| `SectionRenderer.spec.js::test_loads_text_read_template()` | Charge `SectionTextRead` pour template=text | 20min |
| `SectionRenderer.spec.js::test_loads_text_edit_template()` | Charge `SectionTextEdit` en mode edit | 20min |
| `SectionRenderer.spec.js::test_fallback_on_missing_template()` | Fallback si template inconnu | 15min |
| `SectionTextRead.spec.js::test_renders_sanitized_html()` | Rend HTML sanitizé via `v-html` | 20min |
| `SectionTextRead.spec.js::test_applies_settings_classes()` | Applique classes depuis `settings.align` | 15min |

**Total P4** : ~1h30

---

### **P5 - Nice-to-have : Reorder (Drag & Drop)**

#### Feature Tests (`tests/Feature/Controllers/`)

| Test | Description | Effort |
|------|-------------|--------|
| `PageReorderTest::test_reorder_updates_menu_order()` | `PATCH /pages/reorder` met à jour `menu_order` | 20min |
| `PageReorderTest::test_reorder_requires_update_permission()` | Reorder vérifie `authorize('update')` par page | 20min |
| `SectionReorderTest::test_reorder_updates_order()` | `PATCH /sections/reorder` met à jour `order` | 20min |
| `SectionReorderTest::test_reorder_requires_update_permission()` | Reorder vérifie `authorize('update')` par section | 20min |

**Total P5** : ~1h20

---

## 📊 Résumé effort

| Priorité | Scope | Nb tests | Effort total |
|----------|-------|----------|--------------|
| **P1** | Policies (AuthZ) | 10 | ~2h30 |
| **P2** | Validation | 9 | ~2h |
| **P3** | XSS | 7 | ~1h35 |
| **P4** | Renderer | 5 | ~1h30 |
| **P5** | Reorder | 4 | ~1h20 |
| **TOTAL** | | **35 tests** | **~9h** |

---

## 🛠️ Outillage

### Backend (PHPUnit)

```bash
# Lancer tous les tests
php artisan test

# Lancer seulement les tests Pages/Sections
php artisan test --filter=Page
php artisan test --filter=Section

# Avec coverage
php artisan test --coverage --min=80
```

### Frontend (Vitest)

```bash
# Lancer tous les tests
pnpm run test

# Lancer seulement les tests Renderer
pnpm run test SectionRenderer

# Avec UI
pnpm run test:ui

# Avec coverage
pnpm run test:coverage
```

---

## 📋 Checklist d'implémentation

### Phase 1 : Tests critiques (P1 + P3)
- [ ] Créer `tests/Feature/Policies/PagePolicyTest.php` (10 tests, 2h30)
- [ ] Créer `tests/Feature/Security/XssPreventionTest.php` (4 tests, 1h05)
- [ ] Créer `tests/Unit/Utils/SanitizeHtmlTest.js` (3 tests, 30min)
- **Total Phase 1** : 17 tests, ~4h

### Phase 2 : Tests validation (P2)
- [ ] Créer `tests/Feature/Requests/StorePageRequestTest.php` (4 tests, 45min)
- [ ] Créer `tests/Feature/Requests/StoreSectionRequestTest.php` (5 tests, 1h15)
- **Total Phase 2** : 9 tests, ~2h

### Phase 3 : Tests renderer (P4)
- [ ] Créer `tests/Vitest/Organismes/SectionRenderer.spec.js` (3 tests, 55min)
- [ ] Créer `tests/Vitest/Organismes/SectionTextRead.spec.js` (2 tests, 35min)
- **Total Phase 3** : 5 tests, ~1h30

### Phase 4 : Tests reorder (P5 - optionnel)
- [ ] Créer `tests/Feature/Controllers/PageReorderTest.php` (2 tests, 40min)
- [ ] Créer `tests/Feature/Controllers/SectionReorderTest.php` (2 tests, 40min)
- **Total Phase 4** : 4 tests, ~1h20

---

## ✅ Critères de succès

1. **Coverage** : ≥80% sur `app/Policies/PagePolicy.php`, `app/Policies/SectionPolicy.php`, `app/Services/SectionService.php`
2. **Non-régression** : Tous les tests passent après refactors DRY
3. **CI/CD** : Tests lancés automatiquement sur chaque PR
4. **Documentation** : Chaque test documente un comportement attendu (PHPDoc + assertions claires)

---

## 🔗 Fichiers de tests (structure)

```
tests/
├── Feature/
│   ├── Policies/
│   │   ├── PagePolicyTest.php
│   │   └── SectionPolicyTest.php
│   ├── Requests/
│   │   ├── StorePageRequestTest.php
│   │   └── StoreSectionRequestTest.php
│   ├── Security/
│   │   └── XssPreventionTest.php
│   └── Controllers/
│       ├── PageReorderTest.php
│       └── SectionReorderTest.php
├── Unit/
│   └── Utils/
│       └── SanitizeHtmlTest.js
└── Vitest/
    └── Organismes/
        ├── SectionRenderer.spec.js
        └── SectionTextRead.spec.js
```

---

## 📚 Ressources

- **PHPUnit** : https://phpunit.de/documentation.html
- **Laravel Testing** : https://laravel.com/docs/12.x/testing
- **Vitest** : https://vitest.dev/guide/
- **Vue Test Utils** : https://test-utils.vuejs.org/guide/

