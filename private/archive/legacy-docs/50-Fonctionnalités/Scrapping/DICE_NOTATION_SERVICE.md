# Service de notation de dés (DiceNotationService)

**Objectif** : Service réutilisable qui convertit des valeurs numériques (une valeur ou une plage min–max) en notation JDR **ndX** ou **ndX+y**, pour affichage ou stockage (barèmes, conversion Dofus → Krosmoz, fiches, etc.).

**Emplacement** : `App\Services\Jdr\DiceNotationService`.

---

## 1. Cas d’usage

- **Conversion Dofus → Krosmoz** : Dofus renvoie des dégâts (ex. 100–120). On les réduit d’abord à l’échelle Krosmoz (ex. 10–12), puis on les convertit en notation dés (ex. 2d4+4 ou 3d4).
- **Barèmes par niveau** : à partir de min/max cibles, générer une formule ndX ou ndX+y pour fiches ou règles.
- **Affichage** : afficher une valeur brute sous forme de dés (ex. 11 → « 2d4+4 »).

Les valeurs passées au service sont considérées comme **déjà dans l’échelle cible** (Krosmoz ou autre). La réduction d’échelle (100–120 → 10–12) est faite en amont (ex. formules characteristic_spell).

---

## 2. Règles de choix de la forme

On calcule l’**écart relatif** : `(max - min) / max` (si une seule valeur, écart = 0).

| Écart | Forme privilégiée | Exemple |
|-------|-------------------|--------|
| **&lt; 5 %** ou valeur unique | **ndX + y** pour coller au plus près de la valeur | 10–12 → 2d4+4 |
| **&gt; 30 %** | **ndX** seul, **petit n**, **grand X** (aléatoire) | 5–20 → 2d12 |
| **Entre 5 % et 30 %** | **ndX** seul, **grand n**, **petit X** (courbe en cloche) | 4–12 → 3d4 |

- **Dés utilisés** : d4, d6, d8, d10, d12, d20.
- **n** : nombre de dés (typiquement 1 à 8 pour ndX+y, 1 à 6 pour ndX).

---

## 3. API

```php
$service = app(\App\Services\Jdr\DiceNotationService::class);

// Valeur unique (équivalent min = max = 11)
$service->toDiceNotation(11);           // ex. "2d4+4" ou "3d4+1"

// Plage min – max
$service->toDiceNotation(10, 12);        // ex. "2d4+4" (écart < 5 %)
$service->toDiceNotation(5, 20);        // ex. "2d12" (écart > 30 %)
$service->toDiceNotation(4, 12);        // ex. "3d4" (écart 5–30 %)
```

**Signature** : `toDiceNotation(float $min, ?float $max = null): string`

- `$min` : valeur minimale (ou valeur unique si `$max` est null).
- `$max` : valeur maximale ; si null, une seule valeur cible.
- **Retour** : chaîne `"ndX"` ou `"ndX+y"` (ex. `"2d6"`, `"2d4+4"`).

---

## 4. Intégration via conversion_function convertToDice

Le service est **externe** à la conversion : il ne dépend pas du pipeline Dofus/Krosmoz. Pour l’utiliser dans la conversion des effets de sort :

1. **Réduire** d’abord la valeur Dofus à l’échelle Krosmoz (ex. formule `power_spell` → valeur entière 5–25).
2. Si l’effet a une **plage** (min–max) en entrée, réduire min et max séparément.
3. Appeler `DiceNotationService::toDiceNotation($minKrosmoz, $maxKrosmoz)`.
4. Stocker le résultat (ex. dans `params.dice_formula` ou en complément de `value_converted`) pour affichage JDR.

Le service est intégré via la fonction **convertToDice** du registry : si une caractéristique (ex. `power_spell`) a `conversion_function = 'convertToDice'`, `SpellEffectsConversionService` appelle `DiceNotationService::toDiceNotation()` sur la valeur convertie et stocke le résultat dans **`params.dice_formula`**. Par défaut, `power_spell` est livré avec `conversion_function = 'convertToDice'` dans le seeder. Flux : formule BDD → valeur convertie → si convertToDice → `params.dice_formula = toDiceNotation(value_converted)`.

---

## 5. Références

- [FORMULES_CONVERSION_SORTS_REGLES.md](./FORMULES_CONVERSION_SORTS_REGLES.md) — Réduction d’échelle Dofus → Krosmoz (power_spell, etc.).
- [PLAN_IMPLEMENTATION_PHASE3_CONVERSION_VALEURS_EFFETS.md](./PLAN_IMPLEMENTATION_PHASE3_CONVERSION_VALEURS_EFFETS.md) — Contexte conversion des effets.
- Règles 5.3.1 (barèmes dégâts/soins), 5.2.3 (sorts et aptitudes).
