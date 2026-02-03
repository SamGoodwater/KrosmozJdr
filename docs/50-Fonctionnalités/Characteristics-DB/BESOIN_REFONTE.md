# Besoin : refonte du paramétrage des caractéristiques

Résumé du besoin décrit, avec exemples. Approche retenue : **organisation par entité** (affichage, édition et modèle de données).

---

## 0. Vue d’ensemble : organisation par entité

L’organisation est **par entité**. Pour chaque entité (Monstre, Sort, Équipement, Classe, Ressource, Consommable, Panoplie, …), on dispose de la **liste des caractéristiques** associées à cette entité. Pour chaque caractéristique de cette liste, on configure au même endroit :

- **Valeurs limites** (min, max, éventuellement en computation)
- **Valeur par défaut**
- **Valeurs de conversion** (Dofus → Krosmoz)

**Exigence** : il doit être possible d’**ajouter une caractéristique à une entité donnée** (nouvelle ligne « cette entité a cette caractéristique » avec sa définition propre). Une même étiquette (ex. « PO ») peut donc avoir une **définition différente** selon l’entité (ex. PO = portée pour un sort, PO = déplacement pour un monstre), avec des limites, défauts et formules de conversion distincts.

### 0.1 Workflow utilisateur

1. Choisir une **entité** (ex. Sorts).
2. Voir la **liste des caractéristiques** de cette entité (nom, limites, défaut, conversion).
3. **Éditer** une caractéristique existante (limites, défaut, conversion) ou **ajouter** une nouvelle caractéristique à cette entité.
4. Pour une nouvelle caractéristique : saisir la définition (nom, short_name, unit, helper, etc.) et les règles (min, max, défaut, conversion) spécifiques à cette entité.

Cette vision est plus simple à utiliser, plus propre, et permet naturellement des définitions différentes par entité pour des caractéristiques qui portent le même nom.

### 0.2 Contenu par type d’entité (rappel)

| Entité | Contenu config (limites, défaut, conversion) |
|--------|----------------------------------------------|
| **Monstre et NPC** | min, max, computation, défaut, message_erreur ; computation de conversion Dofus → Krosmoz. |
| **Équipement** | min/max en computation (niveau), types d’équipement concernés, required, forgemagie (allowed, max), prix unité / rune, conversion Dofus. |
| **Sort** | min, max, computation ; conversion Dofus pour éditer les formules. |
| **Classe** | min, max, défaut, message_erreur (pas de formule ni conversion Dofus). |
| **Panoplies, ressources, consommables** | level, optionnellement price, weight ; min, max, défaut, message_erreur ; conversion Dofus éventuelle. Consommables de soin : effet (formule soin, bonus). |

À part pour les sorts : les **effets** de sorts (dommages, soin, retrait, etc.) restent une paramétration à part (types d’effets, voir § 6.3).

---

## 1. Deux usages distincts (mais traitables de la même façon)

| Usage | Description | Point commun |
|-------|-------------|--------------|
| **Calcul / évolution** | Calcul d’une caractéristique à partir des autres et de son évolution dans le cadre KrosmozJDR (ex. vie = f(vitalité, niveau)). | **Formules** : expressions qui lient des caractéristiques entre elles. |
| **Conversion Dofus → KrosmozJDR** | Transformation d’une valeur issue du jeu Dofus en une caractéristique exploitable par KrosmozJDR (ex. niveau Dofus → niveau JDR). | Même idée de **formules** (ou structures équivalentes) pour décrire la transformation. |

Les deux peuvent donc être modélisées et implémentées de façon unifiée (moteur de formules, stockage formule ou “computation” par caractéristique/entité).

---

## 2. Structure d’une caractéristique (par entité)

En **organisation par entité**, une caractéristique est définie **au niveau du couple (entité, clé)**. Chaque ligne « entité X a la caractéristique Y » porte toute la définition nécessaire pour cette entité : affichage, limites, défaut, conversion. Ainsi, **ajouter une caractéristique à une entité** = créer une nouvelle ligne avec (entity, characteristic_key) et ses champs.

