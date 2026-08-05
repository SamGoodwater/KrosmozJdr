# Ce qui a été fait

## Août 2026 — Conversion Dofus plus fiable

Le socle d'import Dofus a été simplifié pour rendre les imports massifs plus sûrs :

- les valeurs numériques suivent désormais les formules et limites administrables des caractéristiques ;
- un élément incorrect n'interrompt plus tout un lot ;
- les effets et caractéristiques inconnus sont signalés pour revue au lieu d'être perdus ;
- une commande d'audit vérifie le paramétrage avant un import massif ;
- l'administration indique les mappings incomplets et les prévisualisations exposent les étapes de conversion.

Cette évolution ne cherche pas à deviner toutes les exceptions Dofus. Elle privilégie une donnée brute
importable et clairement signalée lorsqu'une règle métier doit encore être définie.

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
