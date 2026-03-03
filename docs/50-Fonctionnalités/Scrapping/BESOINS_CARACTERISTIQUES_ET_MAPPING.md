# Besoins — Caractéristiques et mapping DofusDB ↔ KrosmozJDR

**Date :** 2026-03-03  
**Contexte :** Liste des besoins pour la refonte du scrapping, en commençant par les **caractéristiques** et le **mapping**. Ce document résume les besoins exprimés et signale les points à ne pas oublier.

---

## 1. Résumé des besoins exprimés

### 1.1 Contexte API DofusDB

- L’API DofusDB est **complexe** : structure différente de KrosmozJDR, beaucoup de propriétés **cachées derrière des IDs**.
- Il faut donc **plusieurs mappings** (pas un seul).
- **Problème actuel :** les mappings ne sont pas faits de la même manière partout (JSON entité, BDD scrapping_entity_mappings, constante PHP pour effectId, JSON dofusdb_characteristic_to_krosmoz pour les bonus items).

### 1.2 Source de vérité : base de données

- La **description des caractéristiques** (et tout ce qui s’y rapporte) doit avoir pour **source de vérité la base de données**, pas un fichier de config.
- On s’oriente vers : tout ce qui définit une caractéristique (propriétés généralistes, conversion, **mapping DofusDB**) stocké en BDD et piloté par le **service de caractéristiques**.

### 1.3 Différencier les caractéristiques par contexte

- Une même notion (ex. PA, niveau) doit être **différenciée** selon le contexte d’usage :
  - **Spell** (sort) : coût en PA, niveau du sort, portée du sort, etc.
  - **Créature** (monstre, PNJ, joueur) : PA par tour, niveau de la créature, etc.
  - **Object** (item : consommable, équipement, ressource) : bonus PA donné par l’objet, niveau de l’objet, etc.
- C’est déjà le cas dans l’existant avec les **groupes** creature / object / spell et les tables characteristic_creature, characteristic_object, characteristic_spell. Le besoin confirme qu’on garde cette séparation et qu’on y **accroche** aussi le mapping.

### 1.4 Contenu de la « liste » de caractéristiques (en BDD)

Dans cette liste (définitions en BDD), on veut :

- **Propriétés généralistes :** icône, nom, id Krosmoz (clé), description, couleur, **limites pour le JDR** (min, max, type, value_available selon le type).
- **Valeurs pour la conversion** Dofus → Krosmoz (formules, échantillons, etc.).
- **Le mapping** : lien entre « ce que dit l’API DofusDB » et « cette caractéristique KrosmozJDR ». Aujourd’hui ce n’est pas poussé ; on veut aller plus loin.

### 1.5 Mapping DofusDB ↔ KrosmozJDR dans le service de caractéristiques

- Le **mapping entre l’API DofusDB et KrosmozJDR** doit être **enregistré dans le service de caractéristiques** (en base de données), pas éclaté entre JSON, constantes PHP et table scrapping_entity_mappings sans lien fort à la caractéristique.
- Idée : une caractéristique Krosmoz « sait » comment elle se remplit à partir de DofusDB (chemin, contexte, formatter, etc.).

### 1.6 ID DofusDB des caractéristiques

- **Enregistrer l’id DofusDB** concernant les caractéristiques (pas le cas encore).
- Cet id correspond à ce que l’API DofusDB expose (ex. `GET /characteristics`, ou `item.effects[].characteristic`). Cela peut servir pour le **gros des propriétés** (bonus d’équipement, résistances, etc.) : un même id DofusDB → une caractéristique Krosmoz donnée.

### 1.7 Profondeur dans DofusDB

- Les caractéristiques peuvent avoir des **profondeurs différentes** dans DofusDB :
  - Données **au niveau racine** de l’entité (ex. monster.grades[0].level).
  - Données **imbriquées** (ex. spell-levels[].effects[].effectId + valeur ; item.effects[].characteristic + value).
- Le mapping doit **prendre en compte cette profondeur** (chemin d’accès, contexte « à quel niveau » on lit la valeur).

---

## 2. Synthèse en quelques points