### 2.1 Champs de définition (par entité + clé)

Pour chaque **(entity, characteristic_key)** on stocke :

| Propriété | Description |
|-----------|-------------|
| **characteristic_key** | Clé métier (ex. `pa`, `po`, `life`, `spell_range`). Unique **dans l’entité** (deux entités peuvent avoir une clé `po` avec des définitions différentes). |
| **name** | Nom affiché pour cette entité (ex. « Portée » pour sort, « PO » pour monstre). |
| **short_name** | Nom court (ex. « PV », « PA », « PO »). |
| **helper** | Texte d’aide / infobulle. |
| **descriptions** | Description(s) textuelle(s). |
| **icon** | Lien vers l’icône. |
| **color** | Couleur (ex. hex). |
| **unit** | Unité si applicable. |

### 2.2 Champs de règles (limites, défaut, conversion)

| Propriété | Description |
|-----------|-------------|
| **min** | Borne minimale : valeur fixe ou formule / computation. |
| **max** | Borne maximale : valeur fixe ou formule / computation. |
| **computation** | Calcul par seuils (voir § 3) : caractéristique de référence + paliers → formules. |
| **default** | Valeur par défaut (fixe ou computation). |
| **validation_message** | Message d’erreur personnalisé (ex. « Le bonus PA doit être entre :min et :max »). |
| **Conversion Dofus → Krosmoz** | Stockée dans une table dédiée (ex. dofusdb_conversion_formulas) référencée par (entity, characteristic_key). |

Pour l’entité **équipement** (item), en plus : types d’équipement concernés (slots), required, forgemagie_allowed, forgemagie_max, base_price_per_unit, rune_price_per_unit (voir § 5.3). Les paliers par niveau (bracket_max) restent sur equipment_slot_characteristics (lien slot ↔ caractéristique).

Exemple : pour l’entité « classe », la caractéristique `life` a une **computation** qui dépend de la vitalité et du niveau ; **min** et **max** pour valider ; **validation_message** personnalisé. Pour l’entité « sort », la caractéristique `po` (portée) a ses propres min/max et sa conversion Dofus, indépendants du `po` monstre.

### 2.3 Modèle de données recommandé (centré entité)

Pour supporter l’**organisation par entité** et l’**ajout de caractéristiques à une entité donnée**, le modèle suivant est recommandé.

**Table principale : `entity_characteristics`** (une ligne = une caractéristique attachée à une entité, avec sa définition complète)

| Colonne | Type | Rôle |
|---------|------|------|
| **id** | PK (uuid ou bigint) | Identifiant technique (optionnel si clé composite suffit). |
| **entity** | string | Entité (monster, spell, item, class, resource, consumable, panoply, …). |
| **characteristic_key** | string | Clé de la caractéristique dans cette entité (ex. `pa`, `po`, `life`). Unique avec entity : UNIQUE(entity, characteristic_key). |
| **name** | string | Nom affiché. |
| **short_name** | string, nullable | Nom court. |
| **helper** | text, nullable | Texte d’aide. |
| **descriptions** | text/json, nullable | Description(s). |
| **icon** | string, nullable | Icône. |
| **color** | string, nullable | Couleur. |
| **unit** | string, nullable | Unité. |
| **sort_order** | int | Ordre d’affichage dans la liste de l’entité. |
| **min** | valeur ou nullable | Borne min (entier ou expression selon implémentation). |
| **max** | valeur ou nullable | Borne max. |
| **formula** | text, nullable | Formule simple (optionnel). |
| **computation** | json, nullable | Calcul par seuils (caractéristique de référence + paliers). |
| **default_value** | string, nullable | Valeur par défaut. |
| **required** | boolean | Champ obligatoire ou non. |
| **validation_message** | text, nullable | Message d’erreur. |
| **forgemagie_allowed** | boolean | (Pour entity=item) Forgemagie autorisée. |
| **forgemagie_max** | int, nullable | (Pour entity=item) Max forgemagie. |
| **base_price_per_unit** | decimal, nullable | (Pour entity=item) Prix unité. |
| **rune_price_per_unit** | decimal, nullable | (Pour entity=item) Prix rune. |

