# Catalogue objets et pré-filtre

Les objets sont le **socle** : sans un petit référentiel `playable`, PNJ et rencontres n’ont rien de cohérent à porter. Ce n’est **pas** un import massif de tout Dofus.

Commande : `php artisan ia:equipment-grid` (rapport). `--write` crée les **trous** en `draft` (`official_id` `ia-grid:slot:voie:niveau`), jamais `playable`. Config : `resources/ia/equipment-grid.json`. Les fiches Dofus ne sont pas réécrites.

## Pourquoi pas tout Dofus

En JDR, un type d’équipement n’a que **3 ou 4 caractéristiques possibles**. Après conversion, des dizaines d’items Dofus **collapsent** vers la même signature (type + niveau + bonus).

Le code sait déjà :

- filtrer les bonus incompatibles avec le type (`allowed_item_type_ids`, `ItemEffectsToBonusConverter`) ;
- snaper aux normes (`NormsResolver`) ;
- hasher une signature (`DuplicateEquipmentSignatureChecker`).

Un quota « une dizaine d’objets par niveau » est le bon **ordre de grandeur** s’il décrit une **couverture**, pas 10 items tirés au hasard.

## Grille cible

Remplir les cases **niveau × slot × voie** (Terre / Feu / Eau / Air, parfois Neutre), pas un tas Dofus.

| Idée | Volume |
| --- | --- |
| ~8–15 objets par niveau (slots utiles × voies) | **200–400** `playable` pour tout le jeu |
| 10 par niveau × 30 | ~300, cohérent |
| 10 par *type* et par niveau | encore trop |

Référence d’équilibrage : `private/game/rules/5-Ressources-et-equilibrage/5.2-principes-d-equilibrage/5.2.4-equipements-et-panoplies.md`.

## Pipeline objets (surtout algorithmique)

1. Convertir Dofus (pipeline scrap actuel).
2. Classer dans la grille (niveau clampé 1–20 × slot × voie). La voie se lit sur `effect` (JSON converti) ou, à défaut, sur le tableau d’effets Dofus brut encore stocké dans `bonus` (Force / Int / Chance / Agi, dégâts élémentaires).
3. Pour chaque case, **garder un représentant** sans le modifier (identité Dofus inchangée). En cas de doublon : source Dofus, puis état `playable` > `draft` > `auto` > `raw`.
4. Trou : `ia:equipment-grid --write` crée un objet **sans** `dofusdb_id`, état `draft`, bonus de palier (règle 5.2.4 : 1 / 2 / 3 / 4 selon la bande de niveaux). Accessoires et armures : carac de voie. Armes : dégâts élémentaires. Relancer `--write` est idempotent.

Le filtre « 3–4 caracs autorisées du type » (`item_type_dofus_ids`, aujourd’hui souvent **cape seule** pour Force) s’applique au scrap, pas à la classification de grille : un anneau Dofus avec de la Force reste un candidat Terre, même si la conversion JDR a droppé le bonus. Les trous générés portent volontairement la carac de voie (à relire).

L’IA **ne réécrit pas** le nom, la description ni les caracs d’un item Dofus (gel admin / JSON). Elle n’intervient que pour un **unique de scénario** (sans source) ou un cas que l’algo ne tranche pas. Un snap algo peut aller en revue humaine légère ; `auto` n’est obligatoire que s’il y a eu une passe LLM. Ne pas publier `playable` tout seul.

## Pré-filtre pour le LLM (pas d’API agent)

Le modèle **ne reçoit pas** tout le catalogue. Laravel envoie une liste courte, par exemple pour un Iop Terre niveau 8 : `playable`, niveau 6–8, types portables, bonus dans { Force, Vitalité, dégâts Terre, éventuellement PA }.

Forme compacte (1–3 k tokens, 20–40 lignes) :

```json
[
  {
    "id": 412,
    "name": "Anneau du Bouftou",
    "type": "anneau",
    "level": 7,
    "bonuses": { "strength": 2, "vitality": 1 }
  }
]
```

Consigne : *tu ne crées pas d’objet ; tu renvoies des `id` ; un item par slot ; cohérence Terre / Force.*