| # | Besoin |
|---|--------|
| 1 | **Plusieurs mappings** cohérents, car l’API DofusDB est complexe (structure différente, propriétés en IDs). |
| 2 | **Unifier la façon** dont les mappings sont définis : même modèle (idéalement porté par le service de caractéristiques en BDD). |
| 3 | **Source de vérité en BDD** pour la description des caractéristiques (et tout ce qui s’y rapporte), pas en config. |
| 4 | **Différencier** les caractéristiques selon le contexte : spell, créature (monstre/PNJ), object (consommable, équipement, ressource). |
| 5 | Dans la définition BDD d’une caractéristique : **propriétés généralistes** (icône, nom, id Krosmoz, description, couleur, limites JDR) + **conversion** (formules, etc.) + **mapping** DofusDB → cette caractéristique. |
| 6 | **Mapping DofusDB ↔ KrosmozJDR** stocké dans le **service de caractéristiques** (en BDD), pas dispersé. |
| 7 | **Stocker l’id DofusDB** de la caractéristique (ex. id de `GET /characteristics` ou équivalent) pour faire le lien avec le gros des propriétés (bonus, résistances, etc.). |
| 8 | **Prendre en compte la profondeur** dans DofusDB : mapping possible au niveau racine ou dans des structures imbriquées (effets de sort, effects[] des items). |

---

## 3. Points à ne pas oublier (compléments)

À partir de l’existant et de la doc, voici des éléments à garder en tête pour ne rien oublier :

### 3.1 Déjà en place côté caractéristiques (à réutiliser / étendre)

- **Tables** : `characteristics` (définition générale), `characteristic_creature`, `characteristic_object`, `characteristic_spell` (par groupe et entité). Déjà : min, max, default_value, conversion_formula, formula_display, conversion_dofus_sample, conversion_krosmoz_sample, type, value_available, db_column, entity (`*` ou entité précise).
- **Lien / copie** : une caractéristique peut être **liée** à une caractéristique maître (`linked_to_characteristic_id`) : la liée n’a pas de paramètres propres, elle affiche et utilise la config de la maître. **Si le mapping DofusDB est différent** entre deux caractéristiques, on ne peut pas garder le lien (chaque caractéristique doit avoir son propre mapping). Le lien reste pertinent pour **toutes les caractéristiques qui ne proviennent pas de DofusDB** (définitions purement Krosmoz partagées entre groupes).
- **db_column** : le lien entre « caractéristique » et « colonne BDD / champ » du modèle (ex. level_creature → colonne `level`). Le mapping DofusDB devra aboutir à ce champ ; donc soit le mapping est attaché à la caractéristique et pointe vers son db_column, soit vers un « modèle + champ » explicite.

### 3.2 Mapping : plusieurs niveaux à couvrir

- **Niveau 1 — Propriété « plate »** : un chemin DofusDB (ex. grades.0.level) → une caractéristique Krosmoz (ex. level_creature) → un modèle/champ (creatures.level). C’est ce que fait aujourd’hui scrapping_entity_mappings (from_path, to model/field).
- **Niveau 2 — Propriété identifiée par un ID** : un **id DofusDB** (ex. characteristic id 15 = intelligence) → une caractéristique Krosmoz (intel_object). Utilisé pour item.effects[].characteristic, et potentiellement pour d’autres endpoints (ex. /characteristics). Stocker cet id sur la caractéristique (dofusdb_characteristic_id) permet de faire le lien sans fichier JSON séparé.
- **Niveau 3 — Profondeur (effets de sort, bonus item)** : la valeur est **à l’intérieur** d’une structure (spell-levels[].effects[], item.effects[]). Il faut pouvoir exprimer : « pour cette caractéristique (ex. dommages feu spell), la valeur vient de tel type de nœud (effectId X, ou characteristic Y) et tel chemin (diceNum, diceSide, value, etc.) ». Aujourd’hui : effectId → sous-effet en PHP ; characteristic id → key en JSON. À ramener dans le modèle « caractéristique + mapping en BDD ».

### 3.3 Entité cible du mapping

