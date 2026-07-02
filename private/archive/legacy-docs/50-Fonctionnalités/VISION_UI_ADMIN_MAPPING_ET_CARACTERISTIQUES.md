# Vision : UI admin Mapping DofusDB → KrosmozJDR et caractéristiques

Ce document fixe l’**objectif global** et la **spécification de l’interface admin** pour gérer le flux **Scrapping (récupération) → Mapping → Conversion / validation** entièrement depuis la base de données et l’UI.

---

## 1. Objectif global

- **Récupération** : Données provenant d’une API externe (DofusDB), dont l’architecture diffère de KrosmozJDR.
- **Mapping** : Une fois les données récupérées, les **mapper** vers le modèle KrosmozJDR. Aujourd’hui ce mapping est décrit en JSON ; l’objectif est de le gérer via **UI + base de données**.
- **Conversion** : Chaque propriété mappée doit être **convertie** pour être compatible avec KrosmozJDR (formules, limites, types).
- **Deux gros services** :
  1. **Scrapping** : récupère les données (DofusDB).
  2. **Mapper** puis **Characteristics** : convertit les données, les compare et les valide par rapport aux limites.

L’intérêt de mettre les données **variables en base** est de **tout gérer côté admin** (sans éditer de JSON).

---

## 2. Données par propriété (caractéristique)

Pour chaque propriété (caractéristique), on peut avoir :

- **Nom**
- **Icône**
- **Description**
- **Couleur**
- **Format** : int, string, array, etc.
- **Unité** (facultatif)
- **Ordre d’affichage** (facultatif)

---

## 3. Expression dynamique

On utilise un système de **formule** appelé **expression dynamique**, capable de :

- **Variables dans l’entité** : `[level]`, `[life]`, etc.
- **Valeurs aléatoires** : `[1-6]`, dés `1d6`, `3d8`.
- **Fonctions de base** (ex. PHP) et **opérateurs classiques** pour les calculs.

La **valeur par défaut** et les **limites** peuvent être des formules. Une formule peut **varier selon une autre variable** (conditions), par exemple :

- `[level] < 3` → formule 1  
- `[level] < 5` → formule 2  
- `[level] >= 6` → formule 3  

**Résumé** : Une expression dynamique est une entité qui peut représenter soit une **valeur constante**, soit une **formule calculée à l’exécution**. Elle peut référencer d’autres variables, utiliser opérateurs, fonctions autorisées et générateurs aléatoires (ex. `1d6`, `3d8`, `[1-6]`). Elle peut aussi être **conditionnelle** et retourner différentes formules selon le contexte.

---

## 4. Structure de l’UI admin (par caractéristique)

Les panneaux peuvent **varier selon le groupe d’entité** (creature, object, spell, etc.) voire **d’une entité à l’autre** dans un même groupe.

### Panneau 1 — Limite et valeur

- **Défaut**, **limite basse** et **limite haute** : les trois sont des **expressions dynamiques**.
- Affichage : **formule** en chaîne (string).

### Panneau 2 — Conversion

- **Formule de conversion** (DofusDB → Krosmoz) : expression dynamique, affichée en string.
- **Graphe** : en dessous de la formule, axes basés sur les limites du panneau 1 (abscisses / ordonnées). Un petit **menu d’options** permet de modifier les bornes **uniquement pour l’affichage** (aucun enregistrement).

**Sous-panneau Échantillonnage**

- **Tableau d’entrée** avec :
  - Colonnes **DofusDB** : niveau ⇒ valeur.
  - Colonnes **Krosmoz** : niveau ⇒ valeur.
- **Niveaux par défaut** :  
  - DofusDB : 1, 40, 80, 120, 160, 200.  
  - KrosmozJDR : 1, 4, 8, 12, 16, 20.  
- Objectif : proposer **automatiquement** des types de formules (linéaire, carré, carré décalé, exponentielle, logarithmique, polynôme 2, etc.).

### Panneau 3 — Mapping (nouveau, remplace les JSON)

- Contient **toutes les infos** nécessaires pour faire le **lien** entre :
  - la **propriété côté API DofusDB**,
  - et la **propriété côté KrosmozJDR**.
- **Important** : cette liaison est **unique par entité**, même au sein d’un même groupe (ex. une même caractéristique logique peut avoir un mapping différent pour monster, spell, item, etc.).

---

