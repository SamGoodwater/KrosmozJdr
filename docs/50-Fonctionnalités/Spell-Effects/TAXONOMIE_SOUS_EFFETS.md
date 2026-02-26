# Taxonomie des sous-effets — Pattern et rôle d’Effect

Ce document décrit le **pattern** des sous-effets et le rôle d’**Effect** pour interpréter l’action. On ne fixe pas une liste exhaustive en dur : on garde **action → caractéristique (élément = caractéristique) → valeur**, et c’est **Effect** qui définit les contraintes par action (durée, défense, conséquences partielles).

Références : [ARCHITECTURE_EFFETS_3_COUCHES.md](./ARCHITECTURE_EFFETS_3_COUCHES.md), [MODELE_EFFECT_SOUS_EFFECT.md](./MODELE_EFFECT_SOUS_EFFECT.md), [SYNTAXE_EFFETS.md](./SYNTAXE_EFFETS.md).

---

## 1. Pattern unique

- **Sous-effet** = **action** + **caractéristique** (optionnelle) + **valeur** (formule).
- **Élément = caractéristique** : pour “frapper”, la caractéristique est un élément (neutre, feu, eau, etc.) ; pour “booster”/“retirer”, c’est une stat ou ressource (PA, PM, agi, …). Le modèle ne distingue qu’un seul champ “caractéristique” ; **Effect** (ou la config par action) indique quelles caractéristiques sont acceptées (ex. pour “frapper” : uniquement type élément).
- **Valeur** : formule (ndX, [min-max], [level], [agi], floor(), etc.), voir [NOTATION_SOUS_EFFETS.md](./NOTATION_SOUS_EFFETS.md) et [SYNTAXE_EFFETS.md](./SYNTAXE_EFFETS.md).

On n’énumère pas ici toutes les actions ni toutes les valeurs de variables possibles ; le seeder ou la config fournit les actions de base (booster, retirer, soigner, frapper), et la liste des **caractéristiques** (stats + éléments) avec un **type/catégorie** pour filtrer par action.

---

## 2. Remplissage à la construction de l’effet

- Lors de l’attachement d’un sous-effet à un effet (admin Effets), on renseigne :
  - **Action** : choisie dans la liste des sous-effets (slug / type_slug).
  - **Caractéristique** : selon l’action, liste filtrée (ex. pour “frapper” → seulement caractéristiques de catégorie “element” ; pour “booster”/“retirer” → “stat” / “resource”).
  - **Valeur** : formule (texte).
- Stockage : `effect_sub_effect.params` = `{ characteristic, value_formula }`. Pour “soigner”, `characteristic` peut être vide ou absent.

---

## 3. Rôle d’Effect (couche 2)

**Effect** ne stocke pas seulement une liste de sous-effets ; il porte (ou portera) les règles d’interprétation :

- Pour l’action **frapper** : la caractéristique doit être un **élément** (Effect ou moteur de règles le vérifie / filtre).
- **Durée** : plusieurs tours en combat, ou en secondes hors combat.
- **Défense** : esquive PM, CA, jet de sauvegarde (ex. Intel), etc.
- **Conséquences partielles** : ex. moitié des dégâts si on se prémunit.

La liste exhaustive des types de défense ou des conséquences partielles n’a pas à être figée dans la taxonomie des sous-effets ; elle relève du modèle **Effect** et du moteur de résolution (voir [ARCHITECTURE_EFFETS_3_COUCHES.md](./ARCHITECTURE_EFFETS_3_COUCHES.md)).

---

## 4. Alignement avec le modèle actuel

- **sub_effects** : `slug`, `type_slug`, `template_text`, `param_schema`. Le `param_schema` décrit les paramètres (ex. characteristic avec `categories: ['element']` pour frapper, `categories: ['stat','resource']` pour booster/retirer).
- **effect_sub_effect** : `params` = `{ characteristic, value_formula }` (un seul champ “characteristic” pour tout ; élément = valeur possible de characteristic).
- **effect_sub_effects** (config) : une seule liste de **caractéristiques** avec un champ **category** (ex. `stat`, `resource`, `element`) pour filtrer selon l’action.

---

## 5. Résumé

- Un seul pattern : **action → caractéristique → valeur** ; pas de liste exhaustive figée.
- **Effect** interprète l’action (frapper ⇒ caractéristique = élément), durée, défense, conséquences partielles.
- **Spell/Item** (couche 3) : cible, zone, coût, limites d’usage.
