# Syntaxe des formules (Krosmoz JDR)

- Toute formule est encadrée par des accolades `{ ... }`.
- Variables entre crochets `[variable]`.
- Opérateurs : +, -, *, /, %, parenthèses pour l’ordre.
- Listes : `[a-b]`, `[XdY]`.
- Fonctions : min, max, floor, ceil, random.
- Conditions : `condition ? valeur1 : valeur2`.
- Min/Max en fin de formule : `(min: ...)`, `(max: ...)`.
- Une seule liste par formule.

## Exemples
- `{ [level] + 2 }`
- `{ random(1, 10) }`
- `{ [level] > 5 ? 10 : 5 }`
- `{ ([level] > 5 ? [level] : 5) * random(1, 6) }(max: 20)`

**Résumé** :
- Toujours `{ ... }` pour une formule.
- Une seule liste par formule.
- Variables `[variable]`, fonctions `nom_fonction(...)`, conditions ternaires, min/max à la fin. 