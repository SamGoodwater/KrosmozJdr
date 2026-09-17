# Bugs entités — Phase F (release 1.3.2)

**Objectif** : corriger les régressions UX sur capacités, sorts, items, monstres, bibliothèques états/traits et panoplies.

**Voir aussi** : [CHECKLIST release 1.3.2](../110-%20To%20Do/CHECKLIST-release-1.3.2.md)

## Correctifs livrés

| Thème | Changement |
| --- | --- |
| **Capacités — Ziggy** | `getEntitySingularRouteKey('capabilities')` → `capability` (évite `capabilitie` pour show/edit/PDF/dispatcher). |
| **États / traits — routes** | `conditions` et `creatureTrait` en `paramsMode: object` pour `show` ; création modal `creature-trait` alignée sur `condition`. |
| **Sorts — état minimal** | Pastille `EntityStateBadge` + contexte actions `inMinimal` ; tick local après action `state`. |
| **Items / consommables — popover sorts** | Effet HTML en fiche full via `RichTextReadonlyView` + `enableRichReferences` (kref sorts dans le texte). |
| **Monstres** | Langues visibles en badges (plus seulement au survol) ; `EditActionDock` via `fixed-footer-actions` sur l’édition. |
| **Panoplies minimal** | Effet par défaut = chips `bonus` ; liste équipements en vue Texte au survol (`PanoplyEquipmentTextList`) ; API table charge `items.id,name,level`. |

## Tests

- `tests/unit/entity/entityRouteRegistry.test.js`
- `tests/unit/models/spell-effect-summary-fallback.test.js` (Phase E, colonne Effets)

## Recette manuelle

1. **Capacité** : depuis tableau/minimal, ouvrir page show et PDF — URL avec param `capability=`.
2. **Sort minimal** : changer l’état via raccourci ⋮ / pastille ; vérifier pastille à jour.
3. **Monstre** : édition → dock bas-droite Enregistrer + survol Reset/Annuler ; langues visibles sur carte minimal.
4. **Panoplie minimal** : bonus visible ; survol carte → liens équipements (vue Texte + popover item).
5. **Bibliothèque Traits / États** : index, modal full, copier lien, show.
