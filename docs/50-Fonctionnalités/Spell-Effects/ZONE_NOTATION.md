# Notation des zones d’effet (damier)

Les zones d’impact des sorts, pièges et glyphes sont décrites par la propriété **`area`** sur le modèle **Effect**. La notation est une chaîne lisible et sans ambiguïté pour un damier (grille hexagonale ou carrée selon le jeu).

---

## Forme de la valeur

Une zone est notée : **`forme[-paramètres]`**.

- **forme** : `point`, `line`, `cross`, `circle`, `rect`.
- **paramètres** : selon la forme (séparés par `x` pour les dimensions).

---

## Formes supportées

| Forme      | Notation       | Exemple    | Description |
|-----------|----------------|------------|-------------|
| **Point** | `point`        | `point`    | 1 case (cible unique). |
| **Ligne** | `line-WxL`     | `line-1x9` | Ligne de largeur W et longueur L (ex. ligne de 9 cases). |
| **Croix** | `cross-N`      | `cross-2`  | Croix, N = demi-longueur des branches (ex. cross-2 = 5 cases). |
| **Cercle**| `circle-N`     | `circle-2` | Cercle de rayon N (N cases du centre). |
| **Rectangle** | `rect-WxH`  | `rect-3x4` | Rectangle largeur W × hauteur H. |

### Exemples

- `point` — une case.
- `line-1x9` — ligne 1×9 (ligne de 9 cases).
- `cross-2` — croix avec branches de 2 cases (5 cases au total).
- `circle-3` — cercle rayon 3.
- `rect-3x4` — rectangle 3×4.

---

## Où c’est utilisé

- **Effect.target_type** : `direct` (défaut), `trap` (piège), `glyph` (glyphe).
- **Effect.area** : zone d’impact de l’effet (sort direct, ou taille du piège/glyphe quand `target_type` = trap ou glyph).

La zone s’applique à l’effet (un bloc de sous-effets), pas au sort entier : un même sort peut avoir plusieurs effets (ex. par degré) avec des zones différentes.

---

## Correspondance DofusDB

L’API DofusDB expose `effects[].zoneDescr` avec `shape` (entier) et éventuellement `param1`, `param2`. Un mapping vers cette notation peut être fait côté conversion (ex. shape 0 → `point`, shape 1 + param → `line-1xN`, etc.) selon la doc Dofus des shape IDs.
