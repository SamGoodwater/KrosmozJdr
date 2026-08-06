# Mappings d'effets sans caractéristique Krosmoz

Triage des `dofusdb_effect_mappings` dont `characteristic_source = characteristic`
mais sans `characteristic_key` convertible.

## Jouables (mappés)

| ID Dofus | Concept | Clé Krosmoz |
| --- | --- | --- |
| 95 | maxLifePoints / PV temporaires | `pv_temporaires` (`donner-pv-temporaires`) |
| 98 | Puissance sorts | `mastery_bonus` |
| 132 | % Tacle | `tacle` |
| 133 | % Fuite | `fuite` |

## Hors périmètre (revue manuelle → `autre` / none)

Ces effets restent importables pour diagnostic, mais ne reçoivent pas de clé inventée :

- cosmétique / UX Dofus : apparence, invisibilité, taille, confusion horaire ;
- économie Dofus : prospection, honneur, capture, XP monture ;
- mécaniques absentes : érosion, tour annulé, % résistances génériques/mêlée/distance/armes/sorts ;
- % de caractéristiques principales (échelle différente de Krosmoz) ;
- réductions magique/physique anciennes ;
- puissance armes / glyphes / runes ;
- échecs critiques (retiré du groupe spell) ;
- états (`État #N`) : convertis via `appliquer-etat`, pas via booster.

La liste machine est dans `ScrappingEffectsMapCommand::OUT_OF_SCOPE_CHARACTERISTIC_IDS`
et `dofusdb_characteristic_to_krosmoz_spell.json` (`out_of_scope_ids`).