**Ajouter une caractéristique à une entité** = `INSERT` dans `entity_characteristics` (entity, characteristic_key, name, …). Aucune table « caractéristique globale » n’est obligatoire : chaque ligne porte sa définition. Optionnellement, une table **characteristics** (bibliothèque / templates) permet de proposer des modèles lors de l’ajout (pré-remplissage name, unit, etc.), sans être la source de vérité pour les valeurs par entité.

**Conversion Dofus** : table **dofusdb_conversion_formulas** avec clé (entity, characteristic_key) — ou (entity_characteristic_id) si on référence l’id de entity_characteristics — et colonnes formula_type, conversion_formula, handler_name, etc.

**Équipement (slots)** : conserver **equipment_slots** et **equipment_slot_characteristics**. Lier à la caractéristique par (entity=item, characteristic_key) : equipment_slot_characteristics(slot_id, entity, characteristic_key, bracket_max, forgemagie_max, base_price_per_unit, rune_price_per_unit). Ainsi, pour l’entité « item », les caractéristiques sont dans entity_characteristics ; les paliers par slot restent dans equipment_slot_characteristics.

**Résumé** : une seule table principale **entity_characteristics** par (entity, characteristic_key) avec toute la définition (nom, limites, défaut, conversion référencée ailleurs). Ajout de caractéristique = nouvelle ligne ; même nom « PO » pour sort et monstre = deux lignes différentes (entity=spell, key=po et entity=monster, key=po) avec des name/limites/conversion différents.

---

## 3. Formule et computation

### 3.1 Formule

- **Formule** : expression qui référence d’autres caractéristiques entre crochets, ex. `[vitality]`, `[level]`, et combine avec constantes et opérateurs (ex. `+`, `*`, `-`, `/`, éventuellement des fonctions comme `floor`, `ceil`, `abs`, `round`, `exp`, `sqrt`, `sin`, `cos`, `tan`, `asin`, `acos`, `atan`, `sinh`, `cosh`, `tanh`, `asinh`, `acosh`, `atanh`, `pow`, `log`, `log10`).
- Les valeurs réelles sont substituées au moment du calcul (ex. `[vitality]` → 12, `[level]` → 5).
- Une formule peut aussi être remplacée par une **valeur fixe** (nombre ou string).

### 3.2 Computation (calcul par seuils)

La **computation** décrit comment **calculer** la valeur d’une caractéristique en fonction d’une **caractéristique de comparaison** et de **seuils** :

- On choisit une caractéristique de référence (ex. `level`).
- On associe à **chaque seuil** (valeur de cette caractéristique) une **formule** (ou une valeur fixe).
- Règle d’application : on utilise la formule du **plus grand seuil** qui est **inférieur ou égal** à la valeur courante de la caractéristique de comparaison.

C’est exactement le même principe que les « tables par caractéristique » déjà utilisées en conversion Dofus (ex. niveau Dofus → niveau JDR avec des paliers).

---

## 4. Exemple détaillé : points de vie (life) pour l’entité « classe »

**Besoin** : la vie dépend de la vitalité, mais la formule change selon le niveau.

- **Niveau 1 à 7** : par ex. `[vitality] * 2 + 3` (ou toute formule que tu veux).
- **À partir du niveau 8** : par ex. `[vitality] * 5 + 10`.

**Computation** (structure proposée) :

- **Caractéristique de comparaison** : `level`.
- **Seuils** :
  - pour `level >= 1` → formule 1 : `[vitality]*2+3` ;
  - pour `level >= 8` → formule 2 : `[vitality]*5+10`.

En JSON (à valider selon ton format exact) :

```json
{
  "characteristic": "level",
  "1": "[vitality]*2+3",
  "8": "[vitality]*5+10"
}
```

Interprétation :

