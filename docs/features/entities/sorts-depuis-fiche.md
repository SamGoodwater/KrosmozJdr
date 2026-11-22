# Construire un sort depuis sa fiche

Deux familles. On ne mélange pas les budgets ni les catégories.

| Famille | Qui | Budget | Source Dofus |
| --- | --- | --- | --- |
| **Classe** | PJ / PNJ | ≈ 30–32 sorts, voies, passif | sorts de la classe |
| **Créature** | monstre / invocation | 1 à 4 selon la puissance | sorts de la créature |

Une fois écrite, la fiche (Obsidian ou atelier) fait foi. Le catalogue JSON puis l’import recopient : une case vide n’est pas devinée.

Guide MJ / IA (même texte) : `resources/ia/creation-guides/spell.php` (atelier Création → Sorts).

---

## Partie A — Sort de classe

On choisit d’abord le sort Dofus de départ, on écrit la fiche, puis on la recopie dans le catalogue. Un sort se construit **un par un**.

### Pourquoi une passe globale rate des colonnes

Beaucoup de détail (classe, paliers, colonnes). L’importeur ne reprend pas la ligne précédente : une clé absente d’un palier retombe sur le **sort de base**.

### Choisir le sort Dofus de départ

Chaque sort de Krosmoz part d’un sort de la même classe dans Dofus (ex. [DofusDB Iop](https://dofusdb.fr/fr/database/breeds/8)).

| Famille | Nombre | Places | Source Dofus |
| --- | --- | --- | --- |
| Élémentaire | 16 | 4 par voie (Feu, Terre, Air, Eau), aux niveaux 1, 4, 7 et 12 | sort de cet élément **qui fait des dégâts** |
| Général | le reste | 8 choix de 1–2 variantes | sort **sans élément strict et sans dégâts** |

Exceptions volontaires (sort moteur seul : Bombe, Arbre, Masque, Portail, Mot d’Amitié…) : écrire dans Adaptations. Le total peut alors être 30 ou 31.

#### 1. Trier les sorts Dofus de la classe

- **dégâts d’un élément** → tas élémentaire ;
- **pas de dégâts, pas d’élément strict** → tas général.

C’est l’élément et les dégâts qui décident, pas l’orientation.

#### 2. Les sorts élémentaires

Pour chaque voie, 4 sorts aux niveaux 1, 4, 7 et 12, du plus simple au plus fort.

- Au moins **deux** collent à l’orientation de la voie.
- **Deux sorts d’une même voie ne font pas exactement la même chose.**
- Niveau 1 : frappe **basique** ; effets forts en montée ou sur 4 / 7 / 12.
- Chaque sort élémentaire fait des dégâts de l’élément de sa voie.

#### 3. Les sorts généraux

Paires sur les choix généraux. Les deux variantes n’ont pas la même orientation, sauf exception écrite. Répartir les orientations pour que chaque voie trouve de l’aide.

#### 4. Vérifier le kit avant d’écrire les tableaux

- Un sort par place de la répartition.
- 16 élémentaires avec dégâts de voie.
- Généraux sans dégâts (sauf exception écrite).
- `dofusdb_id` quand l’homonyme existe ; sinon `official_id` `jdr:{classe}:…`.

### Le texte Général (classe et créature)

- *Général* (`description`) : geste, 1–2 phrases, tutoiement, inclusif, image concrète, élément + nature d’effet, **sans valeurs**, ≤ 255 caractères.
- *Effets* (`effect`) : dés, portées, durées, relances — ce qui sert à jouer le tour.

### Une ligne de fiche

1. Case vide sur la **première** ligne = défaut (PA 3, contact, 1 case, LdV, 2/tour, relance 0).
2. Case vide plus loin = ligne précédente.
3. Si `/cible` n’est jamais écrit, il vaut le `/tour` de la ligne.
4. PO `1-4` = modifiable ; `0` = non ; `1` = 1–1.
5. Le dé n’inclut pas le modificateur d’élément. Un cran par ligne.
6. Pas de critique écrit sur la fiche.

### Chaque colonne → champ

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
| « / N tours » ou « pendant N tours » | `duration` + `duration_formula` |
| « −N PA / PM / PO » | sous-effet `retirer` |
| « repousse / saut / attire de N » | `cells_formula` du déplacement |
| Jet d’attaque vs CA | `resolution_mode` = `attack_roll` |
| DD vs sauvegarde | `resolution_mode` = `saving_throw` |
| automatique | `resolution_mode` = `auto_success` |
| « Allié·e·s saufs » | `spares_chosen_targets` |
| Physique / Wakfu | `is_magic` |
| « Si Agi ≥ 3 » / règles de valeur | `params.conditions`, `params.value_rules` |
| « jusqu’à N » | `params.value_up_to` |
| Invocation | `monster_official_id` `jdr:summon:…` |

Sur chaque palier dont une valeur **diffère de la base**, répéter la clé. Ne pas compter sur le palier d’avant.

### Contrôle classe

```bash
python3 scripts/audit-class-spell-sheets.py
```

Puis `scripts/build-{classe}-spells-from-sheet.py` si le kit change, import `ClassLevel1SpellSeederImporter::importBreed`, vérifier chaque onglet `Niv` sur la fiche.

---

## Partie B — Sort de créature (monstre)

Objectif : une rencontre jouable en 10 secondes par action, pas un grimoire MMO.

### Principe

1. Lister les sorts Dofus de la créature (nombre + types).
2. Garder ceux qui collent aux orientations utiles : **dégâts càc**, **dégâts distance**, entrave, amélioration, protection, soin, placement, invocation.
3. **Simplifier** (1 effet principal + 0–2 secondaires). Couper jusqu’au plafond de puissance.
4. Catégorie **créature** (`category` = 1). Élément aligné sur la carac haute de la fiche.

### Kit selon la puissance (repères souples)

Peu puissant = sbire · normal = standard · puissant = élite. Pas de critère chiffré obligatoire. Boss ≈ 4 = plafond souple.

| Puissance | Repère | Composition typique |
| --- | --- | --- |
| **Peu puissant** / sbire | 1–2 | 1 dégât ± 1 autre (placement / entrave) |
| **Normal** / standard | 2 | 1 dégât + 1 autre |
| **Puissant** / élite | ~3 | 1–2 attaques + 1–2 autres |
| **Boss** | plafond ≈ 4 | distance + càc + (amélioration **ou** entrave) + placement |

**Invocation** (sur un monstre) : rare, seulement si elle compte pour le gameplay. Moins d’entités sur le terrain = plus simple pour le MJ.

Invocations de classe (`jdr:summon:…`) : autre modèle — souvent **1** action fragile, pas ce tableau.

**Traits** de créature : souvent 0–2 du catalogue ; possibles de temps en temps s’ils collent.

Bestiaire Incarnam / Amakna : en **brouillon** tant que non validé.

### Écriture

Même gabarit de tableau que la classe (PA, PO, zone, dés…), mais :

- peu ou pas d’intensification longue (le niveau de la créature porte la puissance) ;
- budget PA du monstre respecté (pas tout lancer chaque tour) ;
- coûts PA/PM proches de Dofus pour le rythme ;
- dégâts calés sur `dommages_spell` puis ajustés (faible −20–30 %, puissant +20–30 %, boss +50–100 % **ou** phases).

Fiche type Obsidian : `Idées/Liste contenu/Monstres/1- Fiche type.md`. Gabarit stats : règles 5.1.2. Paquet IA : monstre + sorts dans le même JSON (`docs/IA/RENCONTRES.md`).

### Contrôle créature

- Élément du sort = carac haute.
- Nombre de sorts ≤ plafond de puissance.
- Chaque sort a *Général* + *Effets* + résolution.
- Lien `creature_spell` ; sorts en `playable` avec le monstre.