- Une même caractéristique logique peut être **mappée différemment selon l’entité** DofusDB (monster vs item vs spell) : chemin différent, ou même id DofusDB mais formule de conversion différente. Donc le **mapping** (ou les règles de mapping) peut être **par (caractéristique + entité source)** : ex. « level_creature pour entity monster » vs « level_object pour entity item ».

### 3.4 Formatters et conversion

- Le mapping ne suffit pas : il faut **comment** transformer la valeur (formatter : toInt, pickLang, dofusdb_level, etc.). Soit on garde une notion de **formatters** associés à la règle de mapping (comme aujourd’hui dans scrapping_entity_mappings.formatters), soit on déduit du type de caractéristique et de la formule de conversion. À trancher (probablement : règle de mapping = chemin ou id + formatters + characteristic_id).

### 3.5 Effets de sorts (effectId)

- Aujourd’hui : **effectId** DofusDB → sous-effet Krosmoz (slug) dans une constante PHP. Les **sous-effets** sont du côté Krosmoz (SubEffect, params avec characteristic, value_formula, etc.). Il faudra décider : soit on attache aussi un « mapping effectId → caractéristique / sous-effet » au service de caractéristiques (ou à une table dédiée liée aux caractéristiques spell), soit on garde un catalogue effectId séparé mais **configurable en BDD** (pas en dur en PHP).

### 3.6 Récap « ce qu’on pourrait oublier »

| Point | Pourquoi c’important |
|-------|----------------------|
| **Lien / caractéristique maître** | Si le mapping DofusDB diffère, pas de lien : chaque caractéristique a son propre mapping. Le lien reste utile pour les caractéristiques qui ne viennent pas de DofusDB. |
| **db_column / champ cible** | Le mapping doit aboutir à un champ (modèle + colonne). Soit on déduit via la caractéristique (db_column), soit on le stocke dans la règle de mapping. |
| **Mapping par (caractéristique + entité source)** | Même caractéristique Krosmoz peut avoir un chemin ou un id DofusDB différent selon qu’on importe un monster, un item ou un spell. |
| **Formatters** | Associer à chaque règle de mapping les formatters à appliquer (ou une stratégie pour les déduire). |
| **effectId (sorts)** | Intégrer le mapping effectId → sous-effet / caractéristique dans le modèle « mapping en BDD » ou au moins en BDD configurable. |
| **Profondeur (chemin dans un nœud)** | Pour effects[] (item ou spell-levels), le chemin peut être effectId + characteristic + value/diceNum/diceSide ; le modèle doit permettre de décrire « où » prendre la valeur dans le nœud. |

---

## 4. Besoins — Conversion

### 4.1 Formules et fonctions

- Les **formules** de conversion sont indiquées dans les **caractéristiques** (en BDD).
- On peut aussi indiquer une **fonction PHP** qui sera exécutée sur la donnée **après** application de la formule (s’il y en a une).
- Il peut **ne pas y avoir de formule ni de fonction** si on ne veut pas modifier la valeur (pass-through).
- Ordre : valeur brute → (optionnel) formule → (optionnel) fonction PHP → valeur finale.

### 4.2 Langue

- Les valeurs DofusDB multi-langues : on ne récupère que le **français (fr)**.

### 4.3 Items DofusDB ↔ Krosmoz (types et super_types)

- **Items** DofusDB correspondent aux **consommables**, **ressources** et **équipements** Krosmoz. C’est leur **type** et **super_type** qui les différencient.
- Il existe un **mapping** (ex. superTypeId → resource | consumable | equipment | excluded). On peut récupérer la **liste entière des types** via l’API DofusDB.
- **Décision :** enregistrer en **BDD** (voir § 8).

### 4.4 Conversion délicate — Items (bonus)

- On **ne peut pas prédire** quels seront les bonus d’un item : on a une **liste (id, valeur)** côté DofusDB (ex. `item.effects[]` avec `characteristic` = id et valeur).
- Logique attendue : pour chaque effet (id caractéristique DofusDB + valeur) → **récupérer la caractéristique** Krosmoz correspondante (via id DofusDB) → **trouver la bonne formule** de conversion pour cette caractéristique (groupe object / entité item) → **convertir la valeur**.
- Donc : résolution id DofusDB → caractéristique → formule (et éventuellement fonction PHP) → application sur la valeur.

