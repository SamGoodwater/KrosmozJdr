# Construire un sort de classe depuis sa fiche

On choisit d’abord le sort Dofus de départ, on écrit la fiche, puis on la recopie dans le catalogue. Une fois écrite, la fiche (Obsidian) fait foi. Le repère de niveaux (5/10/15/20, etc.) ne sert que lorsqu’une fiche n’a pas encore ses lignes.

Un sort se construit **un par un**. Le catalogue JSON puis l’import ne devinent pas une case vide : ils recopient le sort de base. C’est pour ça qu’un palier peut perdre un lancer, une portée ou une relance même si la ligne d’au-dessus était juste.

## Pourquoi une passe globale rate des colonnes

Il y a beaucoup de détail : trois classes, une trentaine de sorts, plusieurs paliers, une quinzaine de colonnes. Ce n’est pas la seule raison. Les catalogues ont été réécrits en plusieurs passes (repère commun, puis dés, puis niveaux de la fiche) sans une liste de colonnes à cocher. Et l’importeur ne reprend pas la ligne précédente : une clé absente d’un palier retombe sur le **sort de base**.

## Choisir le sort Dofus de départ

Cette étape vient avant la fiche. Chaque sort de Krosmoz part d’un sort de la même classe dans Dofus (page de la classe sur [DofusDB](https://dofusdb.fr/fr/database/breeds/8), ici l’Iop).

Une classe a deux familles de sorts, et elles ne viennent pas du même tas.

| Famille | Nombre | Places | Source Dofus |
| --- | --- | --- | --- |
| Élémentaire | 16 | 4 par voie (Feu, Terre, Air, Eau), aux niveaux 1, 4, 7 et 12 | sort de cet élément **qui fait des dégâts** |
| Général | 16 | 8 choix de 2 variantes (deux au niveau 1, puis 2, 5, 6, 8, 10, 11) | sort **sans élément strict et sans dégâts** |

### 1. Trier les sorts Dofus de la classe

Lister chaque sort avec son élément, s’il fait des dégâts, et son effet secondaire. Puis faire deux tas :

- **dégâts d’un élément** : tas élémentaire de cet élément ;
- **pas de dégâts, pas d’élément strict** : tas général.

C’est l’élément et les dégâts qui décident, pas l’orientation. Un sort Dofus sans dégâts reste dans le tas général, même si son effet colle à une voie (un bonus de dégâts colle à la voie Amélioration, mais ne devient pas un sort Feu pour autant). C’est l’erreur qui a mélangé l’Iop : des sorts généraux rangés dans les voies, des sorts élémentaires sans dégâts.

### 2. Les sorts élémentaires

Pour chaque voie, prendre 4 sorts dans le tas de son élément, puis les ranger aux niveaux 1, 4, 7 et 12 du plus simple au plus fort.

1. Préférer un sort dont l’effet secondaire suit l’orientation de la voie (repousse pour Placement, retrait pour Entrave, bouclier pour Protection…).
2. Si aucun ne la suit, garder un sort de l’élément qui fait des dégâts, et adapter son effet à l’orientation. Les dégâts et l’élément restent ceux de Dofus. L’effet peut s’en éloigner.
3. Chaque sort élémentaire de Krosmoz fait des dégâts de l’élément de sa voie.

### 3. Les sorts généraux

Prendre 16 sorts dans le tas général, puis les mettre par paires sur les 8 choix.

1. Viser des sorts utiles à plusieurs voies : déplacement, protection, amélioration, entrave, soin.
2. Les deux variantes d’un même choix n’ont pas la même orientation.
3. Répartir les orientations sur les 8 choix, pour que chaque voie trouve des généraux qui l’aident.
4. Une exception est possible (un sort Dofus à dégâts devenu général, deux variantes de même orientation) si l’adaptation la justifie. L’écrire dans *Général* ou *Effets*.

### 4. Vérifier le kit avant d’écrire les tableaux

- 32 sorts, un par place.
- Les 16 élémentaires font des dégâts de l’élément de leur voie.
- Les 16 généraux ne font pas de dégâts, sauf exception écrite.
- Aucun choix général n’a deux variantes de même orientation, sauf exception écrite.
- Le sort Dofus de départ est connu pour chaque sort (`dofusdb_id` dans le catalogue JSON quand il existe).

Ensuite seulement, on écrit les tableaux de la fiche, puis on suit les étapes ci-dessous.

## Avant de recopier

Comparer le nom **et** le niveau d’apprentissage. S’ils diffèrent, c’est un autre sort : on le construit, on n’écrase pas l’homonyme. `--apply` ne le fait pas non plus.

Les sorts généraux choisissent leurs paliers. Un 9, un 14 ou un 16 écrit sur la ligne est le bon niveau, même s’il n’est pas dans le repère 7/14/20.

## Une ligne de fiche

1. Lire l’en-tête du tableau. Les colonnes ne sont pas toujours dans le même ordre, et « Tours avant relance » est parfois absent. Une colonne absente vaut le défaut (relance 0).
2. Une case vide reprend la ligne du dessus. Sur la première ligne, une case vide vaut le défaut : PA 3, portée au contact, une case, ligne de vue, 2 lancers par tour, relance 0.
3. Si « /cible » n’est jamais écrit, il vaut le « /tour » **de cette ligne**. S’il est écrit une fois, les cases vides suivantes gardent ce nombre, même quand /tour change.
4. Une portée écrite `1-4`, avec ou sans « m », est modifiable. `0` ou `0-0` ne l’est pas. `1` seul veut dire 1–1. « càc » n’est pas un nombre à inventer.
5. Le dé de la case n’écrit pas le modificateur d’élément : il s’ajoute quand même (`1d6` de Terre devient `1d6+Force`), sauf si la fiche dit le contraire.
6. Le commentaire du sort (*Effets*) cite les mêmes niveaux que les lignes. « dès 16 » est faux si le palier est 17 ou 18.
7. La fiche ne donne pas le critique. On ne laisse pas l’ancienne courbe de critique à côté du nouveau dé.

## Chaque colonne, un champ

| Fiche | Champ |
| --- | --- |
| PA | `pa` |
| PO min–max | `po_min`, `po_max` |
| « m » ou une portée au-dessus de 0 | `po_editable` vrai ; 0–0 = faux |
| Ligne de vue vide | `sight_line` vrai |
| « sans » | `sight_line` faux |
| « en ligne » | `cast_in_line` vrai |
| /tour | `cast_per_turn` |
| /cible | `cast_per_target` |
| Tours avant relance | `number_between_two_cast` |
| Zone `2x2`, `1xN`, `croix N` | `area` du sous-effet (`rect-2x2`, `line-1xN`, `cross-0-N`) |
| « / N tours » ou « pendant N tours » | `duration` du sort et `duration_formula` du sous-effet qui dure |
| « −N PA / PM / PO » | `value` du sous-effet `retirer`, sur le palier où le nombre change |
| « repousse / saut / attire de N » | `cells_formula` du déplacement de ce type |
| Jet d’attaque vs CA | `resolution_mode` = `attack_roll` |
| DD vs sauvegarde | `resolution_mode` = `saving_throw` |
| automatique | `resolution_mode` = `auto_success` |
| « Dégâts d’Air », voie, tags | élément, caractéristique du jet, types du sort |
| « Si Agi ≥ 3 » / « Intel > Chance de la cible » | `params.conditions` (formules `[caster:…]` / `[target:…]`) |
| « +1 case si … » / « remplace / ajoute / soustrait » | `params.value_rules` (`add` / `replace` / `subtract`) |
| « sur toi » / « toi et les cibles » | `params.applies_to` (`caster` / `caster_and_targets` ; défaut `targets`) |

Sur une fiche Obsidian, note la condition sous *Effets* ou dans une colonne « Autres », puis recopie-la dans l’éditeur (bloc Conditions + règles de valeur). Les sorts généraux d’une classe suivent les règles de résolution (auto / Sagesse / Vitalité, quatre exceptions de voie) décrites dans l’atelier Classes.

Un second jet ne remplace pas le premier. « Jet d’attaque, retrait vs Esquive » reste un jet d’attaque. « Automatique pour les allié·e·s » ou « pour un allié consentant » ne se coche pas : une cible consentante ne fait jamais de jet (règle 3.3.2.3). Le mode principal ne change pas. « Allié·e·s saufs » (pas de dégâts aux allié·e·s, autres effets maintenus) se coche en `spares_chosen_targets` (Épargne).

« AS », un rebond, ou « selon la pièce » restent dans le texte de l’effet. AS veut dire que les alliés de la zone ne sont pas touchés. Ce n’est pas une forme de zone. « En ligne » se lit aussi avec une majuscule.

Sur chaque palier dont une valeur **diffère de la base**, répéter la clé (`cast_per_turn`, `cast_per_target`, `po_max`, `number_between_two_cast`, etc.). Ne pas compter sur le palier d’avant.

## Contrôle

```bash
python3 scripts/audit-class-spell-sheets.py
```

Le script compare PA, portée, portée modifiable, ligne de vue, lancers par tour, lancers par cible, relance, zone géométrique, mode de résolution et durée. Il signale aussi un sort appris à un autre niveau que la fiche, ou absent du catalogue.

Il ne juge pas le texte (Pile/Face, alliés saufs, rebond), ni le montant d’un retrait, ni une repousse. Ça se lit sur la fiche, onglet par onglet, y compris la phrase d’effet au-dessus du tableau.

`--apply` réécrit un sort seulement si le nom et le niveau d’apprentissage sont déjà ceux de la fiche. Il ne crée pas les sorts manquants.

Quand le kit entier change (sorts déplacés, renommés, ajoutés), on reconstruit les deux catalogues d’un coup. `scripts/build-iop-spells-from-sheet.py` le fait pour l’Iop : la fiche donne les lancers et les paliers, une liste dans le script donne pour chaque sort l’emplacement, les types, le `dofusdb_id` déjà en base (pour mettre à jour la ligne existante plutôt que d’en créer une) et ses sous-effets. On copie ce script pour une autre classe, puis on relance l’audit.

Ensuite : importer la classe, ouvrir la fiche, cliquer chaque onglet `Niv`. La ligne d’usage est `spell-facts-timing`, la ligne d’effet est `spell-sub-effect-row`. On vérifie aussi que la phrase libre ne cite pas un autre niveau que l’onglet.

On passe au sort suivant seulement quand celui-ci est juste.
