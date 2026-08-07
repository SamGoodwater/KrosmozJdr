# Ce qui a été fait

## Août 2026 — Caractéristiques calculées (base / objets / contexte)

Les fiches de créatures peuvent désormais composer une caractéristique à partir de sa formule
de base, des bonus d’équipement et d’un bonus contextuel (nombre ou formule). Un total stocké
reste prioritaire pour ne pas casser le scrap. Le niveau peut être une fourchette ou un dé :
l’interface affiche la première valeur possible et recalcule toute la fiche via un sélecteur.
Un popover détaille la décomposition et le tableau par niveau. Les formulaires monstre / objet /
sort ont été allégés (sections repliables).

## Août 2026 — Gates et préparation scrap serveur

Le pipeline d’import est verrouillé pour un scrap catalogue complet :

- audits live : conversion effets à 100 %, 0 mapping caractéristique manquant ; ~33 % d’effets `autre`
  surtout glyphes / pièges / placeholders (hors périmètre volontaire) ;
- 80 écarts qualité objet corrigés (`item_type_dofus_ids` dérivés des aides) ;
- quality gate **active par défaut** sur `scrapping:run` (hors simulate) + gate effets après import sorts ;
- samples créature / objet alignés sur les formules + tests automatiques ;
- mappings téléports / échanges ajoutés ; checklist scrap serveur documentée ;
- workflow CI dédié aux gates scrapping.

## Août 2026 — Référence DofusDB pendant l’édition

Sur les fiches **Full** et les écrans d’**édition** (page ou modal), une action avec l’icône Dofus
ouvre un **panneau flottant** de référence lorsque l’entité a un `dofusdb_id`. Le panneau affiche
le lien profond vers DofusDB et ouvre le site dans une fenêtre séparée (pas d’iframe), sans bloquer
l’édition de la fiche Krosmoz.

## Août 2026 — Almanax interactif dans le header

Le badge Almanax du header affiche désormais le **jour** (identique au calendrier réel) avec le mois
Dofus, un **tooltip** au survol, et un **calendrier** au clic pour parcourir les mois du Monde des Douze.

## Août 2026 — Conversion Dofus plus fiable

Le socle d'import Dofus a été simplifié pour rendre les imports massifs plus sûrs :

- les valeurs numériques suivent désormais les formules et limites administrables des caractéristiques ;
- un élément incorrect n'interrompt plus tout un lot ;
- les effets et caractéristiques inconnus sont signalés pour revue au lieu d'être perdus ;
- une commande d'audit vérifie le paramétrage avant un import massif ;
- l'administration indique les mappings incomplets et les prévisualisations exposent les étapes de conversion.

Cette évolution ne cherche pas à deviner toutes les exceptions Dofus. Elle privilégie une donnée brute
importable et clairement signalée lorsqu'une règle métier doit encore être définie.

### Fiches de sort enrichies

Les fiches de sort conservent maintenant les contraintes globales de lancer en ligne ou en diagonale, le
mode direct/piège/glyphe, la limite de cumul et le temps de relance global. Ces informations sont importées
depuis le premier niveau DofusDB et restent séparées du moteur détaillé des effets.

### Première calibration des monstres

Le niveau des monstres suit maintenant une conversion lisible : dix niveaux Dofus correspondent à
un niveau Krosmoz, dans une plage de 1 à 30. Une analyse des statistiques de l'ensemble du catalogue
DofusDB sert de base à la calibration progressive des autres caractéristiques.

Les autres caractéristiques des monstres disposent désormais de leurs propres marges : scores jusqu'à 30,
PA/PM/PO élargis, bonus tactiques conservés et initiative non plafonnée. Les résistances en pourcentage
ont été remplacées par cinq paliers faciles à jouer, tandis que critiques et soins suivent des conversions
simples et limitées. Les Kamas ne sont plus importés sur les monstres.

La chaîne d'import conserve maintenant aussi la description, le tacle, la fuite, les critiques et les soins.
Les races DofusDB sont reliées par leur identifiant source (et créées en brouillon si nécessaire). Les règles
JSON, leur copie de secours PHP et la base sont synchronisées et contrôlées par les audits automatisés.

### Calibration des objets

