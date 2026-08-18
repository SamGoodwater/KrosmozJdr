# IA générative — cadrage

Fonctionnalité **non implémentée**. Ce dossier fixe l’intention : une IA qui aide à produire du contenu **jouable en JDR**, pas à recopier Dofus.

À ne pas confondre avec les fichiers `_ai.md` du reste de `/docs` : ceux-là orientent les **agents de développement**. Ici, il s’agit d’un **LLM métier** (GPT, Claude, etc.) branché plus tard sur le site.

## Le problème

Le pipeline de scrapping convertit déjà DofusDB en entités Krosmoz (formules, clamps, normes). Les **nombres rentrent dans les bornes**. Ça ne suffit pas pour un JDR :

- sorts trop complexes ou illisibles à table ;
- monstres trop détaillés, stats peu pertinentes en partie ;
- valeurs « légales » mais aberrantes (sorts Terre sur une créature à Force nulle) ;
- PNJ : trop de paramètres (classe, voie, sorts, stuff, caracs) pour un algo classique cohérent et diversifié.

L’IA est pertinente là où il faut **du design** (simplifier, choisir, raconter). Le code reste la **loi** (limites, normes, cohérence).

## Principes

1. **Le code compte, l’IA décide et raconte, un humain publie.**
2. Pas de fine-tuning ni de modèle entraîné « Krosmoz » au départ. Un LLM du commerce + règles machine + exemples `playable` + JSON Schema + validateur PHP.
3. On ne dump pas la base dans le prompt. Laravel **pré-filtre** (catalogue compact, normes, gabarit) puis appelle le modèle.
4. L’IA n’écrit jamais en `playable`. Elle dépose une **proposition à relire**.
5. Les exemples donnés au modèle sont uniquement des fiches déjà `playable`.
6. Les objets forment un **petit catalogue JDR** (algo + grille), pas un miroir de Dofus. Monstres, sorts de créature et PNJ se font **au fil de l’eau**.

## Ce qu’on ne fait pas

- Entraîner un modèle sur tout le JDR avant d’avoir des étalons.
- Laisser le LLM appeler l’API en boucle pour retravailler un catalogue.
- Importer tous les objets / sorts / monstres Dofus « pour que l’IA ait le choix ».
- Publier une fiche générée sans relecture.
- Remplacer `SpellEffectsConversionService` ou les formules de caractéristiques par le LLM.

## Rôles de l’IA par type

| Type | Rôle de l’IA | Rôle de l’algo |
| --- | --- | --- |
| **Objets** | Flavour, objets uniques de scénario, cas bizarres | Conversion, 3–4 caracs par type, normes, dédoublonnage, grille de couverture |
| **Sorts de classe** | Réécriture JDR (1 effet principal + 0–2 secondaires) | Bornes PA/portée, mapping d’effets, element ↔ carac d’attaque |
| **Monstres** | Fiche + 2–3 actions cohérentes, à la demande | Gabarit niveau / PV / dégâts (règles 5.1.2) |
| **PNJ** | Concept, choix dans des listes, stats alignées | Pré-filtre stuff/sorts, validation ids, cohérence voie ↔ carac |

## Ordre de livraison

1. **Grille d’objets** par algorithme (~200–400 `playable`, pas tout Dofus). Sans ça, un PNJ n’a rien de cohérent à porter.
2. Descriptions machine + JSON Schema + ~20 étalons `playable` **par type** au moment où l’IA touche ce type.
3. **Rencontre à la demande** : un monstre et ses sorts-créature dans le même JSON, état `ai_review`.
4. **PNJ à la demande** : brief + listes préfiltrées (objets et sorts `playable`).

Les sorts de classe se réécrivent au fil de l’eau (quand un PNJ ou un perso en a besoin), pas en masse au départ.

## Documents à préparer (hors code)

- Contraintes machine par type (max d’effets, PA, cohérence élément ↔ carac, 1 item par slot…).
- JSON Schema de sortie (pas une fiche prose).
- Prompt superviseur court et stable (cacheable).
- Prompt de tâche par type (objet, sort, monstre, PNJ).
- ~20 fiches or `playable` par type concerné.

## Suite de lecture

- [ARCHITECTURE.md](./ARCHITECTURE.md) — pipeline, état, prompts.
- [CATALOGUE.md](./CATALOGUE.md) — objets et pré-filtre.
- [RENCONTRES.md](./RENCONTRES.md) — monstres, sorts liés, PNJ.
- [COUTS.md](./COUTS.md) — modèles et ordres de grandeur.