- Si `level = 3` → on prend le seuil 1 → formule `[vitality]*2+3`.
- Si `level = 8` ou plus → on prend le seuil 8 → formule `[vitality]*5+10`.

**Min / max** (exemple) : pour « classe », on pourrait avoir par ex. `min = 1`, `max = 500` (valeurs fixes) ou des formules si besoin.

---

## 5. Autres exemples rapides

### 5.1 PA d’un sort vs PA d’un monstre

- **Partie généraliste** : une seule caractéristique `pa` (name, short_name « PA », unit « PA », etc.).
- **Partie par entité** :
  - **Entité « sort »** : min/max et éventuellement computation pour les PA du sort (sens « coût en PA du sort »).
  - **Entité « monstre »** : min/max et éventuellement computation pour les PA du monstre (sens « points d’action par tour »).

Même `id` de caractéristique, **paramétrage différent par entité** (min, max, computation).

### 5.2 Conversion Dofus → KrosmozJDR (niveau)

- Même idée de « computation » ou formule par seuil : on a une **caractéristique de comparaison** (ex. niveau Dofus `d`) et des seuils avec formules ou valeurs.
- Ex. : pour `d < 800` → `floor(1 + 19 * sqrt(([d]-1)/799))` ; pour `d >= 800` → `floor(20 + 10*(([d]-800)/2))`.
- Ça peut être stocké dans une structure du même type que la computation (caractéristique = la valeur Dofus, seuils → formules).

### 5.3 Cas particulier : équipement

Pour l’**entité équipement** (ou par type d’équipement), la caractéristique a des paramètres supplémentaires. Min et max restent possibles en **valeur ou formule** ; pour l’équipement ils sont souvent **liés au niveau** (système de paliers / computation).

| Paramètre | Description |
|-----------|-------------|
| **min** | Borne min : valeur fixe ou **computation** (ex. par palier de niveau). |
| **max** | Borne max : valeur fixe ou **computation** (ex. par palier de niveau). |
| **default_value** | Valeur par défaut : **valeur fixe** ou **computation** (ex. 0 pour un bonus). |
| **required** | Indique si la caractéristique est **obligatoire** pour cet équipement (ex. au moins une valeur renseignée). |
| **applicable_to_equipment_types** | Liste des **types d’équipement** sur lesquels cette caractéristique peut apparaître (ex. amulette, anneau, cape). Les types n’ont pas encore d’ID dédié en base ; on peut utiliser un slug (ex. `amulet`). Une même caractéristique peut être autorisée sur **plusieurs types** (ex. PA sur amulette, life sur chapeau / cape / anneau). |
| **forgemagie_allowed** | Autoriser ou non la **forgemagie** pour cette caractéristique sur cet équipement. |
| **forgemagie_min** | Si forgemagie autorisée : borne min du bonus forgemagie — **valeur fixe** ou **computation**. |
| **forgemagie_max** | Si forgemagie autorisée : borne max du bonus forgemagie — **valeur fixe** ou **computation**. |
| **price_per_unit** | Prix moyen pour **une unité** de cette caractéristique (ex. kamas par point de bonus). |
| **rune_price_per_unit** | Prix pour **une rune** de forgemagie (une unité) pour cette caractéristique. |
| **validation_message** | Message personnalisé en cas d’erreur de validation (ex. « Le bonus PA de l’objet doit être entre :min et :max (équipement +6 max, forgemagie +1 max). »). |

**Exemple (PA sur amulette)** : applicable_to_equipment_types = `["amulet"]`, min/max en computation par niveau (paliers 1–2, 3–4, …), default_value = 0, required = false, forgemagie_allowed = true, forgemagie_min = 0, forgemagie_max = 1 (ou formule), price_per_unit et rune_price_per_unit pour l’économie de jeu.

**Exemple (vie sur plusieurs slots)** : applicable_to_equipment_types = `["hat", "cape", "amulet", "boots", "ring"]`, même principe min/max par niveau, forgemagie possible avec plafond, etc.

---

