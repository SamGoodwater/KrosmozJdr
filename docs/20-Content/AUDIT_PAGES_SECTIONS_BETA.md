# Audit du système Pages et Sections — Version bêta

**Date :** 2025-03-03  
**Objectif :** Vérifier si le système de pages et de sections est fonctionnel, cohérent, optimisé et abouti pour une mise en ligne en version bêta.

---

## 1. Synthèse

| Critère        | État   | Commentaire |
|----------------|--------|-------------|
| **Fonctionnel** | ✅ Oui | Routes, contrôleurs, services, front (PageRenderer, SectionRenderer, templates) et menu dynamique sont en place et cohérents. |
| **Cohérent**    | ✅ Oui | Modèles, policies, resources, mappers et doc (PAGES_SECTIONS.md) sont alignés. Une correction mineure sur les ancres a été faite. |
| **Optimisé**   | ✅ Oui | Eager loading, cache menu, cache registry des templates, pas de N+1 évident. |
| **Abouti**     | ⚠️ Partiel | Prêt pour la bêta. Quelques points à finaliser (voir section 5). |

**Verdict :** Le système est **prêt pour une mise en ligne en version bêta**, avec les réserves et améliorations listées ci-dessous.

---

## 2. Ce qui fonctionne bien

### 2.1 Backend

- **Modèles** `Page` et `Section` : états (raw, draft, playable, archived), niveaux lecture/écriture, soft delete, relations (sections, parent/children, users, campaigns, scenarios, media).
- **Services** `PageService` et `SectionService` : logique centralisée, cache menu (TTL 1 h), `getSectionsForPage()` selon droits (éditeur = toutes les sections, visiteur = affichables uniquement), sanitization HTML (Purifier) pour le template `text`.
- **Contrôleurs** `PageController` et `SectionController` : CRUD, reorder, policies, notifications, redirections cohérentes vers `pages.show` après création/édition de section.
- **Routes** : `pages` (index, menu, show par slug, create/store, edit/update, delete/restore/forceDelete, reorder) ; `sections` (index, store, show, update, delete, reorder, files). Route `pages.show` en `{page:slug}` pour URLs lisibles.
- **Resources** `PageResource` et `SectionResource` : exposent `can.update/delete/…`, sections incluses dans la page, chargement conditionnel des relations.
- **Config** `config/section_templates.php` : valeurs par défaut (text, image, gallery, video, entity_table) synchronisées avec les configs JS des templates.

### 2.2 Frontend

- **PageRenderer** : titre, bouton d’édition (si droits), liste de sections triées par `order`, bouton d’ajout de section, modals EditPage et CreateSection, gestion de l’ouverture en édition après création.
- **SectionRenderer** : rendu dynamique par template (registry avec cache), SectionHeader (titre, édition, paramètres, copier le lien), chargement read/edit selon le mode, message si template inconnu.
- **Registry de templates** : auto-discovery via `import.meta.glob`, validation au démarrage, cache des composants, templates `text`, `image`, `gallery`, `video`, `entity_table` (ce dernier marqué caché côté UI).
- **DynamicMenu** : appel à `GET /pages/menu`, arborescence parent/enfants, `url` et `slug` par page, état actif et sous-menus ouverts, intégré dans `Aside.vue`.
- **Modals** : EditPageModal (onglets Général / Sections avec drag & drop), CreateSectionModal, SectionParamsModal selon le type de section.

### 2.3 Sécurité et qualité

- **Policies** : PagePolicy et SectionPolicy pour view/update/delete/restore/forceDelete ; prise en compte de l’auteur et des utilisateurs associés.
- **XSS** : Purifier sur le contenu HTML des sections `text` (config `section_text`).
- **Tests** : `PageControllerTest`, `SectionControllerTest`, `SectionAuthorizationTest`, `SectionTextSanitizationTest`, `StoreSectionRequestTest`, `XssPreventionTest`.

---

## 3. Cohérence et alignement

