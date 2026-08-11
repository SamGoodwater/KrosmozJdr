# Effets — IA

> Effets de sorts/objets et mappings DofusDB.

## Fichiers pivots

- `app/Models/Effect*.php`, `app/Models/ObjectEffect.php`
- `app/Services/Effect/`
- `app/Services/Scrapping/Core/Conversion/SpellEffects/`
- `app/Support/DofusHyperlinkText.php` (libellés d’états `{{spell,…::Nom}}`)
- `Spell::visibleToUser` / `EntityDisplayVisibilityService::constrainQueryToViewer` (listes)
- `GET /api/effects/definitions` — recherche defs pour liaison sort (payload edit allégé)
- `GET /api/effects/effects?q=&per_page=` — index paginé (plus de dump massif)
- `scrapping:effects:reapply-mappings` — reclasse les `autre` déjà mappés (ex. téléports)
- Canal sorts : `effects_definitions` (table legacy `spell_effects` droppée)
- `database/seeders/DofusdbEffectMappingSeeder.php`

## Hors périmètre

- Triage `autre` / effectId sans clé : [MAPPINGS_HORS_PERIMETRE.md](./MAPPINGS_HORS_PERIMETRE.md)

## Liens

- Scrapping : [../scrapping/_ai.md](../scrapping/_ai.md)
- Caractéristiques : [../characteristics/_ai.md](../characteristics/_ai.md)
- Scrap serveur : [../scrapping/SERVER_MASS_SCRAP.md](../scrapping/SERVER_MASS_SCRAP.md)