## 6. Complexité par type d’entité

### 6.1 Classes

- **Simple** : pas de formule associée aux caractéristiques.
- Les caractéristiques de classe sont essentiellement des valeurs à afficher ou à valider (min/max éventuels), sans computation ni conversion par seuils.

### 6.2 Panoplies, ressources, consommables

- **Peu de paramétrage** : peu de caractéristiques à configurer spécifiquement.
- Principales à prendre en compte :
  - **level** (niveau) : toujours pertinent.
  - **price** (prix) : optionnel, utile pour ressources et consommables.
  - **weight** (poids) : optionnel, utile pour ressources et consommables.
- Le reste peut se limiter à min/max fixes ou à une liste réduite de caractéristiques sans formules complexes.

### 6.3 Sorts (spells) : deux parties distinctes

Les sorts sont le cas le plus complexe car ils combinent **deux niveaux** de description :

| Partie | Description | Exemples |
|--------|-------------|----------|
| **Caractéristiques du sort** | Propriétés qui **décrivent** le sort (coût, portée, cible, zone, etc.). | `pa`, `po`, `po_editable`, `target`, `area`, cooldown, ligne de vue, etc. |
| **Effets** | Description des **effets** du sort, qui peuvent **très fortement varier** d’un sort à l’autre. | Dommages (élément, jet, fixe), types (terre, feu, …), attributs (force, agilité, …), résistances, retrait PA/PM, soin, esquive, buffs, débuffs, invocation, etc. |

- **Caractéristiques du sort** : même modèle que le reste (id, min/max, computation éventuelle, validation_message). Ex. : PA du sort entre 0 et 8, PO selon paliers, cible dans une liste (unique, cercle, ligne, …).
- **Effets** : structure plus riche et variable selon le **type d’effet** (dommages, soin, retrait, buff, …). Chaque type d’effet peut avoir ses propres champs (montant, élément, condition, durée, etc.). Le paramétrage doit donc prévoir :
  - une **taxonomie des types d’effets** (déjà en place avec `spell_effect_types`) ;
  - pour chaque type d’effet, les **champs attendus** (paramètres, formules éventuelles, validations) ;
  - une description des **effets** concrets d’un sort (liste d’effets, chacun typé et paramétré).

En résumé : pour les sorts, il faut distinguer clairement le **paramétrage des caractéristiques du sort** (aligné sur le modèle commun) du **paramétrage des effets** (structure variable selon le type d’effet, à modéliser à part).

---

## 7. Synthèse

| Bloc | Contenu |
|------|--------|
| **Organisation** | **Par entité** : pour chaque entité, liste des caractéristiques (entity, characteristic_key) avec définition complète. Possibilité d’**ajouter une caractéristique à une entité donnée**. Même nom (ex. PO) peut avoir des définitions différentes selon l’entité (sort vs monstre). |
| **Définition par (entity, characteristic_key)** | name, short_name, helper, descriptions, icon, color, unit ; min, max, computation, default_value, required, validation_message ; conversion Dofus (table dédiée). Pour entity=item : forgemagie_*, price_*, equipment_slots (paliers par slot). |
| **Deux usages** | (1) Calcul / évolution en JDR, (2) Conversion Dofus → JDR ; formules et computation par seuils. |
| **Computation** | Caractéristique de référence + pour chaque seuil une formule (ou valeur fixe) ; on applique la formule du plus grand seuil ≤ valeur courante de la caractéristique de référence. |
| **Complexité par entité** | **Classes** : pas de formule. **Panoplies / ressources / consommables** : peu de paramétrage (level, price, weight). **Équipement** : forgemagie, types, prix, paliers. **Sorts** : caractéristiques du sort + effets (types d’effets à part). |

---

## 8. Conformité avec les documents du jeu (α 0.1.3.0)

Comparaison avec les PDF de référence (`docs/110- To Do/`) pour vérifier que le besoin de refonte couvre bien les règles du JDR.

### 8.1 Caractéristiques.pdf

