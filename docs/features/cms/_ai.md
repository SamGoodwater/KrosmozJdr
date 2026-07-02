# CMS Pages & Sections — carte IA (degré 1a)

> CMS maison : des **pages** hiérarchiques (arbre `parent_id`, menu dynamique) composées de **sections** typées (texte Tiptap, image, galerie, vidéo, tableaux d'entités, documents légaux, chartes de caractéristiques). Références inline « kref » vers entités/pages/caractéristiques.

## Quand lire ce nœud

- Travailler sur les pages publiques/règles, le menu, les sections de contenu.
- Ajouter/modifier un type de section (template), l'éditeur riche, ou les références kref.

## Concepts clés

- **Page** : `slug` unique, `state`, `read_level`/`write_level`, `parent_id`, `in_menu`/`menu_order`/`menu_group`, `settings` JSON (dont `linked_entity`). Code : `app/Models/Page.php`. Slugs critiques : `accueil`, `cgu`.
- **Section** : appartient à une page, `template` (enum `SectionType`), `data`/`settings`, `order`, médias Spatie. Code : `app/Models/Section.php`, `app/Enums/SectionType.php`.
- **9 templates** : `text`, `image`, `gallery`, `video`, `entity_table`, `legal_markdown`, `characteristic_norms`, `characteristic_norms_catalog`, `characteristic_reference_table`. Détail : [README](./README.md#templates-de-sections).
- **Rendu front** : `PageRenderer` → `SectionLazyGate` (lazy) → `SectionRenderer` → template via registry auto-discovery. Détail : [README](./README.md#rendu-frontend).
- **kref** : références inline `@` (Tiptap) sérialisées en `<span class="kref">`, validées et prévisualisées via API CMS. Détail : [README](./README.md#references-kref).
- **Menu dynamique** : `GET /pages/menu` (JSON) → `useDynamicMenu` → `DynamicMenu.vue`. 4 groupes fixes (L'Essentiel, Règles, Bibliothèques, Informations).
- **Sécurité contenu** : Mews\Purifier (profil `section_text`) sur le HTML de section.

## Fichiers pivots

- `app/Models/Page.php`, `app/Models/Section.php`, `app/Enums/SectionType.php`.
- `app/Http/Controllers/PageController.php`, `SectionController.php` ; API `app/Http/Controllers/Api/Cms*.php` (×3).
- `app/Services/PageService.php` (menu + cache), `app/Services/SectionService.php` (save + Purifier + kref).
- `app/Policies/PagePolicy.php`, `SectionPolicy.php` ; resources `PageResource`/`SectionResource`.
- `routes/web/page.php`, `routes/api/cms.php`.
- `resources/js/Pages/Organismes/section/PageRenderer.vue` (+ `SectionRenderer`, `SectionLazyGate`, `templates/index.js`, `composables/useTemplateRegistry.js`).
- `resources/js/Pages/Molecules/data-input/RichTextEditorField.vue` + `resources/js/Composables/richText/*` (kref).

## Descendre

- [README humain](./README.md) — modèles, templates, kref, menu, routes détaillés.
- Entités référencées par kref : [../entities/_ai.md](../entities/_ai.md).
- Doc existante (L2) : `docs/features/cms/README.md`, `ENTITY_SECTIONS.md`, `docs/features/cms/README.md`.
