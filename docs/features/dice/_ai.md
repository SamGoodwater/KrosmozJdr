# Dés / formules — IA

> Parser sécurisé de formules tapées (dés, tranches, opérateurs, nombres). Pas d’`eval`.

## Quand lire

- Modifier min/max/moyenne, conversion `[min-max]` → dés, lancer simulé.
- Calculateur de dés de vie (PNJ / monstre) : relance des 1, méthode stable, formules monstre.

## Règle de conversion

`[min, max]` inclusif → `1d(max-min+1)+(min-1)`. Ex. `[2-6]` = `1d5+1`.

## Dés de vie

- Classes : `breeds.life_dice` (`1d6`…`1d12`). Monstres : `monsters.life_formula` (gain d’un niveau).
- Créature : `creatures.life_dice_total` = part des dés (niv. 1 → N). PV = part + max(0, mod. Vit) × niv. + équip.
- UI : `LifeDiceCalculatorModal.vue` depuis l’éditeur de caractéristiques (édition PNJ / monstre).
- PHP : `LifeDiceCalculator.php` ; JS : `lifeDiceCalculator.js`. Options de lancer : `reroll_ones` / `fixed_by_sides`.

## Fichiers

- PHP : `app/Services/Jdr/DiceFormulaService.php`, `DiceNotationService.php`, `LifeDiceCalculator.php`
- JS : `resources/js/Utils/dice/diceParser.js`, `lifeDiceCalculator.js`
- UI : `DiceFormulaStrip.vue`, `SearchInput.vue`, `DiceRollerModal.vue`, `LifeDiceCalculatorModal.vue`
- Conversion moyenne → dés : `diceEquivalents.js` (`suggestDice`, critique = dés ×2) ; UI barèmes : `SpellReferenceScales.vue`
- Reconnu : dé, tranche **ou** opérateur (`50-17`). Un nombre seul (`12`) n’est pas une formule de recherche.
- Historique : props `history` `{ formula, value }[]`, pas de localStorage / cookie.
