# Caractéristiques

Les caractéristiques décrivent les valeurs numériques et règles de lecture des créatures, objets et sorts : niveaux, dégâts, PA/PM/PO, limites, formules et normes.

## Backend

- Définitions et lecture : `app/Services/Characteristics/`, `app/Support/Characteristics/`.
- Limites : `app/Services/Characteristic/Limit/CharacteristicLimitService.php`.
- Formules : services sous `app/Services/Characteristic/`.
- Seeders : `CharacteristicSeeder`, `CreatureCharacteristicSeeder`, `ObjectCharacteristicSeeder`, `SpellCharacteristicSeeder`.
- Qualité des définitions : `app/Services/Characteristics/CharacteristicDefinitionQualityService.php` vérifie notamment les `norms_grid`, formules de conversion et restrictions d'équipement.

## Normes

Les définitions JSON dans `database/seeders/data/characteristic-definitions/` portent les grilles de normes 5 puissances × 20 niveaux (`norms_grid`). L'audit 2026-07 ne laisse aucun `norms_grid` manquant sur les vraies définitions `creature`, `object` et `spell`; seuls les fichiers `_templates` restent volontairement vides.

Pour les objets, les caractéristiques dont l'aide cible un type d'équipement portent aussi `item_type_dofus_ids` :
- amulettes : `[1]`
- anneaux : `[9]`, ceintures : `[10]`, bottes : `[11]`
- chapeaux : `[16]`, capes : `[17]`, boucliers : `[82]`
- armes : `[2,3,4,5,6,7,8,19,21,22,114,271]`

Ces valeurs sont les identifiants stables de types DofusDB, jamais les clés locales de la table
`item_types`. Une aide mentionnant plusieurs emplacements produit l'union correspondante.

## Surcharges des monstres

Une définition `entities.monster` remplace les limites et la formule génériques uniquement pour les monstres.
Les personnages restent plafonnés à 24 pour leurs caractéristiques principales, tandis que les monstres
peuvent atteindre 30. Le même mécanisme porte leurs limites PA/PM/PO élargies et les conversions par
paliers des résistances relatives et des critiques.

Les tables de formule acceptent des seuils négatifs. La tranche retenue est le plus grand seuil inférieur
ou égal à la valeur source, ce qui permet de convertir les faiblesses Dofus en `-50` ou `-100`.

## Conversion des bonus d'objets

Les bonus et malus Dofus sont convertis de façon symétrique : hors métadonnées, la borne minimale d'une
caractéristique objet est l'opposé de sa borne maximale. Les six caractéristiques principales vont de
`-6` à `+6` sur l'équipement, avec une marge de forgemagie de `2`. Les PA vont de `-5` à `+5`
(forgemagie `1`) et les PM de `-2` à `+2` (forgemagie `1`).
Le critique d'objet suit la même convention signée (`-3..3`). Les bonus de PV et d'initiative restent
sans plafond d'équipement ; leur forgemagie est limitée respectivement à `20` et `3`.

Les résistances fixes de bouclier sont limitées à `±7` hors forgemagie, avec `±3` supplémentaires en
forgemagie.

Les résistances en pourcentage des objets individuels ne produisent aucun palier Krosmoz. Seuls les bonus
de panoplie sont convertis : `< -50 %` donne `-2`, `-50..-20 %` donne `-1`, `-19..7 %` donne `0`,
`8..12 %` donne `1` et `>= 13 %` donne `2`.

L'ID DofusDB `0` n'est pas associé aux PV d'objet, car il est réutilisé par de nombreux effets techniques.
Les dommages fixes multi-éléments utilisent l'ID caractéristique `16` (`allDamageBonus`), et non l'ID `103`
(`weaponPower`).

## Frontend

- Store Pinia : `resources/js/Composables/store/useCharacteristicsPiniaStore.js`.
- Affichages : composants `Characteristic*` dans les Atoms/Molecules.
- Admin : pages `resources/js/Pages/Admin/characteristics/`.

## Lien avec d'autres features

- Scrapping : conversion DofusDB → caractéristiques Krosmoz.
- Effets : effets de sorts/objets liés à des caractéristiques.