## 5. Synthèse

| Élément | Contenu |
|--------|---------|
| **Flux** | DofusDB (API) → Scrapping → Mapping (DB/UI) → Conversion (Characteristics) → Données KrosmozJDR. |
| **Caractéristique** | Nom, icône, description, couleur, format, unité, ordre. |
| **Expression dynamique** | Constante ou formule ; variables `[level]`, dés, conditions. |
| **Panneau 1** | Défaut + limites (expressions dynamiques). |
| **Panneau 2** | Formule de conversion + graphe (option d’affichage) + échantillonnage (suggestion de formules). |
| **Panneau 3** | Mapping DofusDB ↔ KrosmozJDR **par entité** (remplace les JSON). |

Ce document sert de référence pour la conception de l’UI admin et des modèles/tables associés (voir aussi [INVENTAIRE_JSON_ET_MIGRATION_BDD_UI.md](./Scrapping/INVENTAIRE_JSON_ET_MIGRATION_BDD_UI.md) pour l’inventaire des JSON et le plan de migration).

---

## 6. Faut-il modifier le code caractéristique actuel avant les nouveautés ?

**En résumé : non, pas de refonte obligatoire.** Le code actuel couvre déjà la plupart des besoins ; les nouveautés portent surtout sur l’UI (3 panneaux, graphe, échantillonnage) et sur un **nouveau module Mapping** (tables + lecture par le scrapping).

### 6.1 Déjà en place

| Besoin | État actuel |
|--------|-------------|
| **Stockage** | Tables `characteristics` (nom, icon, color, unit, type, sort_order) et `characteristic_creature` / `characteristic_object` / `characteristic_spell` (min, max, default_value, conversion_formula, formula_display, conversion_dofus_sample, conversion_krosmoz_sample, entity). |
| **Formules** | Variables `[id]`, dés `NdX`, opérateurs, `floor`/`ceil`/`pow`/`sqrt`/etc., **table par caractéristique** (JSON avec seuils → formule ou valeur) = formules conditionnelles. |
| **Limites** | min/max peuvent être une valeur fixe ou une **formule** ; évaluation avec variables dans `CharacteristicGetterService::getLimits()`. |
| **Conversion** | `conversion_formula` lue par Getter, évaluée par `DofusConversionService` avec le service Formula. |
| **Types** | `CharacteristicLimitService` gère déjà **boolean**, **list** (value_available), et min/max pour les numériques. |
| **Échantillons** | Colonnes `conversion_dofus_sample` et `conversion_krosmoz_sample` (JSON niveau → valeur) ; exposées dans la définition par le Getter. |
| **UI admin** | `CharacteristicController` permet déjà d’éditer caractéristiques, entités par groupe, default_value, conversion_formula, min, max. |

### 6.2 Modifications optionnelles (backend)

- **Syntaxe `[min-max]`** (valeur aléatoire entière) : la doc vision mentionne `[1-6]` ; le moteur a déjà les dés `1d6`. Si on veut aussi `[1-6]`, il faut l’ajouter dans `FormulaResolutionService` / `SafeExpressionEvaluator` (optionnel).
- **Types** : vérifier que **boolean** et **list** sont bien utilisés partout (validation, formulaire admin, affichage) selon la doc ; compléter si des écrans ne les gèrent pas encore.

### 6.3 Où porte le travail principal

- **UI** : réorganiser / enrichir l’écran admin caractéristique en **3 panneaux** (Limite/valeur, Conversion + graphe + échantillonnage, Mapping), sans changer la structure BDD ni les services existants.
- **Mapping (panneau 3)** : **nouveau** — le lien DofusDB ↔ Krosmoz **par entité** est aujourd’hui dans les JSON (`entities/*.json`) et dans `FormatterApplicator` / `ConversionService`. Il faut ajouter des **tables** pour stocker ce mapping et faire en sorte que le **scrapping** lise la BDD (ou un cache dérivé) au lieu des fichiers JSON. Aucune modification du **service Characteristics** n’est nécessaire pour le mapping : il continue à recevoir une clé de caractéristique et à fournir formules/limites.

**Conclusion** : on peut démarrer les nouveautés (UI 3 panneaux, module Mapping) sans modifier en profondeur le code caractéristique actuel. Les éventuelles évolutions (syntaxe `[min-max]`, renforcement des types) peuvent se faire en parallèle ou après.
