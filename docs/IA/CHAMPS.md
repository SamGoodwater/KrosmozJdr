# Champs figés vs champs générés

L’IA **ne réécrit pas une fiche Dofus entière**. Elle travaille un **delta JDR** : ce qui est illisible ou incohérent à table. L’identité (nom, description, type, illustration) reste celle de la source.

**Exception : les PNJ.** Il n’y a pas d’import DofusDB. L’IA **crée la fiche complète**, éventuellement à partir d’un PNJ déjà décrit sur un site (encyclopédie, wiki, DofusDB).

Ce contrat alimentera plus tard le JSON Schema (`frozen` vs `writable`).

## Identité — figée dès qu’il y a une source Dofus

| Champ | Figé | Motif |
| --- | --- | --- |
| `name` | oui | On reconnaît l’entité en partie |
| `description` | oui | Flavour officiel, pas une réécriture LLM |
| Type (`item_type_id`, catégorie de sort, race de monstre, classe d’un sort) | oui | Le mapping scrap a déjà tranché |
| Image / icône | oui | Asset Dofus |
| `dofusdb_id` / `official_id` | oui | Traçabilité |
| Niveau source | oui | L’algo aligne les **valeurs**, pas le palier |

Un objet unique de **scénario** (sans source Dofus) n’a pas cette identité : là, l’IA peut proposer nom et description, comme pour un PNJ.

## Caractéristiques

Ce n’est **pas** le LLM qui « refait les stats » en premier.

1. **Algo** (déjà amorcé dans le scrap) : ne garder que les 3–4 caracs autorisées du type, snaper `norms_grid`, jeter le reste, ne **pas** changer l’identité d’une carac (Force reste Force).
2. **IA** : seulement un **écart JDR** encore aberrant après l’algo — retirer une carac hors-sujet, ajuster une valeur « légale » mais idiote. Pas de swap d’élément, pas de redistribution libre du budget.

Les caracs déjà dans les normes et pertinentes **ne bougent pas**.

## Par type

### Objets

| Figé | Algo | IA |
| --- | --- | --- |
| Nom, description, type, image, ids, niveau | Bonus : filtre type, normes, dédoublonnage, case de grille | Quasi rien sur un item Dofus. Uniques de scénario seulement |

Le flavour Dofus **suffit**. On ne demande plus au modèle un nom ou une description « plus vivants » pour le catalogue.

### Sorts de classe

| Figé | Algo | IA |
| --- | --- | --- |
| Nom, classe, élément, image, fantasy | PA / portée / bornes déjà convertis | Texte d’effets : 1 principal + 0–2 secondaires jouables à table. Ajuster un chiffre seulement s’il reste illisible |

Pas de nouveaux types d’effets hors catalogue. Pas de changement de classe ni d’élément.

### Monstres

| Figé | Algo | IA |
| --- | --- | --- |
| Nom, race, image | Gabarit niveau / PV / dégâts (règles 5.1.2) | Simplifier les stats **hors gabarit** (trop de détails Dofus). Rédiger 2–3 sorts-créature cohérents |

On garde **ce** Bouftou. On ne le renomme pas et on ne change pas sa race.

### PNJ — tout créer

Pas de champs figés issus de Dofus. Le paquet JSON porte toute la fiche :

- identité : nom, `story`, `historical`, `age`, rôle, taille, lieu ;
- build : `breed_id`, `specialization_id`, stats créature ;
- kit : `item_ids` et `spell_ids` du pré-filtre `playable` (le modèle **choisit**, il n’invente pas d’objets).

**Source optionnelle (site)** : une page encyclopédie / wiki / DofusDB (URL ou export). Laravel en extrait ce qui est utile (nom, portrait, blurb lore). L’IA s’en sert comme **brief**, puis construit le kit JDR. Ce n’est **pas** un scrap de masse : un PNJ à la fois, quand le MJ en a besoin. Aujourd’hui les PNJ n’ont pas de pipeline DofusDB (`Npc` sans `dofusdb_id`).

## Consigne pour le JSON Schema (plus tard)

- Entité sourcée : le modèle **renvoie seulement les clés `writable`**. Laravel recopie les clés figées depuis la fiche `raw`.
- PNJ (et objet unique sans source) : le modèle remplit l’identité **et** le kit ; le validateur refuse les ids hors liste.