### 4.5 Conversion délicate — Sorts (effets)

- Dans DofusDB les sorts sont composés de **spell-levels** : on obtient quelques propriétés intéressantes au niveau du level (apCost, minRange, range, etc.).
- Les propriétés sont **un cran plus bas** dans **effect[]** : il peut y avoir **plusieurs effets** par niveau. Chaque effet possède des propriétés qui nous intéressent (effectId, diceNum, diceSide, value, zoneDescr, etc.).
- **En KrosmozJDR** : certaines propriétés sont **uniques pour le sort** (ex. coût PA, portée min/max au niveau du sort), alors qu’en DofusDB ces mêmes notions existent **par spell-level** voire **par effet**. Les effets DofusDB renvoient vers une sorte de **sous-effet** (SubEffect dans Krosmoz) qui décrit **ce que fait** l’effet (action : frapper, soigner, etc.).
- **Complexité** : la **description** (type d’action, élément) est dans le sous-effet (mapping effectId → SubEffect), tandis que les **valeurs** (dégâts, soins, etc.) sont dans **effect-level.effects[]**. Il faut donc : 1) associer chaque instance d’effet à un sous-effet Krosmoz (effectId → SubEffect) ; 2) pour chaque sous-effet, savoir quelle **caractéristique** (ex. dommages feu) et quelle **formule** appliquer ; 3) lire les valeurs dans le bon chemin (value, diceNum, diceSide, etc.) et les convertir. La conversion est plus complexe car elle croise **structure DofusDB** (niveaux → effets[]) et **modèle Krosmoz** (sort avec propriétés globales + Effect → EffectSubEffect avec params par caractéristique).

---

## 5. Synthèse conversion

| # | Besoin |
|---|--------|
| C1 | **Formules** dans les caractéristiques ; **optionnel** : fonction PHP exécutée après la formule ; **optionnel** : pas de formule ni fonction (valeur inchangée). |
| C2 | **Langue** : ne récupérer que le **fr** pour les données multi-langues DofusDB. |
| C3 | **Items** = consommables / ressources / équipements ; mapping par **types** et **super_type** ; liste des types en **BDD** (voir § 8). |
| C4 | **Bonus items** : liste (id, valeur) non prédictible → pour chaque id : résolution caractéristique Krosmoz (via id DofusDB) → formule (et fonction) de conversion → convertir la valeur. |
| C5 | **Effets de sorts** : propriétés au niveau spell-level + propriétés dans effect[] ; en Krosmoz propriétés globales du sort + sous-effets (description de l’action) + valeurs par sous-effet ; conversion qui croise effectId → sous-effet, caractéristique + formule, et lecture des valeurs dans effect-level.effects[]. |

---

## 6. Points à ne pas oublier (conversion)

| Point | Pourquoi c’important |
|-------|----------------------|
| **Ordre formule puis fonction** | Définir clairement : formule (ex. niveau/10) puis fonction PHP (ex. clamp, notation dés). Éviter l’ambiguïté si les deux modifient la valeur. |
| **Fonction PHP enregistrée où ?** | La « fonction PHP » (ex. nom d’un callable, clé d’un registre) doit être enregistrée quelque part (config, BDD par nom, registry côté code). Si en BDD : stocker un identifiant de fonction, pas du code brut (sécurité). |
| **Valeurs plurielles (dés, min/max)** | Pour les effets de sort, une même « caractéristique » peut avoir diceNum + diceSide (dés) ou value ou min/max. La formule et la fonction doivent savoir quel(s) champ(s) lire et comment les combiner (ex. notation dés 2d6+3). |
| **Agrégation des bonus items** | Un item peut avoir plusieurs effets (plusieurs paires id+valeur). Après conversion de chaque valeur, faut-il les **agréger** (ex. somme par caractéristique) ou garder une structure par effet ? Aujourd’hui : agrégation (bonus[caractéristique] = somme). |
| **Spell-level vs effet** | En DofusDB une propriété peut être au niveau du spell-level (ex. apCost) ou dans chaque effect[]. En Krosmoz on peut vouloir une seule valeur au niveau du sort (ex. coût PA = celui du niveau 1 ou max). Règles d’agrégation ou de choix (premier niveau, niveau max, etc.) à clarifier. |
| **Types / super_types en BDD** | Si on met les types (item-types, super-types) en BDD : modèle de données (table(s)), synchronisation avec l’API (import périodique ou à la demande), impact sur les services qui filtrent aujourd’hui via les fichiers JSON. |

