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
