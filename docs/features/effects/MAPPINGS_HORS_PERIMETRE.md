# Mappings d'effets sans caractéristique Krosmoz

Triage des `dofusdb_effect_mappings` dont `characteristic_source = characteristic`
mais sans `characteristic_key` convertible, et des effectId fréquents volontairement en `autre`.

## Jouables (mappés)

| ID Dofus | Concept | Clé Krosmoz |
| --- | --- | --- |
| 95 | maxLifePoints / PV temporaires | `pv_temporaires` (`donner-pv-temporaires`) |
| 98 | Puissance sorts | `mastery_bonus` |
| 120 | Rembourse PA | `pa` (`booster`) |
| 132 | % Tacle | `tacle` |
| 133 | % Fuite | `fuite` |

## Déplacements convertibles (`déplacer`)

Ces effectId ne doivent plus rester en `autre` après seed des mappings +
`php artisan scrapping:effects:reapply-mappings` (ou re-import sorts) :

| ID Dofus | Concept |
| --- | --- |
| 8 | Échange de positions |
| 1099 | Téléporte à la position de début de tour |
| 1100 | Téléporte à la position précédente |
| 1101 | Téléporte ou échange de positions |
| 1104–1106 | Téléportations symétriques |

## Hors périmètre (revue manuelle → `autre` / none)

Ces effets restent importables pour diagnostic, mais ne reçoivent pas de clé inventée :

- cosmétique / UX Dofus : apparence, invisibilité (ex. 150), taille, confusion horaire ;
- économie Dofus : prospection, honneur, capture, XP monture ;
- mécaniques absentes : érosion (ex. 776), tour annulé, % résistances génériques/mêlée/distance/armes/sorts ;
- % de caractéristiques principales (échelle différente de Krosmoz) ;
- réductions magique/physique anciennes ;
- puissance armes / **glyphes** / **pièges** / runes (ex. 400–402, 1091, 1026, 2160) ;
- placeholders `#1` sans libellé utile (ex. **792** ~10k lignes, 1160, 2792/2794, 2960) ;
- kill / purge d’effets / durée générique (ex. 141, 406, 1075) ;
- échecs critiques (retiré du groupe spell) ;
- états (`État #N`) : convertis via `appliquer-etat`, pas via booster.

Le volume `autre` (~40 %) est donc **attendu** tant que glyphes / pièges / `#1`
restent hors périmètre ; l’audit `scrapping:effects:audit-autre` mesure surtout
les fuites convertibles (téléports mal reclassés, etc.).

La liste machine est dans `ScrappingEffectsMapCommand::OUT_OF_SCOPE_CHARACTERISTIC_IDS`,
`dofusdb_characteristic_to_krosmoz_spell.json` (`out_of_scope_ids`) et
`database/seeders/data/dofusdb_effect_mappings_suggested.php`.