- **Ancres de section** : La doc indiquait une ancre `#section-{id}`. Le conteneur de section n’avait pas d’`id` et le lien copié utilisait `#slug` ou `#id` sans préfixe. **Correction appliquée** : le conteneur a maintenant `id="section-{id}"` et le lien copié est `#section-{id}` pour permettre le scroll vers la section.
- **Template text** : Backend (SectionService + config) et frontend (config.js, SectionTextRead/Edit) utilisent `data.content` (et non `data.html`). La doc PAGES_SECTIONS.md mentionnait `data.html` ; à mettre à jour si vous standardisez sur `content`.
- **Lien de section** : `useSectionUI` exposait déjà une URL `#section-${sectionSlug}` ; le copier dans SectionRenderer a été aligné sur `#section-{id}`.

---

## 4. Optimisations déjà en place

- **PageController::show** : Eager load des relations, `setRelation('sections', …)` pour éviter N+1, cache `pages_select_list` pour la liste des pages.
- **PageController::index** : Eager load avec `select` pour limiter les colonnes.
- **PageService::getMenuPages** : cache par utilisateur (`menu_pages_{id|guest}`), TTL 1 h.
- **SectionController** : récupération des sections en lot pour reorder.
- **useTemplateRegistry** : cache des composants chargés par `template:mode`, validation une fois au démarrage.

---

## 5. Points à finaliser ou à surveiller pour la bêta

1. **Documentation**  
   - Mettre à jour PAGES_SECTIONS.md : remplacer `data.html` par `data.content` pour le template text si c’est le choix retenu.  
   - Vérifier que SECTION_PARAMETERS.md et les exemples JSON reflètent bien les champs utilisés (content, etc.).

2. **Template entity_table**  
   - Présent en config (PHP + front) et dans le registry, mais marqué `hidden` côté UI. Si la bêta doit exposer les tableaux d’entités dans les pages, prévoir de le rendre disponible dans CreateSectionModal et de tester le flux complet.

3. **Route sections.show**  
   - La route utilise `{section}` (binding par id par défaut). Le `where('section', '[a-z0-9]+(?:-[a-z0-9]+)*')` accepte les chiffres, donc les URLs du type `/sections/1` restent valides. Aucun changement requis si vous gardez l’id.

4. **Cache menu**  
   - `PageService::clearMenuCache()` est appelé à chaque création/update/delete/restore de page. Si vous ajoutez un cache côté client (ex. localStorage dans useDynamicMenu), prévoir une invalidation ou un TTL court pour la bêta.

5. **Erreurs / états vides**  
   - DynamicMenu affiche un message si le chargement échoue ou si la liste est vide. PageRenderer affiche un message et un CTA si aucune section. Vérifier en bêta que les messages sont clairs pour l’utilisateur final.

6. **Permissions sur la liste des pages**  
   - `pages.index` est protégé par `viewAny` (PagePolicy). S’assurer que seuls les rôles attendus (ex. admin / contributeurs) voient la liste ; les visiteurs n’accèdent qu’à `pages.show` par slug et au menu.

---

## 6. Checklist avant mise en ligne bêta

- [ ] Vérifier que les pages “jouables” et “in_menu” sont bien celles que vous voulez exposer.
- [ ] Tester en production (ou staging) : création page → ajout sections (text, image, gallery, video) → édition → copier le lien de section → ouverture dans un nouvel onglet et scroll vers `#section-{id}`.
- [ ] Tester le menu dynamique (racine + enfants) et la mise en surbrillance de la page courante.
- [ ] Confirmer que le cache menu (et éventuellement client) est bien invalidé après modification des pages.
- [ ] S’assurer que les rôles (guest, user, admin, etc.) et les niveaux read_level/write_level correspondent à la cible bêta.

---

## 7. Références

- [PAGES_SECTIONS.md](./PAGES_SECTIONS.md) — Spécification du système
- [PAGES_SECTIONS_ARCHITECTURE.md](./PAGES_SECTIONS_ARCHITECTURE.md) — Flux de données
- [SECTION_PARAMETERS.md](./SECTION_PARAMETERS.md) — Paramètres par template
- `app/Models/Page.php`, `app/Models/Section.php`
- `app/Services/PageService.php`, `app/Services/SectionService.php`
- `resources/js/Pages/Organismes/section/` — PageRenderer, SectionRenderer, DynamicMenu, modals, templates
