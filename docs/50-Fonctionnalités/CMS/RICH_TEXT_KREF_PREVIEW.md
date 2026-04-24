# Aperçu au survol des références vers les sections (style Wikipédia)

## Contexte

Lorsque le template de section texte active **références riches** (`settings.enableRichReferences`), le contenu TipTap peut contenir des mentions `span.kref` (caractéristique, entité, page, **section de page**).

Pour les références **`pageSection`**, l’interface affiche un **cartel d’aperçu** après un court délai au survol (environ 380 ms), sur le modèle des aperçus Wikipédia. Le HTML affiché dans le cartel est fourni par l’API, déjà passé par **HTMLPurifier** (`section_text`), puis **sanitisé une seconde fois** côté navigateur avec `sanitizeHtml` (DOMPurify).

Les références **navigables** (`page`, `pageSection`, `entité`) portent les classes `kref kref--nav` : soulignement pointillé, curseur pointeur, **clic** pour ouvrir la cible via **Inertia** (`router.visit`).

## Fichiers concernés

| Rôle | Fichier |
|------|---------|
| Encodage / parsing des `kref` | `resources/js/Composables/richText/ReferenceInlineExtension.js`, `resources/js/Composables/richText/krefDomUtils.js` |
| Survol + clic | `resources/js/Pages/Molecules/data-display/RichTextKrefInteractions.vue` |
| Branchement édition | `resources/js/Pages/Molecules/data-input/RichTextEditorField.vue` |
| Branchement lecture | `resources/js/Pages/Molecules/data-display/RichTextReadonlyView.vue` |
| API JSON | `app/Http/Controllers/Api/CmsSectionPreviewController.php`, route nommée `api.cms.sections.preview-snippet` |
| Classes HTML autorisées | `config/purifier.php` (`kref`, `kref--nav`) |

## API

- **GET** `/api/cms/sections/{section}/preview-snippet`
- Nom Ziggy : `api.cms.sections.preview-snippet`
- Réponse typique : `{ "canView": true, "title": "…", "html": "…" }`
- Autorisations : policy **`view`** sur le modèle `Section` (même logique que la page publique).

## Comportement UX

1. Survol d’un `span.kref` dont le type est **`pageSection`** → requête d’aperçu sur la section cible.
2. Survol d’une **`page`** → cartel avec titre + texte d’aide (pas d’extrait HTML).
3. Autres types (ex. caractéristique) → message indiquant l’absence d’aperçu structuré.
4. Entrée souris dans le cartel : l’aperçu **reste affiché** (délai de fermeture annulé) pour permettre le défilement du texte.
5. Clic sur une référence navigable : navigation Inertia vers la page (ancre `#section-{id}` pour les sections).

## Collage depuis Word / navigateur

Les `span.kref` dont le payload est dans l’attribut **`title`** (et non plus uniquement `data-kref-*`) sont **laissés intacts** par `normalizePastedHtml` dans `RichTextEditorField.vue`.

## Tests automatisés

```bash
php artisan test --filter=CmsSectionPreviewApiTest
php artisan test --filter=SectionTextSanitizationTest
```

- `CmsSectionPreviewApiTest` : droits invité / joueur, contenu HTML nettoyé (pas de `<script>` dans la réponse JSON).
- `SectionTextSanitizationTest::test_section_text_preserves_kref_nav_class_for_navigable_references` : persistance des classes `kref kref--nav` après Purifier + validation des références.

## Limites connues

- L’aperçu enrichi est réservé aux références **section** ; les entités / caractéristiques n’ouvrent pas de carte HTML (navigation au clic reste possible pour les **entités** lorsque la route existe).
- Après modification de `config/purifier.php`, exécuter `php artisan config:clear` en environnement où la configuration est mise en cache.
