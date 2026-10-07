# Effets — IA

> Effets de sorts/objets et mappings DofusDB.

## Fichiers pivots

- **Sorts (canal natif)** : `SpellDegree` / `SpellDegreeEffect` — progression unique `Spell → degrés → effets`
  - `app/Models/SpellDegree.php`, `SpellDegreeEffect.php`
  - `app/Services/Spell/SpellDegreeResolver.php`, `SpellDegreeService.php`, `SpellDegreesSerializer.php`
  - `php artisan spells:migrate-degrees` (+ `--dry-run`, `--spell=`) : legacy `effect_spell` → `spell_degrees`
  - API : `GET/POST /api/spells/{spell}/degrees`, PATCH/DELETE degré, `materialize-effects`, sync effets
  - DELETE d’un degré source : matérialise les effets sur le successeur héritant avant cascade
  - UI : `SpellDegreesEditor.vue` (édition), `SpellEffectsJournal.vue` (affichage onglets niveau)
  - Portée UI compacte `x` / `x-y` (`po_min` + `po_max` en stockage) ; propriétés effectives du
    degré actif dans Full, premier degré dans Minimal/Line/table
  - `target_type`, résolution et `allows_reaction` restent globaux sur `Spell`; `area` existe aussi
    sur `Spell` comme repli sans degré. Champ zone partagé avec aperçu + validation de notation.
  - Effets spécialisés : élément, caractéristique recherchable, créature (`params.creature_id`),
    état et `duration_formula`; `monster_id` reste compatible legacy
  - Vocabulaire UI : **effet** (= ancien sous-effet) ; catalogue technique `SubEffect` conservé
- `app/Models/Effect*.php`, `app/Models/ObjectEffect.php` — encore utilisés objets + legacy sorts
- `GET /api/object-effects` — liste par fiche ; `view` sur le parent + `visibleToUser` sur le monstre invoqué (session `web`)
- `app/Services/Effect/` (`SpellNestedPreviewSerializer` : chips d’aperçu sur sorts liés)
- `app/Services/Scrapping/Core/Conversion/SpellEffects/` — après intégration, `rebuildFromLegacy` miroir natif
- `app/Support/DofusHyperlinkText.php` (libellés d’états `{{spell,…::Nom}}`)
- `app/Support/KrosmozGameTerms.php` — désenvoûtable → dissipable
- `app/Services/Condition/ConditionCanonicalMapper.php` — jeton Dofus → état JDR `playable`
- `php artisan conditions:remap-canonical` — recolle `condition_spell` + `params.condition_id`
- `php artisan spells:sync-elements` — aligne `spells.element` sur les éléments des sous-effets
- Affichage sorts : `spell_degrees` prioritaire ; `effects_definitions` legacy en repli
- `Spell::visibleToUser` / `EntityDisplayVisibilityService::constrainQueryToViewer` (listes)
- `GET /api/effects/definitions` — recherche defs legacy
- `GET /api/effects/effects?q=&per_page=` — index paginé
- `GET /api/effects/for-entity` et `GET /api/effects/usages` : middleware `web` + `view` parent
- `scrapping:effects:reapply-mappings` — reclasse les `autre` déjà mappés
- Canal sorts : `spell_degrees` (natif) + `effects_definitions` (legacy jusqu’à bascule)
- Invisibilité Dofus 150 → `appliquer-etat` (state 250)
- `database/seeders/DofusdbEffectMappingSeeder.php`

## Hors périmètre

- Page contenu : `/admin/content/dofusdb-effect-mappings`
- Triage `autre` / effectId sans clé : [MAPPINGS_HORS_PERIMETRE.md](./MAPPINGS_HORS_PERIMETRE.md)
- Retrait définitif de `effect_spell` pour les sorts : après audit migration complète

## Liens

- Scrapping : [../scrapping/_ai.md](../scrapping/_ai.md)
- Caractéristiques : [../characteristics/_ai.md](../characteristics/_ai.md)
- Scrap serveur : [../scrapping/SERVER_MASS_SCRAP.md](../scrapping/SERVER_MASS_SCRAP.md)
