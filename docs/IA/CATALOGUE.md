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

Le modèle **ne reçoit pas** tout le catalogue. Laravel envoie une liste courte via `App\Services\GenerativeAi\NpcKitCatalog` (commande `php artisan ia:npc-kit-catalog`). Exemple : Iop Terre niveau 8 → `playable`, niveau 6–8, types portables, bonus Force / Vitalité / dégâts Terre.

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

Même idée pour les **sorts de classe** d’un PNJ : `NpcKitCatalog::spells($breedId, $level)` (classe, `character_level` ≤ N, `playable`).

Gabarit 5.1.2 : `NpcStatGabarit` (PV / CA / dés selon palier et rôle). Few-shot PNJ : `NpcKitCatalog::exampleIds()` résout les `official_id` `jdr:npc:incarnam:%` playable (pas d’ids numériques dans `generation.json`).

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

`Database\Seeders\Entity\ItemSeeder` rejoue les mêmes fichiers, donc `project:seed` et `project:init` reconstruisent le socle sans scrapping. L’upsert se fait sur `dofusdb_id` (`official_id` à défaut) et le type est résolu par `item_type_dofus_id`, ce qui rend les fichiers portables entre environnements. Les ressources des recettes de ces items, si elles existent déjà en base (scrapping), passent en `playable` sans autre modification. Détail du format : [`database/seeders/data/README.md`](../../database/seeders/data/README.md).

L’export est déterministe (relancer sans changement ne produit aucun diff Git) et exclut `image`, dont la colonne contient une URL liée à l’environnement. Les boutons admin sont réservés au super administrateur ; l’écriture dans le dépôt n’est possible qu’en développement.

### Panoplies bas niveau (playable)

Sets Dofus emblématiques des premiers niveaux, pièces **et** bonus de set relus :

| Panoplie | Niveau | Voie / thème | Signature |
| --- | --- | --- | --- |
| Piou Vert / Rouge / Bleu / Jaune | 1 | Terre / Feu / Eau / Air | Ceinture +1 tacle ou fuite ; **set complet** = compétence de voie (Athlétisme / Arcanes / Supercherie / Acrobaties) + portée, **sans** carac primaire |
| Piou Rose | 1 | Vitalité | Anneau +1 soin ; **set complet** = PV max (pas de compétence, pas de soins au palier) |
| Piou Violet | 1 | Portée | **Set complet** = +1 PO |
| Bouftou | 2 | Terre **et** Feu | Marteau +1 dgt terre, bouclier +1 CA, ceinture +1 tacle, coiffe +1 intimidation ; **8p** = force + intelligence + PA (comme Dofus, les deux caracs seulement au set complet) |
| Tofu | 2 | Air | Baguette +1 dgt air, ceinture +1 fuite, kaskofu +1 acrobaties ; **7p** = agilité + PM |
| Prespic | 3 | Sagesse | Anneau +1 soin, bouclier +1 CA, cape +1 discrétion ; **5p** = sagesse |
| Sanglier | 1–2 | Terre | Ceinture +1 tacle ; **3p** = vitalité + intimidation |
| Mousse (Éponge) | 2 | Eau | Pelle +1 dgt eau, bouclier +1 CA, ceinture +1 fuite, coiffe +1 survie ; **8p** = chance |
| Arakne | 1 | Terre | Hache +1 dgt terre, ceinture +1 tacle, coiffe +1 intimidation ; 4p = force |
| Moskito | 1–2 | Eau | Galurette +1 persuasion ; 4p = chance (pas d’arme ni de ceinture) |
| Champ Champ | 1 | Terre / PV | 2 anneaux, coiffe +1 survie ; 4p = vit + PV |
| Bandit | 1 | Air | Dagues +1 dgt air, ceinture +1 fuite ; 4p = agilité + discrétion |
| Jeune Aventurier | 1 | Mixte / starter | Ceinture +1 tacle, chapeau +1 perception ; 6p = PV + initiative |
| Paysan | 1–2 | Terre | Faux +1 dgt terre, ceinture +1 tacle, bob +1 dressage ; **7p** = force + PV |
| Larvesque | 2–4 | Feu / soins | Baguette +2 dgt feu, cape +1 médecine ; **5p** = int + soins |
| Bouftou Royal | 2–3 | Terre | Épée +2 dgt terre, ceinture +2 tacle, bouclier +1 CA, cape +1 intimidation ; 8p = +1 PA |
| Abraknyde | 4 | Terre | Cape +1 force, bâton +2 dgt terre ; 7p = force + CA + nature |
| Kwak Flammes / Glace / Terre / Vent | 4 | Feu / Eau / Terre / Air | Cape +1 carac, épée +2 dgt, ceinture tacle ou fuite ; **7p** = carac + PO + Arcanes / Persuasion / Athlétisme / Acrobaties |
| Scara Vert / Rouge / Bleu | 4 | Terre / Feu / Eau | Cape +1 carac, ceinture tacle ou fuite ; **4p** = carac + CA + Athlétisme / Arcanes / Supercherie |
| Scara Blanc | 4 | Sagesse / initiative | Cape +1 initiative, chapeau +1 sag ; **4p** = sag + initiative + perspicacité |
| Akwadala | 3–4 | Eau | Cape +1 chance, bâton +2 dgt eau, bouclier +1 CA ; **8p** = chance + PO + persuasion |
| Champêtre | 1 | Terre / nature | Bâton +1 dgt terre, coiffe +1 nature ; **7p** = force + PV |
| Homme Ours | 2 | Terre | Bâton +1 dgt terre, coiffe +1 intimidation ; **7p** = force |
| Intrépide / Boune | 1 | Mixte / starter | Arme +1 dgt, bouclier +1 CA ; set complet = PV |
| Invisible | 2 | Discrétion | Cape +1 discrétion, bouclier +1 CA ; **3p** = fuite |
| Blop Coco / Griotte / Indigo / Reinette | 5 | Air / Feu / Eau / Terre | Pas de cape : **4p** = carac de voie + Acrobaties / Arcanes / Supercherie / Athlétisme ; ceinture tacle ou fuite |
| Gelax | 6 | Feu | Cape +2 int ; **6p** = intelligence + arcanes |
| Craqueleur | 5–6 | Terre / tank | Cape +2 force, épée +3 dgt terre, bouclier +1 CA ; **7p** = force + CA + intimidation |
| Mulou | 4–6 | Terre | Cape +2 force, hache +3 dgt terre ; **7p** = force + intimidation |
| Koalak | 5–6 | Air | Cape +2 agi, arc +3 dgt air ; **7p** = agilité + PO + discrétion |
| Kitsou | 5–6 | Feu | Cape +2 int ; **4p** = intelligence + arcanes |
| Wabbit | 5–6 | Eau | **3p** = chance + persuasion |
| Cawotte | 5–6 | Eau | **3p** = chance + dressage |
| Tortue | 6 | Tank | Pas de bouclier ; **3p** = CA + vit + survie |
| Chef Crocodaille | 6–7 | Eau | Cape +2/3 chance, épée +3/4 dgt eau, bouclier +1 CA ; **8p** = chance + PO + supercherie |
| Aerdala / Terrdala | 5–8 | Air / Terre | Comme Akwadala : cape + carac, arme + dégâts, bouclier CA ; **8p** = carac + PO + Acrobaties / Athlétisme |
| Scarafeuille Noir | 4–5 | Terre / tank | **4p** = force + CA + intimidation |
| Scarabosse Doré | 4–5 | Feu | Cape +2 int, baguette +3 dgt feu ; **7p** = intelligence + arcanes |
| Boostache | 4 | Feu | Cape +1 int ; **4p** = intelligence + arcanes |
| Kwakwa | 5 | Air | Épée +3 dgt air ; **4p** = agilité + PO + acrobaties |
| Anciens Chafers | 4 | Terre | Marteau +2 dgt terre ; **4p** = force + intimidation |