| Élément doc jeu | Alignement avec le besoin |
|-----------------|---------------------------|
| Tableau : Caractéristique, Valeur de base, Valeur limite, Modification équipements, Forgemagie (max, valeur max) | **Conforme.** Partie généraliste (nom, unité, etc.) + par entité **équipement** : min/max (souvent en computation par niveau), forgemagie (autorisée + max), prix unité / rune. Les colonnes « Valeur limite » et formules (ex. `[niveau/2+1]+`) correspondent à min/max et computation. |
| Caractéristiques listées (PA, PM, Ini, PO, Vitalité, modificateurs, PV, résistances, compétences, etc.) | **Conforme.** Une caractéristique = un id + config généraliste + config par entité selon les cas (classe, équipement, etc.). |
| Modificateur = f(caractéristique de base, équipement) ; forgemagie incluse dans le calcul | **Conforme.** Computation avec caractéristique de référence et formules par seuil ; conversion Dofus → Krosmoz pour importer données équipement. |

**Conclusion** : Le besoin couvre bien la structure du tableau Caractéristiques et la distinction équipement / forgemagie / limites.

### 8.2 Equipements et forgemagie.pdf

| Élément doc jeu | Alignement avec le besoin |
|-----------------|---------------------------|
| Types d’équipement : ARMES, CHAPEAUX, CAPES, AMULETTES, BOTTES, ANNEAUX, CEINTURES, BOUCLIERS | **Conforme.** `applicable_to_equipment_types` (liste de types/slugs) par caractéristique. |
| Bonus en fonction du niveau (paliers 1-2, 3-4, …, 19-20) | **Conforme.** Min/max en **computation** par niveau (une ou plusieurs formules par palier). |
| Prix par unité ; Forgemagie : bonus max, prix de la rune par unité | **Conforme.** `price_per_unit`, `rune_price_per_unit`, `forgemagie_max` (computation). |
| Règles forgemagie : un seul type de rune par objet, pas de cumul du même bonus sur plusieurs équipements | **Conforme.** Règles métier à appliquer côté jeu ; le paramétrage (forgemagie autorisée, max, prix) est bien décrit. |

**Conclusion** : Le besoin reflète fidèlement le tableau équipements et la section forgemagie.

### 8.3 Creation sort.pdf (équilibrage des sorts)

| Élément doc jeu | Alignement avec le besoin |
|-----------------|---------------------------|
| Caractéristiques des sorts : PA, PM, coût PA, dégâts/soin/protection par niveau et par rôle (1er / 2nd / Pas de rôle) | **Conforme.** Config **sort** : min, max, computation (y compris par niveau / rôle si besoin). Les formules d’équilibrage (dégâts max, soin max, retrait PA/PM, etc.) peuvent être portées en computations ou en références pour édition des formules Dofus → Krosmoz. |
| Niveaux de sorts (1, 3, 5, …), montées à certains niveaux | **Conforme.** Géré par **niveau** comme caractéristique de référence dans les computations. |
| Effets variés : dégâts (cac, dist. courte/longue, zone), retrait PA/PM, soin, protection (PV temporaire, bouclier), amélioration PA/PM, etc. | **Conforme.** Distinction **caractéristiques du sort** (pa, po, target, area, …) vs **effets** (paramétration à part, par type d’effet : dommages, soin, retrait, esquive, etc.). |
| Rôles (Dégât, Tank, Entrave, Soin, …) et Voix (Feu, Eau, Terre, Air) | **Conforme.** Rôles/voix = métadonnées de classe/sort ; le paramétrage des caractéristiques et des effets reste indépendant (min/max/computation par entité). |

**Conclusion** : La double partie « caractéristiques du sort » + « effets (par type) » et la conversion Dofus → Krosmoz pour les sorts sont alignées avec le doc d’équilibrage.

### 8.4 Généralités Classes.pdf

