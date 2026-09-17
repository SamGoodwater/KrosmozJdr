# Architecture des effets en trois couches

Ce document décrit la répartition des responsabilités entre **sous-effet**, **Effect** et **Spell/Item** : un pattern générique (action → caractéristique → valeur), sans figer en dur toutes les possibilités ; c’est **Effect** qui interprète l’action et définit durée, défense et conséquences partielles ; **Spell/Item** gère cibles, zone, coût et limites d’usage.

Références : [MODELE_EFFECT_SOUS_EFFECT.md](./MODELE_EFFECT_SOUS_EFFECT.md), [NOTATION_SOUS_EFFETS.md](./NOTATION_SOUS_EFFETS.md).

---

## 1. Pattern générique : action → caractéristique → valeur

- Les données envoyées à **Effect** suivent un seul pattern : **action** → **caractéristique** → **valeur**.
- **Élément = caractéristique** : on ne distingue pas “élément” et “caractéristique” dans le modèle ; l’élément (neutre, feu, eau, etc.) est une caractéristique. C’est **Effect** qui décide que pour l’action “frapper”, la caractéristique doit être un élément.
- On n’écrit pas en dur toutes les possibilités de sous-effets ni toutes les valeurs de variables : on garde ce pattern et on laisse **Effect** (et éventuellement la config) dire quelles caractéristiques sont valides pour quelle action (ex. pour “frapper” : uniquement caractéristiques de type élément).

---

## 2. Couche 1 — Sous-effet (données envoyées à Effect)

- **Rôle** : décrire *quoi* se passe, de façon machine (action + caractéristique + valeur).
- **Structure** : une ligne = une **action** (booster, retirer, soigner, frapper, etc.) + une **caractéristique** (optionnelle selon l’action : PA, PM, agi, neutre, feu…) + une **valeur** (formule : ndX, [1-4], [level], etc.).
- **Stockage** : `effect_sub_effect` avec `params` = `{ characteristic, value_formula }`. Pour “soigner”, `characteristic` peut être vide ; pour “frapper”, `characteristic` contient l’élément (stocké comme une caractéristique).
- La liste des actions et des caractéristiques possibles peut venir d’un seeder ou d’une config ; **Effect** (couche 2) interprète et contraint (ex. pour “frapper”, caractéristique ∈ éléments).

---

## 3. Couche 2 — Effect (règles, durée, défense, conséquences)

- **Rôle** : interpréter l’action et définir *comment* l’effet s’applique (durée, défense, conséquences partielles).
- **Interprétation de l’action** :
  - Ex. si l’action est “frapper”, **Effect** considère que la caractéristique doit être un **élément** (intel, agi, chance, neutre, force, ou la liste éléments du jeu). La validation ou la résolution peut rejeter / filtrer si ce n’est pas le cas.
- **Durée** :
  - Effect peut indiquer si le sous-effet **dure sur plusieurs tours** en combat.
  - Hors combat, la durée peut être exprimée en **secondes** (ou autre unité).
  - Champs envisagés sur Effect (ou sur effect_sub_effect) : type de durée (tours / secondes), valeur, etc.
- **Défense** (comment on se prémunit) :
  - Effect définit **comment on peut se prémunir** de l’attaque : esquive PM, classe d’armure (CA), jet de sauvegarde (ex. Intel), etc.
- **Conséquences partielles** :
  - Si on se prémunit (réussi), **y a-t-il quand même des conséquences ?** Ex. : on prend la moitié des dégâts, ou un effet réduit.
  - Effect porte la règle (ex. “réduction 50 % si sauvegarde réussie”).

*Implémentation* : ces règles peuvent être des champs sur la table `effects` (ou `effect_sub_effect`) selon le besoin, par exemple :
- **Durée** : `duration_type` (tours | secondes), `duration_value` (nombre). En combat = tours ; hors combat = secondes.
- **Défense** : `defense_type` (esquive_pm | classe_armure | sauvegarde_intel | sauvegarde_agi | …) — comment on se prémunit.
- **Conséquences partielles** : `partial_on_save` (ex. half_damage, no_effect) — si on se prémunit, appliquer quand même un effet réduit ou non.

À ajouter en base et au modèle Effect lorsque le moteur de combat / résolution sera implémenté.

---

## 4. Couche 3 — Spell / Item (cibles, zone, coût, limites)

- **Rôle** : définir **qui** peut être ciblé, **où** (zone ou pas), **combien ça coûte** et **combien de fois** on peut l’utiliser.
- **Portée par le sort ou l’item** (pas par Effect) :
  - **Cible** : qui peut être ciblé (allié, ennemi, soi, case vide, etc.).
  - **Zone** : effet de zone ou pas, forme (carré, ligne, cône…).
  - **Coût** : PA, PM, PO, etc.
  - **Limites d’usage** : nombre d’utilisations par cible, par tour, ou tout les X tours (cooldown).
- **Lien** : Spell/Item → `effect_usage` → Effect. L’entité (sort, item) référence l’effet ; les infos de cible, zone, coût et limites restent sur l’entité (ou tables dédiées liées au sort/item).

---

## 5. Résumé

| Couche        | Contenu principal                                      | Exemple |
|---------------|---------------------------------------------------------|---------|
| **Sous-effet**| action + caractéristique + valeur (formule)            | frapper, feu, 2d6+[level] |
| **Effect**    | interprétation (frapper ⇒ élément), durée, défense, partiel | durée 2 tours, défense = jet Intel, 50 % si réussite |
| **Spell/Item**| cible, zone, coût PA, utilisations / tour / cooldown    | cible ennemi, zone 2 cases, 3 PA, 1/tour |

On évite d’écrire en dur toutes les possibilités ; on garde le pattern **action → caractéristique → valeur** et on laisse **Effect** dire les contraintes (caractéristique = élément pour frapper, etc.) et les règles de durée, défense et conséquences partielles.
