# Dés / formules — IA

> Parser sécurisé de formules tapées (dés, tranches, opérateurs). Pas d’`eval`.

## Quand lire

- Modifier min/max/moyenne, conversion `[min-max]` → dés, lancer simulé.
- Brancher plus tard des stats de créatures sur le même service.

## Règle de conversion

`[min, max]` inclusif → `1d(max-min+1)+(min-1)`. Ex. `[2-6]` = `1d5+1`.

## Fichiers

- PHP : `app/Services/Jdr/DiceFormulaService.php`, `DiceNotationService.php`
- JS : `resources/js/Utils/dice/diceParser.js`
- UI : `DiceFormulaStrip.vue`, `SearchInput.vue`, `DiceRollerModal.vue`