| Élément doc jeu | Alignement avec le besoin |
|-----------------|---------------------------|
| Classes définies par rôles (Dégât, Tank, Entrave, …) et voies élémentaires (Feu, Eau, Terre, Air) | **Conforme.** Pas de **formule** sur les caractéristiques des classes ; le besoin précise « Classe : pas de formule ; min, max, défaut, message_erreur ». Les rôles/voix sont des attributs de classe, pas des computations de caractéristiques. |

**Conclusion** : Pas de conflit ; le besoin reste cohérent avec les généralités classes.

### 8.5 Système de soin.pdf

| Élément doc jeu | Alignement avec le besoin |
|-----------------|---------------------------|
| Potions : niveau (Lvl), soin (formule en dés, ex. 2d4+2), soin moyen, prix/unité | **Conforme.** Consommables : **level**, **price** (prix/unité), et une notion d’**effet** (soin = formule en dés). Le besoin mentionne « level, optionnellement price, weight » ; pour les consommables de soin, il faut bien prévoir **level**, **price** et une description du **soin** (formule ou effet). |
| Pains : niveau, soin (1d4+1, etc.), bonus (ex. PV temporaire), prix/unité | **Conforme.** Même idée : level, price, + effet (soin, bonus). |
| Dés de vie, réserve Wakfu (hors soins sorts) | **Conforme.** Caractéristiques de personnage/classe ; pas de changement par rapport au besoin. |

**Point d’attention** : Pour **consommables** (potions, pains), le besoin pourrait expliciter qu’en plus de **level**, **price** et **weight**, il existe un **effet** (ex. soin avec formule en dés, bonus PV temporaire). Soit cet effet est une caractéristique dédiée (ex. `heal_formula`), soit il est modélisé comme un mini-« effet » associé au consommable (proche des effets de sorts mais plus simple). À trancher en refonte.

**Conclusion** : Globalement conforme ; ajouter explicitement la notion d’**effet / formule de soin** (et éventuellement bonus) pour les consommables de soin dans le besoin si on veut couvrir à 100 % le système de soin.

---

### Synthèse conformité

| Document | Conforme | À préciser |
|----------|----------|------------|
| Caractéristiques.pdf | Oui | — |
| Equipements et forgemagie.pdf | Oui | — |
| Creation sort.pdf | Oui | — |
| Généralités Classes.pdf | Oui | — |
| Système de soin.pdf | Oui | Expliciter **effet / formule de soin** (et bonus) pour consommables (potions, pains). |

Le besoin de refonte est **globalement en accord** avec les documents qui décrivent le jeu. La seule précision recommandée est de formaliser, pour les consommables de soin, le paramétrage de l’effet (formule de soin, bonus éventuels) en plus de level, price et weight.

---

## 9. Stratégie de refonte : modèle centré entité

Le plan de refonte est basé sur l’**organisation par entité** et la possibilité d’**ajouter des caractéristiques à une entité donnée** (voir § 0 et § 2.3).

### 9.1 Deux options de mise en œuvre BDD

| Option | Description | Avantages | Inconvénients |
|--------|-------------|-----------|---------------|
| **A. Nouvelle table `entity_characteristics`** | Créer la table décrite en § 2.3 (entity, characteristic_key, name, short_name, …). Migrer les données depuis characteristics + characteristic_entities (characteristic_id → characteristic_key, copier name/short_name depuis characteristics dans chaque ligne). À terme, characteristics peut devenir une **bibliothèque de templates** optionnelle (pré-remplissage à l’ajout). | Modèle clair, une seule source de vérité par (entity, key). Ajout de caractéristique = INSERT simple. | Migration des données et adaptation des services (lecture par entity d’abord). |
| **B. Faire évoluer l’existant** | Garder **characteristics** (templates / bibliothèque) et **characteristic_entities**. Ajouter sur characteristic_entities les colonnes **name**, **short_name**, **helper**, **unit** (nullable) : quand renseignées, elles **surchargent** la définition de characteristics pour cette entité. « Ajouter une caractéristique à une entité » = créer une ligne characteristic_entities (en créant éventuellement une ligne characteristics si nouveau concept). | Moins de changement de schéma, compatibilité avec le code actuel. | Deux tables à maintenir, logique de fusion template + surcharge à gérer. |