Le validateur revérifie ids, niveau, slots, voie. Id inventé → retry.

Même idée pour les **sorts de classe** d’un PNJ : liste `playable` préfiltrée (classe, élément, niveau ≤ N).

## API catalogue (quand on la fera)

Besoin réel : recherche **côté Laravel** (et plus tard UI MJ), pas un agent qui lit une doc d’API.

- Uniquement `playable`.
- Payload compact (pas l’arbre d’effets ni les colonnes TanStack).
- Filtres objets : type, niveau min/max, caractéristique de bonus, élément / voie, rareté.
- Filtres sorts : classe, élément, niveau, rôle (dégâts, soin, contrôle…).
- Auth service / admin, pas les APIs de table actuelles.

Les routes caractéristiques existent déjà (`/api/characteristics`, normes, table de référence). Il faudra une **vue compacte** pour le prompt, pas un dump du référentiel.

## Étalons manuels

Une vingtaine de fiches `playable` **par type** au moment où l’IA touche ce type. Pour les objets, l’algo peut produire le volume ; les étalons servent surtout de few-shot **si** une passe LLM existe (flavour, uniques). Ne pas réécrire tout Dofus à la main avant de commencer.

### Kit d’étalons niveau 8 (en base, à relire)

32 objets `playable` : **4 éléments × 4 raretés**, sur les deux seuls emplacements qui portent un élément.

**Contrainte structurelle** : Force / Intelligence / Chance / Agilité ne sont autorisées que sur la **cape**, les dégâts fixes élémentaires que sur les **armes** (pivot `characteristic_object_item_type`). Anneaux, amulettes, chapeaux, ceintures et bottes n’ont **aucune** caractéristique élémentaire : l’axe « par élément » ne s’applique pas à eux.

Valeurs au niveau 8, dérivées des `norms_grid` (colonne de puissance) puis écrêtées par la `formula` du pivot :

| Rareté | Puissance | Cape (carac de voie) | Arme (dégâts de voie) |
| --- | --- | --- | --- |
| 0 Commun | `weak` | 1 | 1 |
| 1 Peu commun | `neutral` | 2 | 1 + touche 2 |
| 2 Rare | `strong` | 3 + initiative 2 | 2 + touche 3 |
| 3 Très rare | `very_strong` | 3 + initiative 2 + sauvegarde 1 + PV max 4 | 2 + touche 3 + dégâts neutres 2 |

Au niveau 8 la `formula` plafonne la carac de voie à 3 et l’initiative à 2, en dessous de ce que réclamerait `very_strong` : `strong` et `très rare` se différencient donc par le **nombre de secondaires**, pas par la valeur principale. Raretés 4 (Légendaire) et 5 (Unique) restent réservées aux Dofus et trophées (règle 5.2.4.4).

Écriture : le JSON à clés courtes va dans **`bonus`** *et* `effect`. Le front fusionne `[bonus, effect]` et **`bonus` gagne** en cas de doublon (`buildCharacteristicEffectCell`) : écrire seulement `effect` laisserait afficher les valeurs DofusDB brutes. `auto_update` passe à `false` pour qu’un rafraîchissement de scrap n’écrase ni les bonus ni la rareté.

### Rejouer le socle d’objets (base ↔ seeder)

Les objets relus sont **versionnés** en JSON, un fichier par item, sous `database/seeders/data/entities/items/`. On peut donc recréer ce socle sur n’importe quelle base avant de faire tourner l’IA, et repartir de la base après une session de relecture.

| Sens | Commande | Bouton admin |
| --- | --- | --- |
| Base → fichiers | `php artisan items:seeder-export` (`--prune`, `--state=`, `--all`, `--id=`) | `/admin/content/ia-generation` → « Base → fichiers » |
| Fichiers → base | `php artisan items:seeder-import` (`--dry-run`) | `/admin/content/ia-generation` → « Fichiers → base » |

`Database\Seeders\Entity\ItemSeeder` rejoue les mêmes fichiers, donc `project:seed` et `project:init` reconstruisent le socle sans scrapping. L’upsert se fait sur `dofusdb_id` (`official_id` à défaut) et le type est résolu par `item_type_dofus_id`, ce qui rend les fichiers portables entre environnements. Détail du format : [`database/seeders/data/README.md`](../../database/seeders/data/README.md).