---

## 7. Décisions — Réponses aux questions ouvertes

Les réponses ci-dessous sont choisies pour rester cohérentes avec la source de vérité en BDD, l’existant (ConversionFunctionRegistry, DofusConversionService) et l’usage JDR.

| Question | Décision | Justification |
|----------|----------|---------------|
| **Types / super_types (item-types DofusDB) : BDD ou fichier ?** | **BDD** | Alignement avec la source de vérité en BDD pour tout ce qui pilote le mapping et la catégorisation. Permet à l’admin d’ajuster une catégorie (ex. un type passé de consumable à equipment), et de resynchroniser la liste depuis l’API à la demande ou périodiquement. Cohérent avec les seeders et registres existants. |
| **Ordre formule puis fonction** | **Confirmé : valeur brute → formule (si définie) → fonction PHP (si définie) → clamp** | Déjà en place dans DofusConversionService. La formule produit une valeur ; la fonction (conversion_function) peut la transformer (ex. notation dés, arrondi) ; le clamp final applique les limites de la caractéristique. |
| **Fonction PHP enregistrée où ?** | **BDD : identifiant (string). Code : registre (ConversionFunctionRegistry).** | Déjà en place : les tables characteristic_creature/object/spell ont un champ `conversion_function` (nullable string). L’UI admin propose une liste d’identifiants ; le registre côté code associe chaque id à un callable. Pas de code stocké en BDD (sécurité). |
| **Agrégation des bonus items** | **Garder la somme par caractéristique** | Comportement actuel : après conversion de chaque effet (id, valeur), on agrège par caractéristique Krosmoz (bonus[clé] = somme). Correspond à l’usage JDR (total des bonus d’équipement). Si besoin d’un détail par effet plus tard, on peut ajouter une structure optionnelle (ex. brut ou détail pour affichage) sans changer le résultat principal. |
| **Spell-level : quelle valeur remonter au niveau du sort ?** | **Règle configurable par propriété / mapping : premier niveau (niveau 1) par défaut.** | En DofusDB une propriété (apCost, minRange, range, etc.) existe par spell-level. En Krosmoz on veut une valeur au niveau du sort. **Par défaut** : prendre le **premier niveau** (level 1 / grade 1), comme aujourd’hui avec spell_global (levels[0]). Pour certaines propriétés on pourra prévoir d’autres règles (max, min, dernier niveau) **dans la règle de mapping ou la définition de la caractéristique** (ex. champ `spell_level_aggregation` : first | max | min | last). À implémenter côté mapping/conversion quand on refond. |

---

## 8. Quels mappings avons-nous besoin — Où les stocker

### 8.1 Liste des mappings nécessaires

