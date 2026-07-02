# Propriétés DofusDB utiles pour KrosmozJDR

Lors de l’affichage des propriétés (Brut / Converti / Krosmoz), seules les propriétés listées ci‑dessous sont affichées. Le reste (moteur de jeu Dofus, champs API, doublons) est masqué.

**Source de vérité pour le mapping propriété ↔ chemin DofusDB :** les fichiers `resources/scrapping/config/sources/dofusdb/entities/*.json` (champ `mapping[].key` et `mapping[].from.path`). Voir [RECOMMANDATION_MAPPING_PROPRIETES.md](RECOMMANDATION_MAPPING_PROPRIETES.md).

## Monstre (monster)

| Propriété | Intérêt KrosmozJDR |
|-----------|--------------------|
| **id** | ID DofusDB, référence externe. |
| **name** / **name.fr**, **name.en**, etc. | Nom du monstre (fiche, bestiaire). |
| **race**, **raceId**, **raceName** | Race (Larves, Gobelins…) pour filtres et affichage. |
| **grades** | Niveaux et stats par grade (niveau, PV, caractéristiques de combat). |
| **spells** | Sorts du monstre (combat JDR). |
| **drops** | Butin (ressources, objets). |
| **img** | Image pour fiche ou token. |
| **isBoss**, **isMiniBoss** | Classification pour le MJ. |
| **summonCost**, **useSummonSlot** | Invocations (si utilisées en JDR). |
| **look** | Apparence (optionnel). |

## À ne pas afficher (sans intérêt JDR)

- **API / BDD** : `_id`, `className`, `createdAt`, `updatedAt`, `m_id`, `m_flags`
- **Moteur de jeu** : `aggressive*`, `canBeCarried`, `canBePushed`, `canPlay`, `canSwitchPos`, `canTackle`, `canUsePortal`, `fastAnimsFun`, `soulCaptureForbidden`, `speedAdjust`, `useBombSlot`, `useRaceValues`
- **Bloc doublon** : `correspondingMiniBoss`, `correspondingMiniBoss.*`, `correspondingMiniBossId`
- **Internes** : `creatureBoneId`, `favoriteSubareaId`, `hideInBestiary`, `incompatibleChallenges`, `incompatibleIdols`, `scaleGradeRef`, `gfxId`, `spellGrades` (détail technique), `subareas`, `tags`, `temporisDrops`
- **État frontend** : `existing`, `exists`

L’affichage dans le tableau de comparaison utilise **une seule source, côté backend** : les clés du mapping (`entities/*.json` → `mapping[].key`), exposées par l’API `/api/scrapping/config` dans `comparisonKeys`. Le frontend n’applique aucun filtre d’exclusion : seules les propriétés définies dans le mapping sont affichées. Quand un enregistrement Krosmoz existant est chargé, les colonnes viennent du backend (converti + existant).
