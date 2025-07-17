# Pages dynamiques et sections

## Tables principales
- `pages` : titre, slug, visibilité, menu, état, parent, ordre, créateur, timestamps, soft delete
- `sections` : page_id, ordre, type (composant Vue), params (json), visibilité, état, créateur, timestamps, soft delete

## Fonctionnement
- Une page = plusieurs sections ordonnées
- Chaque section référence un composant Vue via `type`
- Les params sont stockés en JSON pour la flexibilité
- Menu généré dynamiquement à partir des pages visibles et publiées
- Organisation en menu déroulant via parent_id et menu_order
- États : draft, pending, published, archived

## Exemples de params
- `text` : `{ "html": "<p>Contenu riche...</p>" }`
- `entity_table` : `{ "entity": "classes", "filters": { "is_visible": "guest" } }`
- `file` : `{ "url": "/storage/files/monfichier.pdf", "label": "Télécharger le PDF" }`

## Transitions d’état
- draft → pending → published → archived
- Actions : soumettre, publier, dépublier, archiver 