Les bonus d'équipement Dofus conservent désormais leurs malus : les conversions sont symétriques autour
de zéro. Les plafonds distinguent l'équipement de la forgemagie, notamment ±6 pour les caractéristiques
principales, ±5 PA et ±2 PM hors forgemagie. Les résistances relatives ne sont transformées en paliers que
pour les panoplies. Les faux PV issus de l'identifiant technique DofusDB 0 sont ignorés et les dommages
multi-éléments utilisent leur véritable identifiant source.

La calibration objet a ensuite été consolidée : chaque bonus réservé à un emplacement pointe vers le bon
type DofusDB, les grilles et exemples respectent leurs plafonds et reproduisent exactement les formules.
Les PV et l'initiative restent non plafonnés, le critique accepte les malus jusqu'à -3 et les résistances
fixes des boucliers sont limitées à ±7 hors forgemagie. Ces règles sont synchronisées en base et couvertes
par les contrôles automatiques.

### Calibration des sorts

Les caractéristiques de sorts utilisent maintenant des unités Krosmoz cohérentes avant leur branchement
au système d'effets : niveau 1–20, limites jouables de portée et de cadence, critique signé de −3 à +3,
résistances relatives par paliers et résistances fixes plafonnées à 10. Les dégâts fixes, soins, bonus de
portée et initiative ont également été harmonisés avec les créatures et les objets.

Cinq métadonnées manquantes ont été définies : lancement en ligne, lancement en diagonale, type de cible,
cumul maximal et délai global. Un contrôle automatique vérifie désormais les bornes, les exemples et
l'invariant « zéro reste zéro » sur l'ensemble des définitions de sorts.

La conversion des effets a ensuite été sécurisée : les plages DofusDB ne sont plus confondues avec des
jets de dés, et les formules exécutées en jeu utilisent désormais les valeurs Krosmoz converties, y compris
en critique. Les bonus, retraits et vols sont distingués correctement ; les paliers de résistance restent
valides et le vol de vie utilise sa courbe réduite dédiée.

Les dégâts, soins et vols de vie sont maintenant proportionnés aux PV de référence du niveau. Le budget
total d'un tour est réparti selon le coût en PA, la portée, la zone et les différentes lignes d'effet du
sort, sur la base du premier équilibrage fourni dans « Creation sort ».

Les effets de bouclier DofusDB sont enfin reconnus comme `protéger` (et non plus comme boosts génériques),
puis budgétés comme les soins pour rester proportionnés aux PV.

La résolution des sorts (jet d'attaque, sauvegarde ou réussite auto) et le booléen Wakfu/physique sont
désormais déduits des effets convertis, car DofusDB ne fournit pas `isMagic`. Les DD de sauvegarde suivent
la formule des règles : 8 + modificateur + maîtrise.

Les états (`appliquer-etat` / `s-appliquer-etat`) propagent correctement leur durée vers
`duration_formula`, mettent à jour les pivots existants au réimport, et alimentent le référentiel
`Condition` avec leurs flags (immobilisation, invulnérabilité, etc.).

Les déplacements distinguent mieux push / pull / téléport (y compris « sans dommages »), et le triage
des mappings restants sépare les clés jouables des effets hors périmètre documentés.

Le snapshot des mappings scrapping a été reconstruit depuis les JSON d’entité (clés item/spell
réalignées). L’audit ignore les catalogues sans cible (`monster-race`). Les sorts encore présents
sur DofusDB ont été resynchronisés : résolution inférée, DD en `8 + …`, couverture d’effets à 100 %.

Les PV temporaires de sort sont désormais distincts du bouclier : sous-effet `donner-pv-temporaires`,
caractéristique `pv_temporaires_spell` (charac DofusDB 95), même enveloppe de budget que les soins.
Le max PV via sort reste porté par la vitalité.

Les compétences actives (18) peuvent être boostées ou retirées par un effet de sort
(`acrobatics_spell`, `stealth_spell`, etc.), sur le même modèle que l’équipement, plafonnées à ±5.

Les sorts locaux absents de l’API DofusDB (404, ex. anciennes flèches Cra) ont été passés en
`archived` avec `auto_update=false` : conservés pour historique, exclus des resync automatiques.