| # | Type de mapping | Rôle | Exemple |
|---|-----------------|------|--------|
| **M1** | **Propriété plate (chemin → caractéristique → champ)** | Une propriété DofusDB repérée par un **chemin** (dot-notation) alimente une caractéristique Krosmoz, donc un champ (model + field). | `grades.0.level` → level_creature → creatures.level ; `spell_global.apCost` → pa_spell → spells.pa |
| **M2** | **Caractéristique par ID DofusDB** | Un **id** DofusDB (ex. `GET /characteristics`, ou `item.effects[].characteristic`) désigne une caractéristique Krosmoz. Permet de résoudre « id 15 » → intel_object sans liste séparée. | id 15 (intelligence) → intel_object ; id 1 (PA) → pa_object |
| **M3** | **Item typeId / superTypeId → catégorie Krosmoz** | Un **type** ou **super_type** DofusDB détermine si l’item est importé comme resource, consumable ou equipment (ou exclu). | superTypeId 9 → resource ; 6 ou 70 → consumable ; reste → equipment |
| **M4** | **effectId (sorts) → sous-effet Krosmoz** | Un **effectId** DofusDB (spell-level.effects[].effectId) correspond à un **SubEffect** Krosmoz (slug, et éventuellement caractéristique pour l’élément). | effectId 98 → sous-effet « frapper » + élément air ; 42 → « autre » |
| **M5** | **Formatters par règle** | Pour chaque règle de mapping (M1 ou M2), **quels formatters** appliquer (toInt, pickLang, dofusdb_level, clampToCharacteristic, etc.) et avec quels arguments. | règle level → [dofusdb_level, toString] ; règle name → [pickLang] |
| **M6** | **Agrégation spell-level (optionnel)** | Pour une propriété lue au niveau des spell-levels, **quelle valeur** remonter au sort (premier niveau, max, min, dernier). | apCost → first ; autre propriété → max |

---

### 8.2 Où stocker chaque mapping

| Mapping | Stockage | Détail |
|---------|----------|--------|
| **M1** (chemin → caractéristique → champ) | **Service Caractéristiques — BDD** | Règles liées à une **caractéristique** : « pour l’entité source X (monster, spell, item…), cette caractéristique se remplit depuis le chemin DofusDB Y ». La cible (model, field) se déduit de la caractéristique (db_column, groupe) ou est stockée dans la règle. **Table dédiée** (ex. `characteristic_dofusdb_mapping_rules` ou évolution de `scrapping_entity_mappings` avec characteristic_id systématique et lisible comme « règle pilotée par la caractéristique »). |
| **M2** (id DofusDB → caractéristique) | **Service Caractéristiques — BDD** | **Colonne** `dofusdb_characteristic_id` (nullable) sur la table de groupe concernée : `characteristic_creature`, `characteristic_object`, `characteristic_spell`. Une ligne (caractéristique + entité ou *) porte l’id DofusDB correspondant. Pour les bonus items, on lit item.effects[].characteristic = id → on cherche la ligne characteristic_object (ou *) dont dofusdb_characteristic_id = id → on obtient la caractéristique et sa formule. |
| **M3** (typeId / superTypeId → catégorie) | **BDD (existant)** | Les **races** et **types** (item_types, resource_types, monster_races) sont déjà en BDD et fonctionnent bien. **Ne pas modifier** ; s’appuyer sur les tables existantes pour la catégorie Krosmoz (resource / consumable / equipment). Si besoin de lier explicitement un typeId DofusDB à un enregistrement existant, ajouter une colonne dofusdb_id (ou équivalent) sur les tables concernées plutôt que dupliquer en tables « dofusdb_* ». |
| **M4** (effectId → SubEffect) | **BDD (catalogue effets)** | Table **dofusdb_effect_mapping** (ou équivalent) : effect_id (DofusDB), sub_effect_id (FK vers sub_effects) ou sub_effect_slug, characteristic_source (ex. element, none), sort_order. Peut vivre dans le module scrapping/dofusdb ou être référencée par le service de caractéristiques pour la conversion des valeurs d’effet. |
| **M5** (formatters) | **Service Caractéristiques — BDD** | Stockés **avec la règle** (M1) : colonne JSON ou table de détail (formatter_name, formatter_args) par règle. Aujourd’hui `scrapping_entity_mappings.formatters` ; si on fusionne les règles dans une table « characteristic_dofusdb_mapping_rules », y inclure formatters (JSON). |
| **M6** (agrégation spell-level) | **Service Caractéristiques — BDD** | Colonne **spell_level_aggregation** (nullable) sur la règle de mapping (M1) ou sur la définition characteristic_spell pour les propriétés lues au niveau spell-level : valeurs possibles first | max | min | last. Par défaut first. |

---

### 8.3 Synthèse « où »

