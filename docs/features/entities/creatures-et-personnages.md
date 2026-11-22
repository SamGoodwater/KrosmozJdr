# Créature, monstre, PNJ, PJ

Trois façades partagent le même corps **`Creature`** (niveau, caractéristiques, PV, PA/PM, sorts liés, capacités, etc.). On ne mélange pas les façades.

| | **Monstre** | **PNJ** | **PJ** (modèle à venir) |
| --- | --- | --- | --- |
| Coquille | `Monster` | `Npc` | (personnage joueur) |
| Corps | `Creature` | `Creature` | `Creature` |
| Niveau + caractéristiques | oui | oui | oui |
| Classe (passif, capacités de classe, **variantes** de sorts) | non | oui | oui |
| Spécialisation (langues, capacités, aptitudes, métiers / savoir-faire…) | non | oui | oui |
| Sorts | catégorie **créature**, **sans** variantes de classe ; kit court | sorts de **classe** (variantes du grimoire) | idem PNJ |
| Capacités / aptitudes | possibles | via classe + spe | via classe + spe |
| Traits | oui (catalogue) | possibles | possibles |
| Équipements / panoplie | **très rare** | oui (kit porté) | oui |
| Ressources / consommables | **très rare** | oui | oui |

## Ce qui est partagé

Tout ce qui vit sur **`Creature`** : niveau, les six caracs et dérivés (PV, CA, initiative…), hostilité éventuelle, liaisons sorts / capacités / traits, et — si on l’utilise — stuff ou butin.

Calibrage commun : gabarit [5.1.2](../../../private/game/rules/5-Ressources-et-equilibrage/5.1-ressources-mj/5.1.2-creation-de-pnj-et-monstres.md), chartes créature.

## Monstre

Rencontre. Pas de classe ni de spécialisation.

- Sorts de **créature** (pas de variantes d’emplacement de classe) : repères souples selon la puissance — voir [sorts-depuis-fiche.md](./sorts-depuis-fiche.md).
- Capacités et, pourquoi pas, aptitudes.
- Traits du catalogue (souvent 0–2, parfois plus s’ils collent).
- Équipement / ressource / conso : exceptionnel (boss unique, quête).

## PNJ

Personnage du récit. Structure proche d’un PJ :

- **Classe** → passif, capacités de classe, grimoire avec **variantes** de sorts.
- **Spécialisation** → langues, capacités, aptitudes, métiers / savoir-faire, compétences…
- **Équipements** (1 objet / emplacement, 2 anneaux), panoplie liée éventuelle, ressources, consommables.
- Niveau + caractéristiques (mêmes briques qu’un monstre).

On ne remplit pas tout le grimoire pour un aubergiste : on pose la structure, on équipe ce que la scène demande. Les sorts restent des sorts de **classe**, jamais le kit bestiaire.

## PJ

Même logique que le PNJ (classe + spe + stuff + caracs). Le modèle dédié n’est pas encore en base : bien faire les PNJ aujourd’hui, c’est déjà tracer le chemin des PJ.

## Sorts : ne pas confondre

| | Classe (PJ / PNJ) | Créature (monstre / invocation) |
| --- | --- | --- |
| Variantes d’emplacement | oui | non |
| Budget | kit large (~30–32) | kit court (1–4) |
| Catégorie | `0` classe | `1` créature |

Détail création : atelier Création (Sorts, Monstres, PNJ) · `resources/ia/creation-guides/`.
