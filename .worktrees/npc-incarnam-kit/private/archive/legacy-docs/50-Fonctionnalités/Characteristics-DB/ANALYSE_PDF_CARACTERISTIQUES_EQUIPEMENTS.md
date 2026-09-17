# Analyse des PDF Caractéristiques et Équipements / Forgemagie

Ce document compare le contenu des documents **Caractéristiques.pdf** et **Equipements et forgemagie.pdf** (règles Krosmoz JDR α 0.1.3.0) avec l’architecture du service de caractéristiques documentée dans ce dossier. Objectif : vérifier que l’architecture proposée permet de décrire l’ensemble des caractéristiques et des règles liées aux équipements et à la forgemagie.

**Sources :** `docs/110- To Do/Caractéristiques.pdf`, `docs/110- To Do/Equipements et forgemagie.pdf`.

---

## 1. Synthèse des deux PDF

### 1.1 Caractéristiques.pdf (côté créature / personnage)

Tableau des caractéristiques pour le **personnage** (classe, joueur) avec pour chaque caractéristique :

| Colonne PDF | Contenu typique |
|--------------|-----------------|
| **Caractéristique** | Nom (PA, PM, Ini, Vitalité, Modificateur de Vitalité, Compétences, CA, Résistances, PV, etc.) |
| **Valeur de base** | Formule (`1d20 + mod. intel`, `[(Vitalité - 10) / 2]`, `[niveau]`, `10 + mod. vitalité`) ou entier (6, 3, 8, 28) |
| **Valeur limite** | Bornes min/max (ex. 1–20, 0–6) ou formules de max |
| **Modification des équipements** | ✓ ou – (la caractéristique peut ou non être modifiée par l’équipement) |
| **Forgemagie** | ✓ ou – (la caractéristique peut ou non recevoir un bonus forgemagie) |
| **Valeur max** (forgemagie comprise) | Plafond du bonus équipement (ex. +6, +5) |
| **Valeur max** (+N) | Plafond forgemagie et incrément par rune (ex. +1, +2, +3) |

On y trouve notamment : PA, PM, Ini, Portée, Nombre d’invocation, Vitalité et modificateurs (Vitalité, Sagesse, Force, Intelligence, Chance, Agilité), bonus sauvegarde, bonus touche au sort, CA, Esquive PA/PM, Tacle, Fuite, dégâts fixes (neutre, terre, feu, air, eau, multiples), résistances fixes et %, PV, dés de vie, Réserve de Wakfu, bonus de maîtrise, compétences (Acrobaties, Athlétisme, etc.), compétences passives.

### 1.2 Equipements et forgemagie.pdf (côté objet / équipement)

- **Organisation par slot** : ARMES, CHAPEAUX, CAPES, AMULETTES, BOTTES, ANNEAUX, CEINTURES, BOUCLIERS.
- Pour chaque slot, **quelles caractéristiques** l’équipement peut donner.
- Pour chaque couple (slot, caractéristique), tableau :

| Colonne PDF | Contenu |
|-------------|---------|
| **BONUS EN FONCTION DU NIVEAU** | Colonnes 1–2, 3–4, …, 19–20 : bonus (entier) selon la tranche de niveau de l’objet → **table par caractéristique (level)** |
| **Prix par unité** | Prix en kamas par unité de bonus (pour calcul du prix de l’équipement) |
| **FORGEMAGIE – Bonus max** | Entier : maximum de bonus ajoutable par forgemagie pour cette caractéristique sur ce type d’équipement |
| **FORGEMAGIE – Prix de la rune par unité** | Prix d’une rune (par unité) pour cette caractéristique. Souvent « – » si forgemagie non autorisée. |

Exemples : PA (amulette) 1 300 / 1 / 2 600 ; Points de vie max (chapeau) 50 / 20 / 100 ; Résistance (bouclier) 2 500 / – / – ; Invulnérabilité 5 000 / 100 %.

---

## 2. Correspondance avec l’architecture

### 2.1 Groupes (object, creature, spell)

