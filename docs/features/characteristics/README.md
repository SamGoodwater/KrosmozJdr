# Caractéristiques

Les caractéristiques décrivent les valeurs numériques et règles de lecture des créatures, objets et sorts : niveaux, dégâts, PA/PM/PO, limites, formules et normes.

Pour le modèle **base + objets + contexte**, les formules saisies et le sélecteur de niveau, voir
[COMPUTED_VALUES.md](./COMPUTED_VALUES.md).

## Backend

- Définitions et lecture : `app/Services/Characteristics/`, `app/Support/Characteristics/`.
- Limites : `app/Services/Characteristic/Limit/CharacteristicLimitService.php`.
- Formules : services sous `app/Services/Characteristic/` (`FormulaExpressionParser`, `LevelDomainResolver`).
- Runtime créature : `app/Services/Creature/Runtime/CreatureRuntimeStatsService.php`.
- Seeders : `CharacteristicSeeder`, `CreatureCharacteristicSeeder`, `ObjectCharacteristicSeeder`, `SpellCharacteristicSeeder`.
- Qualité des définitions : `app/Services/Characteristics/CharacteristicDefinitionQualityService.php` vérifie notamment les `norms_grid`, formules de conversion et restrictions d'équipement.
- Reprise des totaux existants : `php artisan creatures:derive-context-bonuses`.

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

## Conversion des caractéristiques de sorts

Les paramètres globaux de DofusDB sont ramenés aux limites jouables de Krosmoz : niveau minimal ÷ 10 sur
une échelle 1–20, portée 0–20, coût 0–12 PA, 6 lancers par tour, 4 par cible et 10 tours de relance au
maximum.

Les valeurs utilisées par les sous-effets suivent les mêmes unités que les créatures et les objets :

- critique temporaire signé de −3 à +3 ;
- résistances relatives limitées aux paliers −100, −50, 0, 50 et 100 ;
- résistances fixes de 0 à 10 ;
- bonus de portée de 0 à 6 et bonus de soin de 0 à 7 ;
- initiative directe sans plafond artificiel ;
- dégâts fixes élémentaires ou multi-éléments de 0 à 5.

Une entrée nulle reste nulle pour toutes les formules numériques. Les zones, le temps d'incantation et la
durée sont des notations textuelles, pas des quantités numériques. Les restrictions de ligne et diagonale,
le type de cible, le cumul maximal et le délai global disposent désormais de définitions dédiées.

### Budgets dégâts et soins proportionnels aux PV

Le référentiel `private/game/resources/Creation sort.pdf` exprime les dégâts et soins comme un budget
total par tour, proportionnel aux PV moyens d'une créature équivalente :

- dégâts moyens : environ 15 % des PV au niveau 1 jusqu'à 40 % au niveau 20 ;
- soin maximal : `dégâts max × 0,35 + 2 + modificateur de Vitalité` ;
- vol de vie : moitié du budget de dégâts, car il combine dégâts et récupération.

Ce total n'est pas appliqué directement à chaque sous-effet. Il est réparti selon la part de PA consommée
par le lancement, puis entre les différentes lignes de dégâts ou de soin. La mêlée monte d'un palier de
puissance ; la longue portée et la zone descendent chacune d'un palier. Un sort hybride dégâts + soin
partage son budget entre ses deux familles d'action.

Les `norms_grid` de `dommages_spell`, `soin_spell`, `bouclier_spell`, `pv_temporaires_spell` et
`vol_vie_spell` représentent désormais un lancement de référence à 3 PA. Les boucliers et PV temporaires
réutilisent le budget de soins. Les données `action_budget` conservées sur le sous-effet expliquent le
niveau, les PA, le palier et les budgets par tour et par lancement utilisés.

Les compétences actives (18 clés `*_spell`, ex. `acrobatics_spell`) sont utilisables dans les sous-effets
`booster` / `retirer` / `voler-caracteristiques` via la catégorie `skill` de `config/effect_sub_effects.php`.
Plafond ±5, sans passives spell.

## Frontend

- Store Pinia : `resources/js/Composables/store/useCharacteristicsPiniaStore.js`.
- Affichages : composants `Characteristic*` dans les Atoms/Molecules.
- Admin : pages `resources/js/Pages/Admin/characteristics/`.

## Lien avec d'autres features

- Scrapping : conversion DofusDB → caractéristiques Krosmoz.
- Effets : effets de sorts/objets liés à des caractéristiques.
