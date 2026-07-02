# Syntaxe des effets — Variables, dés et formules

Ce document décrit la **syntaxe autorisée** pour les champs texte des effets (template, description) et pour le champ **formula** des sous-effets. Tout texte stocké doit passer par `App\Services\Effect\EffectTextSanitizer` avant enregistrement.

Références : [EFFETS_TEMPLATES_ET_SURETE.md](./EFFETS_TEMPLATES_ET_SURETE.md), [MODELE_EFFECT_SOUS_EFFECT.md](./MODELE_EFFECT_SOUS_EFFECT.md), [SYNTAXE_FORMULES_CARACTERISTIQUES.md](../../10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md), [FORMULAS_PRACTICES.md](../../10-BestPractices/FORMULAS_PRACTICES.md).

---

## 1. Variables dans le template / description

Les placeholders **`[nom]`** sont remplacés à l’affichage ou au calcul par une valeur issue du contexte (niveau, caractéristique, cible, etc.).

| Variable (exemples) | Usage typique |
|--------------------|----------------|
| `[value]` | Valeur min/max ou fixe de l’effet (ex. dégâts 10–20). |
| `[level]` | Niveau du sort, de l’objet ou du personnage. |
| `[agi]`, `[strong]`, `[intel]`, `[cha]`, `[wis]`, `[vita]` | Caractéristiques (agilité, force, intelligence, chance, sagesse, vitalité). |
| `[duration]` | Durée (tours, secondes). |
| `[element]` | Élément (Terre, Feu, etc.). |
| `[mod_agility]`, `[mod_strength]`, … | Modificateur de caractéristique (ex. mod = floor((carac−10)/2)). |

- **Format** : `[nom]` avec `nom` en lettres/chiffres/underscore (ex. `[value]`, `[level_creature]`).
- **Sûreté** : le sanitizer conserve les crochets et le contenu des variables ; seuls HTML/JS et caractères dangereux sont retirés.
- **Résolution** : effectuée côté affichage ou par le service de formules lorsqu’une formule référence ces variables.

---

## 2. Notation dés (ndX)

La notation **ndX** désigne **n dés à X faces** (ex. `1d6`, `2d10`, `1d20`).

| Exemple | Signification |
|--------|----------------|
| `1d6` | 1 dé à 6 faces. |
| `2d10` | 2 dés à 10 faces (somme des jets). |
| `1d20` | 1 dé à 20 faces (typique pour jets de compétence). |

- **Stockage** : la chaîne est conservée telle quelle dans le template (ex. « Jet 2d6 + [agi] dégâts »).
- **Affichage** : peut rester en « 2d6 » ou être formatée « 2 dés à 6 faces » selon l’UI.
- **Évaluation** : pour le calcul, le moteur de formules (réutilisant celui des caractéristiques) interprète `ndX` comme une somme de n jets à X faces.

---

## 3. Formules dans les sous-effets (champ formula)

Le champ **formula** d’un sous-effet contient une **expression évaluable**, alignée sur la syntaxe des caractéristiques pour réutiliser `CharacteristicFormulaService` / `FormulaResolutionService`.

| Élément | Syntaxe | Exemple |
|--------|---------|---------|
| Variable | `[id]` | `[level]`, `[agi]`, `[value]` |
| Dés | `ndX` | `1d6`, `2d10` |
| Opérateurs | `+`, `-`, `*`, `/`, `(`, `)` | `[level] * 2 + [agi]` |
| Arrondis | `floor(...)`, `ceil(...)` | `floor(([strength]-10)/2)` |
| Modificateurs | `[mod_<carac>]` | `[mod_agility]`, `[mod_strength]` |

- **Contexte d’évaluation** : le service injecte les valeurs disponibles (niveau, caractéristiques, value, etc.) et retourne un nombre.
- **Validation** : à l’enregistrement, le champ formula doit être sanitized (EffectTextSanitizer) et peut être validé par le même moteur que les caractéristiques (voir [SYNTAXE_FORMULES_CARACTERISTIQUES.md](../../10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md)).
- **Table par caractéristique (JSON)** : si le moteur le supporte, une formule de type tableau par seuil (comme pour les caractéristiques) peut être stockée ; le décodage reste géré par `FormulaConfigDecoder` côté caractéristiques.

---

## 4. Sanitization (rappel)

- **Service** : `App\Services\Effect\EffectTextSanitizer::sanitize(string $text): string`.
- **Règle** : tout champ « template », « description » ou « formula » d’effet/sous-effet doit être passé par ce service avant sauvegarde (import ou formulaire).
- **Autorisé** : lettres, chiffres, ponctuation courante, espaces, retours à la ligne, `[nom_var]`, `ndX`.
- **Supprimé** : balises HTML, scripts, event handlers (onerror, onclick, …), protocoles `javascript:`, `data:`, `vbscript:`, chevrons `<` `>`.

---

## 5. Exemples

| Contexte | Exemple |
|----------|---------|
| Template | `Inflige [value] dégâts [element].` |
| Avec dés | `Jet 2d6 + [agi] pour les dégâts.` |
| Formula | `[level] * 2 + floor([agi] / 2)` |
| Formula avec dés | `1d20 + [mod_agility]` |

Ces exemples restent valides après sanitization ; les variables et la notation ndX sont préservées.