À ces niveaux, Force / Int / Chance / Agilité sont encore **plafonnées à 0** sur la cape aux niv. 1–2 (formule) : la voie est portée par le **bonus de set complet**, pas par la pièce. Les **Pious** élémentaires n’ont **pas** de carac primaire au palier : Athlétisme / Arcanes / Supercherie / Acrobaties + portée ; le Piou Rose (vitalité) n’a que des PV max. Un set mixte Dofus (Bouftou For+Int) peut porter **les deux** au palier complet seulement, jamais sur les pièces. Les pièces peuvent rester vides. **+1 compétence** (chapeau / cape vides, ou palier complet s’il n’y a pas d’emplacement vide) : impact faible, volontaire, même si la `formula` objet des compétences est à 0. Un set a en général **une** compétence de thème (voie, métier, ou flavour). Le Piou Rose et le Piou Violet n’en ont pas. À partir du niv. 3 la cape prend +1 carac, niv. 5 +2, niv. 7 +3. `auto_update = false`. JSON panoplies : `database/seeders/data/entities/panoplies/` (`PanoplySeeder`, après `ItemSeeder`).

### Liste few-shot panoplies (ce que l’IA doit imiter)

Quand l’IA relit un objet ou un set Dofus, elle ne s’appuie **que** sur les panoplies `playable` (et leurs pièces). C’est cette liste, pas tout le scrap. Noms aussi dans `resources/ia/generation.json` → `entities.item.few_shot_panoplies`, éditables dans l’admin **IA métier** (sélecteur jouable).

**Règles à recopier :**

1. Identité Dofus (nom, illustration) inchangée.
2. Bonus JDR dans `bonus` **et** `effect`, clés courtes, `auto_update = false`.
3. Emplacements : cape = For/Int/Cha/Agi ; arme = dégâts fixes ; ceinture = tacle/fuite ; bouclier = CA ; chapeau = vit/sag ; anneau = soins/PO/invoc ; bottes = PM/initiative.
4. Niv. 1–2 : pas de carac de voie sur la cape → la mettre sur le **set complet**. Pièces vides OK.
5. Set mixte Dofus (Bouftou) : For **et** Int uniquement au palier complet.
6. Compétences : **+1** (une par set). Chapeau vide, sinon cape vide, sinon palier complet. Impact faible volontaire. Piou Rose / Violet : aucune.
7. Pas de raretés croisées à bas niveau.

**Sets or :** Piou (6), Bouftou, Tofu, Prespic, Mousse, Sanglier, Arakne, Moskito, Champ Champ, Bandit, Jeune Aventurier, Paysan, Champêtre, Homme Ours, Intrépide, Boune, Invisible, Larvesque, Bouftou Royal, Abraknyde, Kwak (4), Kwakwa, Scara (5), Scarabosse Doré, Akwadala, Aerdala, Terrdala, Blop (4), Gelax, Craqueleur, Mulou, Koalak, Kitsou, Wabbit, Cawotte, Tortue, Chef Crocodaille, Boostache, Anciens Chafers.