- **Caractéristiques.pdf** → définitions **creature** (personnage / classe) : formules de base, limites, indication « modifiable par équipement » et « forgemagie ».
- **Equipements et forgemagie.pdf** → définitions **object** (équipements par slot) : bonus par niveau, prix par unité, forgemagie max, prix rune.
- Le groupe **spell** n’est pas couvert par ces deux PDF ; l’architecture prévoit déjà le groupe spell.

**Verdict :** Les trois groupes (object, creature, spell) conviennent et couvrent bien le périmètre des deux PDF.

---

### 2.2 Contenu des valeurs : fixe, formule, table

- **Valeur de base** (PDF Caractéristiques) = formule (`1d20+mod`, `[niveau]`, `10+mod.vitalité`) ou **valeur fixe** (6, 3, 8…) → correspond à **formula** / **default_value** (fixe ou formule).
- **Valeur limite** = min/max (fixes ou dérivées) → **min** / **max** (fixe, formule ou table).
- **Bonus en fonction du niveau** (PDF Equipements) = pour chaque tranche de niveau (1–2, 3–4, …), une valeur de bonus → c’est exactement une **table par caractéristique** avec `characteristic: "level"` et des seuils 1, 3, 5, 7, … et des valeurs associées.

**Verdict :** Le modèle « valeur fixe / formule / table par caractéristique » (stockée en JSON) correspond bien aux deux PDF. La table par niveau d’équipement est un cas d’usage direct de la table par caractéristique (niveau = level).

---

### 2.3 Propriétés spécifiques au groupe object

Le PDF Equipements expose explicitement :

- **Prix par unité** → déjà documenté (prix par unité, ex. `base_price_per_unit`).
- **Bonus max** (forgemagie) → **forgemagie_max** (entier).
- **Prix de la rune par unité** → **prix par rune** (ex. `rune_price_per_unit`).
- Certaines lignes ont « – » pour forgemagie → besoin d’un indicateur **forgemagie autorisée** (booléen) par caractéristique (et éventuellement par slot).

**Verdict :** L’architecture (prix par unité, forgemagie_max, prix par rune, et forgemagie autorisée) convient au PDF Equipements et forgemagie.

---

### 2.4 Types d’équipement (item_types)

Le PDF est structuré par **type d’équipement** (arme, chapeau, cape, etc.). Une caractéristique du groupe **object** peut être **réservée à certains types d’équipement** : la table pivot `characteristic_object_item_type` associe chaque définition (`characteristic_object`) aux **id** de la table **item_types**. Si une définition n’a aucune entrée dans cette pivot, la caractéristique s’applique à tous les types ; sinon elle ne s’applique qu’aux types listés. Le getter expose cette liste sous la clé **allowed_item_type_ids** (voir [TYPES_VALEURS_ET_CONTENU_JSON.md](./TYPES_VALEURS_ET_CONTENU_JSON.md) § 5.3).

**Verdict :** La restriction par type (item_types) est compatible avec le PDF. Les « bonus en fonction du niveau » sont portés par la définition de la caractéristique (object) ou par une table par niveau.

---

### 2.5 Côté créature : modifiable par équipement / forgemagie

Le PDF Caractéristiques indique pour chaque caractéristique **si** elle peut être modifiée par l’équipement et **si** elle peut recevoir de la forgemagie. Ce sont des **métadonnées côté créature** (ou des règles globales par caractéristique) :

- **Modification des équipements** : oui/non.
- **Forgemagie** : oui/non.
- **Valeur max** (équipement) et **Valeur max (+N)** (forgemagie) : plafonds et pas d’incrément.

Dans l’architecture actuelle, les limites (min/max) sont déjà prévues pour la créature. Les champs « modifiable par équipement » et « forgemagie autorisée » peuvent soit être déduits (si au moins un objet/slot propose cette caractéristique avec forgemagie), soit être stockés explicitement dans la définition **creature** pour la caractéristique (booléens ou champs dédiés). Ce n’est pas en contradiction avec l’architecture : au pire, ajout de deux propriétés optionnelles côté creature.

**Verdict :** L’architecture peut couvrir ces informations (éventuellement en ajoutant des champs « modifiable_par_equipement » / « forgemagie_autorisee » et plafonds côté creature si besoin).

---

