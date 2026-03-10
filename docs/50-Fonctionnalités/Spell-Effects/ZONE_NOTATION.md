# Notation des zones d’effet (damier)

Les zones d’impact des sorts, pièges et glyphes sont décrites par la propriété **`area`** sur le modèle **Effect**. La notation reprend le système **triple paramètre** de DofusDB : **forme (shape) + taille min (param1) + taille max (param2)**.

---

## 1. Système DofusDB (source)

L’API DofusDB expose `effects[].zoneDescr` avec :

- **shape** : identifiant de forme (voir tableau ci-dessous)
- **param1** : taille min / « où ça commence » (rayon intérieur, branche, etc.)
- **param2** : taille max / « où ça finit » (rayon extérieur, etc.)

### Shapes DofusDB (référence)

| shape | Forme | param1 | param2 | Remarque |
|-------|--------|--------|--------|----------|
| **80** | Case unique (CAC) | — | — | 1 case, pas de 2ᵉ paramètre |
| **67** | Cercle | Rayon intérieur (début) | Rayon extérieur (fin) | Plein, anneau ou bordure |
| **79** | Anneau sans centre | idem cercle | idem | Même notation que 67 (circle-min-max) |
| **76** | Ligne | Longueur | — | line-1xN |
| **88** | Croix pleine | Demi-longueur branche | — | cross-0-N (centre inclus) |
| **81** | Croix sans centre | Demi-longueur branche | — | cross-1-N (centre exclu) ; redondant avec double paramètre |
| **71** | Carré | Côté (ou largeur) | Hauteur (0 = carré) | rect-NxN ou rect-WxH |
| 0, 1, 2, 3, 4 | Anciens formats | — | — | Rétrocompat (0→point, 1→line, 2/4→cross, 3→circle) |

### Cercle (shape 67) — sémantique

- **67, 0, 2** : cercle **plein** de rayon 2 (de 0 à 2).
- **67, 1, 2** : **anneau** : de 1 à 2, la case du centre n’est pas comprise.
- **67, 2, 2** : **bordure seule** : uniquement le cercle extérieur, rien à l’intérieur.

On adopte cette logique dans KrosmozJDR.

---

## 2. Forme de la valeur Krosmoz

Une zone est notée : **`forme[-paramètres]`**.

- **forme** : `point`, `line`, `cross`, `circle`, `rect`, ou `shape-{id}` pour les formes non mappées.
- **paramètres** : selon la forme. Pour les cercles : **min-max** (rayon intérieur, rayon extérieur).

---

## 3. Formes supportées (notation Krosmoz)

| Forme | Notation | Exemple | Shape DofusDB |
|-------|----------|--------|----------------|
| **Point** | `point` | `point` | 80 (case unique) |
| **Ligne** | `line-1xL` | `line-1x9` | 76 |
| **Croix** | `cross-{min}-{max}` | `cross-0-2` (pleine), `cross-1-2` (sans centre) | 88, 81 |
| **Cercle** | `circle-{min}-{max}` | `circle-0-2`, `circle-1-2`, `circle-2-2` | 67, 79 (anneau) |
| **Rectangle / Carré** | `rect-WxH` | `rect-3x4`, `rect-2x2` | 71 |
| **Autre** | `shape-{id}[-p1-p2]` | `shape-99-1-2` | Forme non mappée |

### Exemples cercle (shape 67)

- `circle-0-2` — cercle plein de rayon 2 (67, 0, 2).
- `circle-1-2` — anneau : rayon 1 à 2, centre exclu (67, 1, 2).
- `circle-2-2` — uniquement la bordure extérieure (67, 2, 2).
- `circle-0-1` — cercle plein rayon 1 (équivalent 1 case si interprété strictement, ou shape 3 avec param1=1).

---

## 4. Où c’est utilisé

- **Effect.target_type** : `direct` (défaut), `trap` (piège), `glyph` (glyphe).
- **Effect.area** : zone d’impact de l’effet (sort direct, ou taille du piège/glyphe quand `target_type` = trap ou glyph).

La zone s’applique à l’effet (un bloc de sous-effets), pas au sort entier : un même sort peut avoir plusieurs effets (ex. par degré) avec des zones différentes.

**Déduplication** : `target_type` et `area` sont inclus dans `config_signature` pour éviter de fusionner des effets aux sous-effets identiques mais avec un mode d'application différent (ex. direct vs piège).

---

## 5. Correspondance DofusDB → Krosmoz

Mapping côté conversion (`zoneDescrToNotation`) :

| shape | param1 | param2 | Notation Krosmoz |
|-------|--------|--------|------------------|
| 0, 80 | — | — | `point` |
| 67, 79 | min | max | `circle-{min}-{max}` (67 cercle, 79 anneau sans centre) |
| 76, 1 | N | — | `line-1xN` |
| 88 | N | — | `cross-0-N` (croix pleine) |
| 81 | N | — | `cross-1-N` (croix sans centre) |
| 71 | W | H (0=carre) | `rect-WxH` |
| 2, 3, 4 | (ancien) | — | rétrocompat → cross / circle |
| autre | p1 | p2 | `shape-{id}-{p1}-{p2}` |

Les shapes non listés sont renvoyés en `shape-{id}` (et `-param1-param2` si présents) pour traçabilité.
