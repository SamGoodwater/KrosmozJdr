# Propriétés Items / Resources / Consumables — Comprendre la conversion

Ce document explique **pourquoi** tu voyais trois blocs de propriétés (`consumables.*`, `items.*`, `resources.*`) et ce que signifie chaque propriété. Depuis la **conversion ciblée** (voir ci‑dessous), un seul bloc est produit et affiché selon le type d’item (resource / consommable / équipement).

---

## 1. Pourquoi (jusqu’à) trois blocs (consumables, items, resources) ?

L’API DofusDB expose **un seul** type d’entité : **`/items`**. Un même objet (ex. un parchemin d’Intelligence) peut être, côté jeu, une **ressource**, un **consommable** ou un **équipement**. Dans KrosmozJDR, on a **trois tables** : `resources`, `consumables`, `items`. Le mapping (`item.json`) est unique et peut écrire vers les trois modèles.

### Conversion ciblée (efficacité)

Pour **ne convertir et n’afficher que ce qui sert** :

- Avant la conversion, le **typeId** brut (DofusDB) est résolu vers la table cible : `resources`, `consumables` ou `items` (registres `resource_types`, `consumable_types`, `item_types`).
- Le **contexte** de conversion reçoit `targetModel` = cette table.
- Seules les règles du mapping qui ciblent ce modèle sont appliquées : **un seul bloc** est rempli (ex. `consumables` pour un parchemin).
- **Effets** : moins de formatters exécutés, moins de données en mémoire, et **affichage comparaison** limité aux propriétés utiles (un seul préfixe : `consumables.*`, `resources.*` ou `items.*`).

La liste des propriétés affichées reste définie par le **mapping** (clés `key`) ; la config expose déjà ces clés via `comparisonKeys` (API config). On n’affiche que les chemins présents dans le bloc converti.

Le **« — »** dans le tableau signifie : **pas de valeur** (vide, null, ou non utilisé).

---

## 2. Propriétés communes (consumables, items, resources)

Ces champs sont mappés **vers les trois** modèles. Source DofusDB → même valeur dans les trois blocs.

| Propriété (préfixe) | DofusDB (brut) | Rôle | Exemple |
|---------------------|----------------|------|--------|
| **description** | `description` (multi-langue) | Texte descriptif (FR par défaut) | « Ce parchemin permet de gagner… » |
| **dofusdb_id** | `id` | ID unique DofusDB (lien avec l’API) | 815 |
| **image** | `img` | URL ou chemin de l’image (après formatter) | https://api.dofusdb.fr/img/items/76035.png |
| **level** | `level` | Niveau de l’objet | 1 |
| **name** | `name` (multi-langue) | Nom (objet avec clés de langue : fr, en, etc.) | `{ 6 clé(s) }` = 6 langues, valeur FR = « Parchemin d'Intelligence » |
| **price** | `price` | Prix en kamas | 2500 |
| **rarity** | `rarity` | Rareté (0, 1, …) ; peut être dérivée du level si absent | 0 |

- **name** en `{ 6 clé(s) }` : c’est un **objet** `{ fr: "...", en: "...", ... }`. L’UI affiche souvent la clé `fr` pour l’affichage.
- **level** / **price** : en BDD Krosmoz c’est souvent stocké en string pour souplesse.

---

## 3. Propriétés spécifiques Consumables

En plus des champs communs ci‑dessus, le modèle **Consumable** a :

- **effect** : issu du formatter qui transforme les `effects` DofusDB en format Krosmoz (ex. bonus Intellect).  
  Dans ton exemple, tu le vois plutôt sous **items.effect** (`{"intel":0}`) car le mapping écrit `effect` dans **items** ; pour un **consommable** pur, le même genre de conversion peut alimenter un champ `effect` côté consumables si le mapping l’y envoie (selon la config actuelle, `effect` est mappé vers `items` ; consumables peut avoir un champ `effect` en BDD utilisé ailleurs).

En base, **Consumable** a notamment : `name`, `description`, `effect`, `level`, `recipe`, `price`, `rarity`, `image`, `dofusdb_id`, `consumable_type_id`, etc. Ce qui apparaît dans la comparaison sous **consumables.*** est exactement ce que le mapping écrit dans ce bloc (donc les champs communs listés au §2, plus tout ce qui est mappé explicitement vers `consumables` dans `item.json`).

---

## 4. Propriétés spécifiques Items (équipements)

| Propriété | DofusDB | Rôle |
|-----------|---------|------|
| **items.bonus** | `effects` | JSON **brut** des effets (pour debug / référence) | `[{"from":1,"to":0,"characteristic":15,...}]` |
| **items.effect** | `effects` | Effets convertis en format Krosmoz (ex. bonus lisibles) | `{"intel":0}` |

- **bonus** : tableau/objet JSON tel que renvoyé par DofusDB (structure des bonus).
- **effect** : même source, mais passée par le formatter **itemEffectsToKrosmozBonus** pour produire un objet type `{ intel, str, ... }`.

---

## 5. Propriétés spécifiques Resources

| Propriété | DofusDB | Rôle |
|-----------|---------|------|
| **resources.resource_type_id** | `typeId` | ID du type de ressource **KrosmozJDR** (résolu via `resolveResourceTypeId`) | 118 |
| **resources.recipe_ingredients** | `recipe` | Ingrédients de recette (convertis en liste resource + quantité) | `[0 élément(s)]` = recette vide |
| **resources.weight** | `realWeight` | Poids de la ressource | 1 |

- **recipe_ingredients** : pour les ressources qui sont des recettes ; ici « 0 élément(s) » = pas d’ingrédients (parchemin simple).
- **resource_type_id** : le `typeId` DofusDB (ex. 118) est résolu vers l’ID de la table `resource_types` en BDD.

---

## 6. Autres lignes de ton tableau

- **level** / **price** sans préfixe : ce sont les **mêmes** valeurs que dans les blocs (souvent affichées une fois à la racine pour résumer).
- **type.name** : nom du **type** d’item (ex. type “Parchemin”) ; peut venir d’une relation ou d’un sous-objet `type` dans les données converties.
- **typeId** (sans valeur affichée) : c’est le **typeId** DofusDB de l’item (ex. type consommable/ressource). Il sert à décider dans quelle table intégrer (resources / consumables / items) et à résoudre **resource_type_id** ou **consumable_type_id**.

---

## 7. Résumé Consumables

Pour un **consommable** (ex. Parchemin d’Intelligence) :

1. **consumables.*** = bloc utilisé pour l’intégration en table `consumables` (si le typeId correspond à un consommable).
2. Les propriétés **consumables.description**, **dofusdb_id**, **image**, **level**, **name**, **price**, **rarity** sont les **champs communs** avec items/resources, issus du même brut DofusDB.
3. Le **« — »** en colonne « existant » signifie en général : pas encore en base ou champ non rempli côté Krosmoz.
4. **items.bonus** / **items.effect** décrivent les **effets** (bonus) ; pour un consommable, l’info “effet” peut aussi être stockée dans le champ **effect** du modèle Consumable selon le mapping.

Pour aller plus loin sur le détail du mapping (formatters, chemins exacts) : **resources/scrapping/config/sources/dofusdb/entities/item.json** et **MAPPING_DOFUSDB_TO_KROSMOZJDR.md**.
