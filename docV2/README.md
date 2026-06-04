# docV2 — Documentation code/site (humaine + IA)

> Reconstruction de la documentation technique du projet KrosmozJDR, organisée en **arbre par degrés**. Décrit **l'état actuel** uniquement (pas d'historique, pas de roadmap).
>
> `docV2/` est temporaire : il remplacera `docs/` une fois la migration terminée et vérifiée. L'ancien `docs/` reste la référence pendant la transition.

## Principe : un arbre, deux types de fichiers

La doc est un arbre de **nœuds** (domaine → sous-domaine → feuille). À chaque nœud :

- **`README.md` (humain)** : description complète et lisible du nœud. Présent **partout**.
- **`_ai.md` (IA condensé)** : résumé ultra-court + liens vers les enfants et le code. Présent **uniquement aux nœuds de regroupement** (degrés intermédiaires/supérieurs), pas sur les feuilles.

```
docV2/
  _ai.md            <- carte maître racine (degré 0)
  README.md         <- ce fichier
  <domaine>/
    _ai.md          <- carte du domaine (degré 1)
    README.md
    <sous-domaine>/
      _ai.md        <- si c'est encore un regroupement (degré 1a)
      README.md     <- feuille : README seul, pas de _ai.md
```

## Navigation pour un agent IA

1. La rule Cursor `.cursor/rules/ai-map.mdc` (toujours active) pointe vers `docV2/_ai.md`.
2. `docV2/_ai.md` liste les domaines en une ligne chacun → l'agent choisit le bon.
3. Il lit le `_ai.md` du domaine, puis descend de `_ai.md` en `_ai.md` jusqu'au nœud pertinent.
4. Seulement si besoin de détail : il ouvre le `README.md` humain, puis le code.

Objectif : l'agent ne charge que la branche concernée, pas tout le projet → moins de tokens.

## Gabarit `_ai.md` (nœud de regroupement)

```markdown
# <Nom du nœud> — carte IA

> Résumé en 1-2 phrases (état actuel).

## Quand lire ce nœud
- <déclencheurs : types de tâches concernées>

## Concepts clés
- <concept> : <1 ligne>. Code : `chemin`. Détail : `./README.md#ancre`

## Fichiers pivots
- `chemin/fichier` — rôle en une ligne

## Descendre
- [sous-domaine A](./a/_ai.md) — 1 ligne
- [README humain](./README.md) — détail complet
```

## Gabarit `README.md` (humain)

```markdown
# <Nom du nœud>

Description complète, état actuel uniquement. Schémas si utile, 1-2 exemples max.
Pas de changelog, pas de « TODO », pas d'historique de refactoring.
```

## Règles de rédaction (rappel)

- **Pas d'historique** : on décrit ce qui existe, pas comment on y est arrivé (voir `docs/DOCUMENTATION_GUIDE.md`).
- **Un concept = un fichier**, liens croisés, exemples courts.
- **Chemins exacts** vers le code (vérifiés), pas d'approximation.
- **Contenu de jeu** (`docs/400- Jeu`) : hors périmètre de `docV2`, juste référencé via `game-content/_ai.md`.

## État de la migration (POC)

| Domaine | `_ai.md` | `README.md` | Statut |
| --- | --- | --- | --- |
| `features/entities` | oui | oui | **migré (modèle)** |
| `features/scrapping` | oui | oui | **migré (modèle)** |
| `features/cms` | oui | oui | **migré** |
| `features/permissions` | oui | oui | **migré** |
| autres domaines | stub | — | à migrer (itérations suivantes) |

Les `_ai.md` « stub » contiennent déjà les pointeurs vers la doc `docs/` existante, à remplacer progressivement par la doc remixée dans `docV2`.
