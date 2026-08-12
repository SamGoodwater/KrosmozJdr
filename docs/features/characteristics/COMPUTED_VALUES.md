# Valeurs calculées des caractéristiques

Une caractéristique d’entité (créature, plus tard équipement) peut être un **total explicite**
ou une **composition** de trois couches, pilotée par des formules qui dépendent du niveau.

## Les trois couches

`total = base + objets + contexte` (puis clamp min/max si défini).

| Couche | Source | Rôle |
| --- | --- | --- |
| **Base** | `characteristic_creature.formula` | Formule système (ex. CA = 10 + mod. Vitalité) |
| **Objets** | Somme de `items.bonus` des équipements portés | Agrégée par `CreatureItemBonusAggregator` |
| **Contexte** | Colonne `<db_column>_context` sur `creatures` | Nombre ou formule saisie pour ce monstre / PNJ |

### Priorité du total explicite

Si la colonne de total (`ca`, `ini`, `life`…) est **non nulle**, elle est affichée telle quelle
(`source = total_column`). C’est ce que le scrapping écrit aujourd’hui.

Si la colonne de total est **null**, le moteur compose les trois couches
(`source = composed`).

## Grammaire des formules saisies

Portée : bonus contextuel, niveau, et plus tard bonus d’équipement.

| Forme | Exemple | Effet |
| --- | --- | --- |
| Nombre | `12`, `-3` | Valeur fixe |
| Formule | `{[niveau] / 3}` | Expression, arrondi normal |
| Arrondi haut | `{[niveau] / 3}+` | `ceil` |
| Arrondi bas | `{([vitalite] - 10) / 2}-` | `floor` |
| Référence | `[level]`, `[vitalite]`, `[force]` | Alias FR acceptés |
| Fourchette | `{[5-8]}` | Domaine 5…8 (**niveau seulement**) |
| Dé | `{8 + [1d4]}` | Domaine 9…12 (**niveau seulement**) |

Fonctions autorisées : `floor`, `ceil`, `round`, `sqrt`, `abs`, `exp`, `log`,
`cos`, `sin`, `tan`, `asin`, `acos`, `atan`, `pow`, `min`, `max`.

Implémentation :

- PHP : `app/Services/Characteristic/Formula/FormulaExpressionParser.php`
- JS (aperçu UI) : `resources/js/Utils/characteristic/formulaGrammar.js`
- Domaine de niveau : `app/Services/Characteristic/Domain/LevelDomainResolver.php`

## Seul le niveau porte un domaine variable

Toute caractéristique peut **dépendre** d’autres caractéristiques, mais la seule valeur
modifiable à la main sous forme de fourchette ou de dé est le **niveau**.
Les autres caractéristiques se calculent ensuite niveau par niveau.

L’UI affiche d’abord la valeur du **premier niveau possible**, avec un sélecteur qui
recalcule toute la fiche sans nouvel aller-retour (payload `levels[]` de
`GET /entities/creatures/{id}/resolved-stats`).

## Décomposition UI

Sur les vues `full` / `line` / `minimal`, un **popover** (clic) montre :

- base, objets (détail par équipement), contexte, total ;
- la formule et sa résolution ;
- le tableau des valeurs par niveau.

Composants : `Popover.vue`, `CharacteristicDecompositionBody.vue`,
`CharacteristicsCard` + `CharacteristicGroup` (branchement de `levelEffective`).

## Affichage UI (densités)

`CharacteristicsCard` / `CharacteristicGroup` :

| Densité | Usage | Contenu |
| --- | --- | --- |
| `icon` | Minimal, line, pin | Icône + valeur |
| `labeled` | Modal full | Icône + label + valeur |
| `spacious` | Page full | Idem + aération |

Groupes canoniques : `creatureCharacteristicGroups.manifest.js`
(Combat → Caractéristiques/`abilityStack` → Résistances → Dommages → Contrôle).

Résumé Minimal (`mode: summary`) :

1. **Modificateurs** des 6 stats (mis en avant) ;
2. Combat : `pa`, `pm`, `life`, `ca`, `po`, `ini`, `invocation`.

Le groupe **Caractéristiques** empile par colonne : score → modificateur → sauvegarde
(`AbilityScoreStack.vue`). Les valeurs DB nulles sont résolues via le runtime
(`base + objets + contexte`) ; la CA utilise `10+[modifier_vitality_creature]`.

Résistances : fixe + code relatif entre parenthèses seulement si ≠ 0 %
(`V` Vulnérable / `F` Faiblesse / `R` Résistant / `I` Invulnérable).

Compétences (UI) : total = mod. caractéristique + `mastery_bonus × palier(0|1|2)`
+ bonus BDD + bonus objets (couche runtime). Affichage `+N`, `+N (M)`, `+N (E)`.

## Édition

- Champ dédié : `CharacteristicFormulaField.vue` (validation live + aperçu + aide « ? »).
- Pour chaque caractéristique composable : deux saisies distinctes —
  **total explicite** (colonne existante) et **bonus contextuel** (`*_context`).
- Le niveau accepte les domaines ; le contexte ne les accepte pas.

## Modèle de données

Migration `2026_08_07_220000_add_creature_context_bonus_columns` :

- ajoute `<colonne>_context` (**TEXT** nullable) pour chaque colonne composable ;
  (TEXT plutôt que VARCHAR pour rester sous la limite MySQL de taille de ligne) ;
- convertit aussi les totaux en TEXT et les rend **nullable** (null ≠ 0).

Liste des colonnes : `App\Support\Creature\CreatureComposableColumns`.

## Runtime

`CreatureRuntimeStatsService` :

1. résout le domaine de niveau ;
2. pour chaque niveau, sépare objets (`X_object`) et contexte ;
3. priorise le total explicite s’il existe ;
4. sinon compose base + objets + contexte ;
5. renvoie `levels[{ level, characteristics }]` + `items.lines` (rétrocompat `computed`).

## Reprise de l’existant

```bash
php artisan creatures:derive-context-bonuses --entity=monster --dry-run
php artisan creatures:derive-context-bonuses --entity=monster --clear-total --report=storage/logs/derive-context.md
```

Pour chaque caractéristique : `contextuel = total − base − objets`.
Sans `--clear-total`, le total reste prioritaire à l’affichage.

## Hors périmètre

- Buffs / effets temporaires de combat (4ᵉ couche).
- Modèle joueur dédié.
- Renommage physique des colonnes de total.