**Recommandation** : **Option A** si tu veux un modèle simple et cohérent à long terme (une ligne = une définition complète par entité). **Option B** si tu privilégies un minimum de migrations et que tu acceptes la dualité template + surcharge.

### 9.2 Tables communes aux deux options

- **equipment_slots** : inchangée.
- **equipment_slot_characteristics** : conserver ; lier par (entity=item, characteristic_key) ou par characteristic_id selon l’option (A : characteristic_key ; B : characteristic_id).
- **dofusdb_conversion_formulas** : adapter la clé à (entity, characteristic_key) en option A, ou garder (characteristic_id, entity) en option B.
- **dofusdb_conversion_config** : inchangée.
- **spell_effect_types** : inchangée.

### 9.3 Services

- **CharacteristicService** (ou nouveau **EntityCharacteristicService** en option A) : exposer la config **par entité** (ex. `getCharacteristicsForEntity(string $entity)` → liste des caractéristiques avec name, limits, default, conversion). Conserver aussi une forme « par caractéristique » si des consommateurs (ValidationService, DofusDbConversionFormulas) en ont besoin (construction à partir des données par entité).
- **ValidationService**, **FormulaEvaluator**, **DofusDbConversionFormulaService**, **DofusDbConversionFormulas**, **EquipmentCharacteristicService** : conserver ; adapter les entrées (identifier une caractéristique par (entity, characteristic_key) ou par characteristic_id selon l’option retenue).

### 9.4 Résumé

- **Modèle cible** : une définition complète par (entity, characteristic_key), avec possibilité d’**ajouter une caractéristique à une entité** (nouvelle ligne).
- **Option A** : table **entity_characteristics** = source de vérité ; migration depuis l’existant.
- **Option B** : **characteristics** + **characteristic_entities** avec surcharges name/short_name/helper par entité ; ajout = nouvelle ligne characteristic_entities (et évent. characteristics).
- Les deux options permettent un **affichage et une édition par entité** et des **définitions différentes** pour un même nom (ex. PO sort vs PO monstre).

---

## 10. Plan d’amélioration progressive (centré entité)

**Approche** : avancer par étapes en partant du modèle centré entité ; tu complètes les données et les cas d’usage au fur et à mesure.

### Ordre suggéré (à ajuster selon ton choix Option A ou B)

| Priorité | Action | Effort |
|----------|--------|--------|
| 1 | **Choix BDD** : décider Option A (nouvelle table entity_characteristics + migration) ou Option B (évolution characteristic_entities + surcharges). | — |
| 2 | **Schéma** : en A, créer la table entity_characteristics et migrer les données ; en B, ajouter colonnes name/short_name/helper (nullable) sur characteristic_entities. | Moyen (A) / Faible (B) |
| 3 | **Services** : exposer une API « par entité » (ex. getCharacteristicsForEntity($entity)) et permettre la résolution par (entity, characteristic_key). Adapter DofusdbConversionFormulas / equipment_slot si clé passe à (entity, key). | Moyen |
| 4 | **UI / admin** : écrans **par entité** — sélection d’une entité → liste des caractéristiques de cette entité → édition limites, défaut, conversion. | Moyen |
| 5 | **Ajout de caractéristique** : dans l’UI, bouton « Ajouter une caractéristique » sur la page d’une entité → formulaire (key, name, short_name, unit, limites, défaut, conversion) → enregistrement (INSERT entity_characteristics ou characteristic_entities + optionnel characteristics). | Moyen |
| 6 | Données : compléter les entités (resource, consumable, panoply) et les champs computation / conversion au fil de l’eau. | Continu |
| 7 | DofusdbConversionConfigService : observer + export ; rareté, effet consommables (soin), etc. | Faible / variable |

Ce document et le [RESUME_EXISTANT](RESUME_EXISTANT.md) restent la référence pour l’état du système et les prochaines étapes.