L’export est déterministe (relancer sans changement ne produit aucun diff Git) et exclut `image`, dont la colonne contient une URL liée à l’environnement. Les boutons admin sont réservés au super administrateur ; l’écriture dans le dépôt n’est possible qu’en développement.

### Panoplies bas niveau (playable)

Sets Dofus emblématiques des premiers niveaux, pièces **et** bonus de set relus :

| Panoplie | Niveau | Voie / thème | Signature |
| --- | --- | --- | --- |
| Piou Vert / Rouge / Bleu / Jaune | 1 | Terre / Feu / Eau / Air | Ceinture +1 tacle ou fuite ; **set complet** = carac de voie + portée |
| Piou Rose | 1 | Soins | Anneau +1 soin ; **set complet** = soins + PV |
| Piou Violet | 1 | Portée | **Set complet** = +1 PO |
| Bouftou | 2 | Terre | Marteau +1 dgt terre, bouclier +1 CA, ceinture +1 tacle, coiffe +1 intimidation ; **8p** = force + PA |
| Tofu | 2 | Air | Baguette +1 dgt air, ceinture +1 fuite, kaskofu +1 acrobaties ; **7p** = agilité + PM |
| Prespic | 3 | Sagesse | Anneau +1 soin, bouclier +1 CA, cape +1 discrétion ; **5p** = sagesse |
| Sanglier | 1–2 | Terre | Ceinture +1 tacle ; **3p** = vitalité |
| Mousse (Éponge) | 2 | Eau | Pelle +1 dgt eau, bouclier +1 CA, ceinture +1 fuite, coiffe +1 survie ; **8p** = chance |
| Arakne | 1 | Terre | Hache +1 dgt terre, ceinture +1 tacle ; 4p = force |
| Moskito | 1–2 | Eau | 4p = chance (pas d’arme ni de ceinture) |
| Champ Champ | 1 | Terre / PV | 2 anneaux ; 4p = vit + PV |
| Bandit | 1 | Air | Dagues +1 dgt air, ceinture +1 fuite ; 4p = agilité |
| Jeune Aventurier | 1 | Mixte / starter | Ceinture +1 tacle ; 6p = PV + initiative |
| Paysan | 1–2 | Terre | Faux +1 dgt terre, ceinture +1 tacle ; **7p** = force + PV |
| Larvesque | 2–4 | Feu / soins | Baguette +2 dgt feu ; **5p** = int + soins |
| Bouftou Royal | 2–3 | Terre | Épée +2 dgt terre, ceinture +2 tacle, bouclier +1 CA ; 8p = +1 PA |
| Abraknyde | 4 | Terre | Cape +1 force, bâton +2 dgt terre ; 7p = force + CA |
| Kwak Flammes / Glace / Terre / Vent | 4 | Feu / Eau / Terre / Air | Cape +1 carac, épée +2 dgt, ceinture tacle ou fuite ; 7p = carac + PO |
| Scara Vert / Rouge / Bleu | 4 | Terre / Feu / Eau | Cape +1 carac, ceinture tacle ou fuite ; 4p = carac + CA |
| Scara Blanc | 4 | Sagesse / initiative | Cape +1 initiative, chapeau +1 sag ; 4p = sag + initiative |
| Akwadala | 3–4 | Eau | Cape +1 chance, bâton +2 dgt eau, bouclier +1 CA ; 8p = chance + PO |

À ces niveaux, Force / Int / Chance / Agilité sont encore **plafonnées à 0** sur la cape aux niv. 1–2 (formule) : la voie est portée par le **bonus de set complet**, pas par la pièce (une seule carac de voie, jamais For+Int sur le Bouftou). Les pièces peuvent rester vides. Quelques chapeaux / capes portent +1 compétence (intimidation, acrobaties, discrétion, survie) même si la `formula` objet des compétences est à 0 : impact faible, volontaire. À partir du niv. 3 la cape peut prendre +1 carac. `auto_update = false`. JSON panoplies : `database/seeders/data/entities/panoplies/` (`PanoplySeeder`, après `ItemSeeder`).
