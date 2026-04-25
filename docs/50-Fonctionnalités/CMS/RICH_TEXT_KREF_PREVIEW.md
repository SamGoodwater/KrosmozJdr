# Références riches TipTap (kref)

## Périmètre

Les sections texte peuvent activer les références riches via :
- `settings.enableRichReferences`
- `settings.enableReferenceMapper`

Quand ce mode est actif, le contenu TipTap supporte des références inline `span.kref` pour :
- `characteristic`
- `entity`
- `page`
- `pageSection`

## Format de données

Le format principal stocke le payload dans l’attribut `title` (base64url JSON), avec ce schéma logique :
- `t` : type de référence
- `p` : payload objet
- `l` : libellé affiché

Le format `data-kref-type` / `data-kref-payload` est également pris en charge.

## Rendu et interactions

- Le nœud TipTap `referenceInline` rend les références en puces visuelles.
- Les classes de rendu sont calculées de façon unifiée (`kref`, `kref--type-*`, `kref--nav`, `kref--invalid`).
- Les références navigables (`page`, `pageSection`, `entity`) ouvrent la cible au clic via Inertia.
- Les références `pageSection` ouvrent un aperçu au survol (titre + extrait).
- La navigation vers `pageSection` utilise l’ancre `#section-{id}`.

## Sécurité

- Validation stricte du type de référence (whitelist).
- Rejet des encodages invalides.
- Limite de taille sur l’attribut `title` des références.
- Limite de taille sur le payload legacy `data-kref-payload`.
- Validation serveur des cibles (existence + droits de lecture) avant persistance.
- Double sanitation du HTML d’aperçu section (serveur + client).

## Fichiers clés

| Rôle | Fichier |
|------|---------|
| Codec / parsing / normalisation | `resources/js/Composables/richText/krefCodec.js` |
| Extension TipTap inline | `resources/js/Composables/richText/ReferenceInlineExtension.js` |
| Présentation visuelle des références | `resources/js/Composables/richText/referenceRenderService.js` |
| NodeView Vue des références inline | `resources/js/Composables/richText/ReferenceInlineNodeView.vue` |
| Helpers DOM (décodage + href) | `resources/js/Composables/richText/krefDomUtils.js` |
| Survol / clic | `resources/js/Pages/Molecules/data-display/RichTextKrefInteractions.vue` |
| Éditeur | `resources/js/Pages/Molecules/data-input/RichTextEditorField.vue` |
| Lecture readonly | `resources/js/Pages/Molecules/data-display/RichTextReadonlyView.vue` |
| Lecture section texte | `resources/js/Pages/Organismes/section/templates/text/SectionTextRead.vue` |
| Validation backend des références | `app/Support/SectionRichReferencesValidator.php` |
| Contrôle update section | `app/Services/SectionService.php` |
| API preview section | `app/Http/Controllers/Api/CmsSectionPreviewController.php` |

## API preview section

- **GET** `/api/cms/sections/{section}/preview-snippet`
- Nom Ziggy : `api.cms.sections.preview-snippet`
- Réponse type : `{ "canView": true, "title": "…", "html": "…" }`
- Contrôle d’accès : policy `view` sur `Section`

## Tests

```bash
pnpm exec vitest run tests/unit/composables/richText/krefCodec.test.js
php artisan test tests/Feature/PagesSections/SectionTextSanitizationTest.php
php artisan test --filter=CmsSectionPreviewApiTest
```