| Lieu | Contenu |
|------|--------|
| **Service Caractéristiques (tables existantes)** | `characteristics`, `characteristic_creature`, `characteristic_object`, `characteristic_spell` : ajout de **dofusdb_characteristic_id** (nullable) sur les tables de groupe pour M2. |
| **Service Caractéristiques (table(s) de règles)** | Une table de **règles de mapping** (M1 + M5 + M6) : source (ex. dofusdb), entity_source (monster, spell, item, …), characteristic_id (FK), from_path (nullable), formatters (JSON), spell_level_aggregation (nullable), sort_order. Les cibles (model, field) peuvent être déduites de la caractéristique (db_column + groupe) ou ajoutées en colonnes. Soit **évolution de scrapping_entity_mappings** (en s’assurant que characteristic_id est le pivot), soit **nouvelle table** characteristic_dofusdb_mapping_rules et migration progressive. |
| **Catalogue DofusDB** | M3 : **tables existantes** (item_types, resource_types, monster_races) — pas de nouvelles tables dofusdb_item_types si la catégorie est déjà portée par l’existant. M4 : **dofusdb_effect_mapping** (ou équivalent) pour effectId → sous-effet. |

Ainsi, **tous les mappings sont en BDD** : caractéristiques + règles (M1, M2, M5, M6) dans le service de caractéristiques ; M3 s’appuie sur les tables types/races existantes ; M4 dans une table dédiée (effets).

---

## 9. Bilan — A-t-on fait le tour des besoins (par rapport à l’existant) ?

### 9.1 Ce qui est couvert

| Domaine | Besoins documentés |
|---------|--------------------|
| **Caractéristiques** | Source de vérité BDD, différenciation spell / créature / object, contenu (général, conversion, mapping), lien maître/liée (si mapping différent → pas de lien). |
| **Mapping** | M1–M6 listés (chemin → caractéristique, id DofusDB → caractéristique, types/super_types → catégorie item, effectId → sous-effet, formatters, agrégation spell-level). Stockage : service Caractéristiques (règles + dofusdb_characteristic_id) + catalogues BDD (item types, effect mapping). |
| **Conversion** | Formules + optionnel fonction PHP (id en BDD, registre en code), langue fr, bonus items (id → caractéristique → formule), effets de sorts (complexité décrite), agrégation somme, spell-level first par défaut. Décisions prises (types en BDD, ordre formule→fonction, etc.). |

### 9.2 Ce qui reste à préciser ou à ajouter