### 2.6 Types de valeurs (boolean, list, string) et cas numériques

Les deux PDF décrivent surtout des caractéristiques **numériques** (entiers ou pourcentages) : PA, PM, bonus, résistances %, PV, compétences (jet 1d20 + mod + maîtrise), etc. Les « valeurs de base » et « bonus » sont des formules ou des entiers ; les résistances peuvent être 0 %, 50 %, 100 %.

Dans la doc actuelle des types de valeurs, on a **boolean**, **list**, **string**. Les caractéristiques numériques (formule + min/max, éventuellement table par niveau) correspondent en pratique à un type **int** ou **numeric** déjà utilisé en base (`type`, `min`, `max`, `formula`, `default_value`). Pour aligner la documentation avec les PDF et le code, il est utile d’**expliciter un type int (ou numeric)** avec : valeur calculée (formule/table), valeur par défaut, min, max, helper, calcul affiché (et éventuellement unité comme « % » pour les résistances).

**Verdict :** L’architecture supporte bien les cas des PDF. Recommandation : **documenter explicitement le type int/numeric** (propriétés : formule/default, min, max, helper, calcul affiché) dans [TYPES_VALEURS_ET_CONTENU_JSON.md](./TYPES_VALEURS_ET_CONTENU_JSON.md) pour couvrir PA, PM, PV, résistances, compétences, etc.

---

### 2.7 Cas particuliers repérés

- **Résistances %** : 0 %, 50 %, 100 % (et forgemagie 100 % pour Invulnérabilité) → type **list** (valeurs possibles 0, 50, 100) ou **int** avec min/max 0–100 et valeurs affichées en %. Pas de conflit avec l’architecture.
- **Compétences** : formule avec variantes Maîtrise / Expert (bonus de maîtrise × 1 ou × 2) → déjà prévu dans la syntaxe des formules (variable contextuelle type `[competence_mastery]` ou équivalent). OK.
- **Invulnérabilité** : une seule valeur (100 %) avec prix rune → une caractéristique booléenne ou liste à une valeur, avec prix par unité / prix rune côté object. OK.
- **Recharge de la réserve de Wakfu** (ceinture) : bonus par niveau, pas de forgemagie dans le tableau → table par niveau + forgemagie non autorisée. OK.

---

## 3. Conclusion

| Aspect | Adéquation | Remarque |
|--------|------------|-----------|
| Groupes object / creature / spell | Oui | Les deux PDF correspondent aux groupes object et creature. |
| Valeur fixe / formule / table par caractéristique | Oui | Base et limites (creature) et bonus par niveau (object) sont représentables. |
| Prix par unité, forgemagie_max, prix par rune | Oui | Présents dans le PDF Equipements et dans l’architecture. |
| Forgemagie autorisée ou non | Oui | Couvert par un booléen (forgemagie_allowed ou équivalent). |
| Restriction par type d’équipement (item_types) | Oui | Structure du PDF Equipements couverte par characteristic_object_item_type. |
| Côté creature : modifiable par équipement / forgemagie | Oui (optionnel) | Possible en dérivant ou en ajoutant 1–2 champs sur la définition creature. |
| Type int/numeric | À documenter | Beaucoup de caractéristiques des PDF sont numériques ; ajouter le type **int** (ou numeric) dans la doc des types de valeurs. |

En résumé, **l’architecture proposée convient à la description de l’ensemble des caractéristiques** présentes dans les deux PDF. Les seuls ajustements recommandés sont :

1. **Documenter explicitement le type int (ou numeric)** dans le document des types de valeurs (propriétés : formule / défaut, min, max, helper, calcul affiché).
2. **Prévoir ou documenter** les métadonnées creature « modifiable par équipement » et « forgemagie autorisée » (et éventuellement plafonds équipement/forgemagie) si vous souhaitez les gérer explicitement en base plutôt que par convention.

Une fois ces points intégrés dans [TYPES_VALEURS_ET_CONTENU_JSON.md](./TYPES_VALEURS_ET_CONTENU_JSON.md) (et éventuellement dans la présentation du service), l’architecture couvre de façon cohérente le contenu des documents *Caractéristiques* et *Equipements et forgemagie*.