| Sujet | Existant | Besoin à formuler (recommandation) |
|-------|----------|------------------------------------|
| **Collecte** | Endpoints, filtres, pagination dans JSON (entities/*.json). Spell : fetch levels à part ; item : recette à part ; panoply : filtre cosmétique. | **Décision** : garder endpoints et filtres en **JSON** (hors mapping) pour l’instant, ou prévoir une évolution « config collecte en BDD » plus tard. Pas bloquant pour la refonte mapping/caractéristiques. |
| **Fallback mapping** | Si BDD (mapping) vide → fallback sur le mapping du JSON d’entité. Utilisé en tests et première install. | **Besoins** : **conserver le fallback** (mapping BDD prioritaire, JSON en secours) pour ne pas casser les tests ni l’installation vierge. |
| **Validation** | Clamp puis validate sur champs plats (définition par caractéristique). | **Besoins** : **La validation se fait déjà grâce aux caractéristiques** (CharacteristicLimitService + définitions BDD). Rien à changer ; confirmer que la refonte mapping/conversion s’appuie sur ce mécanisme. |
| **Intégration** | dry_run, replace_mode, relations, download_images, etc. | **Besoins** : **garder le comportement actuel** (pas de besoin nouveau listé). À documenter si on change le pipeline. |
| **Relations** | Config relations (spells, drops) dans le JSON ; RelationResolutionService (pile ou inline). | **Besoins** : **garder le comportement actuel** ; la config relations peut rester en JSON tant qu’on ne décide pas de tout piloter en BDD. |
| **Panoply** | Mapping actuellement **uniquement en JSON** (pas de règles en BDD dans le seeder). | **Besoins** : **Panoply s’inscrit avec item** : fonctionnement proche (bonus via effects). Étendre les règles de mapping BDD à panoply comme pour item (même logique, bonus). |
| **Normalisation** | SpellGlobalNormalizer : produit spell_global à partir du sort + levels[0] pour exposer des chemins plats. | **Besoins** : **garder une phase de normalisation** (ou équivalent) pour les entités dont la structure DofusDB ne correspond pas directement au mapping (spell → spell_global). À intégrer dans le plan de refonte. |
| **Races et types** | monster_race, resource_type, item_types : déjà **en base de données** et fonctionnent bien. | **Besoins** : **ne pas modifier** l’existant. Les races et les types sont déjà en BDD ; rien à migrer pour M3 (item types) si la catégorisation resource/consumable/equipment est déjà pilotée par ces tables. Documenter que l’on s’appuie sur l’existant. |
| **Images** | storeScrappedImage, download_images dans les options d’intégration. | **Besoins** : **garder le comportement actuel** sauf besoin spécifique (ex. désactiver par défaut, stockage externe). |
| **Recettes** | recipeToResourceRecipe (structure DofusDB → recipe_ingredients). Pivot **resource_recipe** (resource_id, ingredient_resource_id, quantity) : aujourd’hui uniquement ressource → ressources. | **Besoins** : **Les recettes sont des relations** entre des équipements, ressources ou consommables (résultat de la recette) **et** des ressources (ingrédients). **Il faut une table pivot** (ou une par type de résultat) pour lier entité résultante ↔ ingrédients (resource_id) + quantité. Aujourd’hui : resource_recipe ne couvre que ressource → ressources. À **étendre** si les recettes DofusDB concernent aussi items et consommables (pivot générique ou tables item_recipe, consumable_recipe). |
| **UI** | Admin caractéristiques (3 panneaux), admin scrapping mappings, page preview/batch. | **Besoins** : **Il faudra mettre le paquet sur l’UI.** Priorité dans le plan de refonte : interface admin (caractéristiques + mapping, catalogues), page scrapping (preview, batch). S’appuyer sur SPEC_UI_SCRAPPING.md et la vision 3 panneaux (ARCHITECTURE_SCRAPPING_MAPPING_CARACTERISTIQUES). |

### 9.3 Synthèse

- **Oui, on a fait le tour des besoins pour les caractéristiques, le mapping et la conversion** (cœur de la refonte).
- **Mises à jour** :
  - **Panoply** : s’inscrit avec **item** (même logique, bonus) ; règles de mapping en BDD comme pour item.
  - **Races et types** : déjà **en BDD** et fonctionnent bien → **ne pas modifier** ; s’appuyer sur l’existant (monster_races, resource_types, item_types). Pour M3 (typeId/superTypeId → catégorie), utiliser ces tables si elles portent déjà la catégorie Krosmoz.
  - **Validation** : se fait **grâce aux caractéristiques** ; rien à changer.
  - **UI** : **mettre le paquet sur l’UI** (priorité du plan).
  - **Recettes** : **relations** (équipement / ressource / consommable) ↔ **ressources** (ingrédients) ; **table pivot** nécessaire. Aujourd’hui resource_recipe (ressource → ressources) ; étendre si recettes pour items/consommables (pivot générique ou tables dédiées).
- **Suite recommandée** : acter le fallback (conserver), normalisation (garder la phase). Traiter **recettes** (schéma pivot étendu) et **UI** (priorité) dans le **plan d’amélioration**.

---

## 10. Suite

- Ce document sera complété au fur et à mesure (décisions collecte/catalogues, besoins UI si besoin).
- La liste des besoins pour les caractéristiques, le mapping et la conversion servira à définir le **modèle de données** (tables, colonnes) et l’**API du service de caractéristiques** pour exposer le mapping et les formules au pipeline de scrapping.
- **Types / races** : s’appuyer sur les tables existantes (item_types, resource_types, monster_races) ; pas de duplication en tables dofusdb_* si l’existant porte déjà la catégorie. **Recettes** : prévoir l’extension du schéma pivot (relation résultat de recette ↔ ingrédients ressources) si recettes pour items/consommables.
